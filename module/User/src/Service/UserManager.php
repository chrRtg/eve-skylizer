<?php

namespace User\Service;

use User\Entity\User;
use User\Entity\UserCli;
use User\Entity\Role;
use Application\Entity\EveCorporation;

/**
 * This service is responsible for adding/editing users
 * and changing user password.
 */
class UserManager
{

    /**
     * Doctrine entity manager.
     *
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * Role manager.
     *
     * @var User\Service\RoleManager
     */
    private $roleManager;

    /**
     * Permission manager.
     *
     * @var User\Service\PermissionManager
     */
    private $permissionManager;
    private $logger;

    /**
     * Constructs the service.
     */
    public function __construct($entityManager, $roleManager, $permissionManager, $logger)
    {
        $this->entityManager = $entityManager;
        $this->roleManager = $roleManager;
        $this->permissionManager = $permissionManager;
        $this->logger = $logger;
    }

    /**
     * User insert or update
     * 
     * @param  int     $eve_char_id
     * @param  Object  $eve_char
     * @param  Object  $eve_corporation
     * @param  boolean $force_admin
     * @return Object User
     */
    public function getOrAddUser($eve_char_id, $eve_char, $eve_corporation, $force_admin = false)
    {
        $user = $this->entityManager->getRepository(User::class)->findOneByEveUserid($eve_char_id);

        if (!$user) {
            // Create new User entity.
            $user = new User();
            $user->setStatus(1);
            $user->setDateCreated(new \DateTime("now"));
        }
        
        $user->setEveCorpid($this->getOrAddCorporation($eve_char->corporation_id, $eve_corporation->name, $eve_corporation->ticker, (isset($eve_corporation->alliance_id) ? $eve_corporation->alliance_id : 0)));
        $user->setEveUserid($eve_char_id);
        $user->setEveUsername($eve_char['name']);


        // Assign roles to user.
        if ($force_admin === true) {
            $this->assignRoles($user, ['1']); // Admin Role
        } else {
            $this->assignRoles($user, ['4']); // 4 == Standard User
        }

        // Add the entity to the entity manager.
        $this->entityManager->persist($user);

        // Apply changes to database.
        $this->entityManager->flush();

        return $user;
    }
    
    /**
     * Corporation Insert or Update
     *
     * @param  int    $corp_id
     * @param  string $corp_name
     * @param  string $corp_ticker
     * @return Object EveCorporation
     */
    private function getOrAddCorporation($corp_id, $corp_name, $corp_ticker = '', $alliance_id=0)
    {
        $corp = $this->entityManager->getRepository(EveCorporation::class)->findOneByCorporationId($corp_id);
        if (!$corp) {
            $corp = new EveCorporation();
        }
        // always update in cause corp has changed name or ticker
        $corp->setCorporationId($corp_id);
        $corp->setCorporationName($corp_name);
        $corp->setTicker($corp_ticker);
        $corp->setAllianceId($alliance_id);

        // Add the entity to the entity manager.
        $this->entityManager->persist($corp);

        // Apply changes to database.
        $this->entityManager->flush();

        return $corp;
    }
    
    /**
     * This method updates the roles of an existing user.
     * 
     * @param  Object $user
     * @param  Array  $data
     * @return boolean
     */
    public function updateUser($user, $data)
    {
        $user->setStatus($data['status']); 
        // Assign roles to user.
        $this->assignRoles($user, $data['roles']);

        // Apply changes to database.
        $this->entityManager->flush();

        return true;
    }

    /**
     * A helper method which assigns new roles to the user.
     * 
     * @param  Object $user
     * @param  Array  $roleIds
     * @throws \Exception
     */
    private function assignRoles($user, $roleIds)
    {
        // Remove old user role(s).
        $user->getRoles()->clear();

        // Assign new role(s).
        foreach ($roleIds as $roleId) {
            $role = $this->entityManager->getRepository(Role::class)
                ->find($roleId);
            if ($role == null) {
                throw new \Exception('Not found role by ID');
            }

            $user->addRole($role);
        }
    }

    /**
     * Create or update entry in table user_cli
     * The table is meant to provide data required for detached processes to fetch bigger ammounts of data from ESI
     *
     * @param int $eve_char_id
     * @param int $eve_corporation
     * @param object \Seat\Eseye\Containers\EsiAuthentication()
     * @param string $expire
     *
     * @return  object User_cli entry created or updated
     */
    public function setCliUser($eve_char_id, $eve_corporation, $authcontainer, $expire)
    {
        $usercli = $this->entityManager->getRepository(UserCli::class)->findOneByEveUserid($eve_char_id);
        if (!$usercli) {
            // Create new User entity.
            $usercli = new UserCli();
        }
        
        $usercli->setAuthContainer(serialize($authcontainer));
        $usercli->setEveCorpid($eve_corporation);
        $usercli->setEveUserid($eve_char_id);
        $usercli->setInUse(0);

        $date_expire = new \DateTime();
        $date_expire->setTimestamp($expire);
        $usercli->setEveTokenlifetime($date_expire);

        // Add the entity to the entity manager.
        $this->entityManager->persist($usercli);

        // Apply changes to database.
        $this->entityManager->flush();

        return $usercli;
    }

    /**
     * Set in_use=1 for $usercli
     *
     * @param Object UserCli
     * @return boolean true on success
     */
    public function setCliUserInUse($usercli) 
    {
        if (!$usercli) {
            return false;
        }

        $usercli->setInUse(1);
        $this->entityManager->persist($usercli);
        $this->entityManager->flush();

        return true;
    }

    /**
     * Return the next entry in UserCli with in_use = 0
     *
     * @return  Object   UserCli
     */
    public function getNextCliUser()
    {
        return $this->entityManager->getRepository(UserCli::class)->findOneBy(array('inUse' => 0));
    }
}
