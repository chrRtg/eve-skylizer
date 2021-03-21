<?php
namespace User\Service;

use User\Entity\Permission;
use User\Entity\Role;
use User\Entity\User;
use Laminas\Permissions\Rbac\Rbac;

/**
 * This service is responsible for initialzing RBAC (Role-Based Access Control).
 */
class RbacManager
{
    /**
     * Doctrine entity manager.
     *
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * RBAC service.
     *
     * @var Laminas\Permissions\Rbac\Rbac
     */
    private $rbac;

    /**
     * Filesystem cache.
     *
     * @var Laminas\Cache\Storage\StorageInterface
     */
    private $cache;

    /**
     * Assertion managers.
     *
     * @var array
     */
    private $assertionManagers = [];

    /**
     * Constructs the service.
     */
    public function __construct($entityManager, $authService, $cache, $assertionManagers)
    {
        $this->entityManager = $entityManager;
        $this->authService = $authService;
        $this->cache = $cache;
        $this->assertionManagers = $assertionManagers;
    }

    /**
     * Initializes the RBAC container.
     */
    public function init($forceCreate = false)
    {
        if ($this->rbac != null && !$forceCreate) {
            // Already initialized; do nothing.
            return;
        }

        // If user wants us to reinit RBAC container, clear cache now.
        if ($forceCreate) {
            $this->cache->removeItem('rbac_container');
        }

        // Try to load Rbac container from cache.
        $result = false;
        $this->rbac = $this->cache->getItem('rbac_container', $result);
        if (!$result) {
            // Create Rbac container.
            $this->rbac = new Rbac();

            // Construct role hierarchy by loading roles and permissions from database.

            $this->rbac->setCreateMissingRoles(true);

            $roles = $this->entityManager->getRepository(Role::class)
                ->findBy([], ['id' => 'ASC']);
            foreach ($roles as $role) {
                $roleName = $role->getName();

                $parentRoleNames = [];
                foreach ($role->getParentRoles() as $parentRole) {
                    $parentRoleNames[] = $parentRole->getName();
                }

                $this->rbac->addRole($roleName, $parentRoleNames);

                foreach ($role->getPermissions() as $permission) {
                    $this->rbac->getRole($roleName)->addPermission($permission->getName());
                }
            }

            // Save Rbac container to cache.
            $this->cache->setItem('rbac_container', $this->rbac);
        }
    }

    /**
     * Checks whether the given user has permission.
     *
     * @param User|null  $user
     * @param string     $permission
     * @param array|null $params
     */
    public function isGranted($user, $permission, $params = null)
    {
        if ($this->rbac == null) {
            $this->init();
        }

        if ($user == null) {

            $identity = $this->authService->getIdentity();
            if ($identity == null) {
                return false;
            }

            $user = $this->entityManager->getRepository(User::class)
                ->findOneByEveUsername($identity);
            if ($user == null) {
                // Oops.. the identity presents in session, but there is no such user in database.
                // We throw an exception, because this is a possible security problem.
                throw new \Exception('There is no user with such identity');
            }
        }

        $roles = $user->getRoles();

        foreach ($roles as $role) {
            if ($this->rbac->isGranted($role->getName(), $permission)) {

                if ($params == null) {
                    return true;
                }

                foreach ($this->assertionManagers as $assertionManager) {
                    if ($assertionManager->assert($this->rbac, $permission, $params)) {
                        return true;
                    }
                }

                return false;
            }
        }

        return false;
    }
}
