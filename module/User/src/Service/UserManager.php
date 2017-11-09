<?php

namespace User\Service;

use User\Entity\User;
use User\Entity\Role;
use Application\Entity\EveCorporation;

/**
 * This service is responsible for adding/editing users
 * and changing user password.
 */
class UserManager {

	/**
	 * Doctrine entity manager.
	 * @var Doctrine\ORM\EntityManager
	 */
	private $entityManager;

	/**
	 * Role manager.
	 * @var User\Service\RoleManager
	 */
	private $roleManager;

	/**
	 * Permission manager.
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
	 * @param int $eve_char_id
	 * @param Object $eve_char
	 * @param Object $eve_corporation
	 * @param boolean $force_admin
	 * @return Object User
	 */
	public function getOrAddUser($eve_char_id, $eve_char, $eve_corporation, $force_admin = false)
	{
		$user = $this->entityManager->getRepository(User::class)->findOneByEveUserid($eve_char_id);

		if (!$user) {
			// Create new User entity.
			$user = new User();
			$user->setEveCorpid($this->getOrAddCorporation($eve_char->corporation_id, $eve_corporation->corporation_name, $eve_corporation->ticker));
			$user->setEveUserid($eve_char_id);
			$user->setEveUsername($eve_char['name']);

			$user->setStatus(1);

			$user->setDateCreated(new \DateTime("now"));

			// Assign roles to user.
			if ($force_admin == true) {
				$this->assignRoles($user, ['1']); // Admin Role
			} else {
				$this->assignRoles($user, ['4']); // 4 == Standard User
			}

			// Add the entity to the entity manager.
			$this->entityManager->persist($user);

			// Apply changes to database.
			$this->entityManager->flush();
		}

		return $user;
	}
	
	/**
	 * Corporation Insert or Update
	 * @param int $corp_id
	 * @param string $corp_name
	 * @param string $corp_ticker
	 * @return Object EveCorporation
	 */
	private function getOrAddCorporation($corp_id, $corp_name, $corp_ticker = '')
	{
		$corp = $this->entityManager->getRepository(EveCorporation::class)->findOneByCorporationId($corp_id);
		if (!$corp) {
			$corp = new EveCorporation();
			$corp->setCorporationName($corp_name);
			$corp->setTicker($corp_ticker);
			$corp->setCorporationId($corp_id);

			// Add the entity to the entity manager.
			$this->entityManager->persist($corp);

			// Apply changes to database.
			$this->entityManager->flush();
		}
		return $corp;
	}
	
	/**
	 * This method updates the roles of an existing user.
	 * 
	 * @param Object $user
	 * @param Array $data
	 * @return boolean
	 */
	public function updateUser($user, $data)
	{
		// Assign roles to user.
		$this->assignRoles($user, $data['roles']);

		// Apply changes to database.
		$this->entityManager->flush();

		return true;
	}

	/**
	 * A helper method which assigns new roles to the user.
	 * 
	 * @param Object $user
	 * @param Array $roleIds
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

}
