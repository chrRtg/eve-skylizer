<?php
namespace User\Service;

use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Result;
use User\Entity\User;

/**
 * Adapter used for authenticating user. It takes login and password on input
 * and checks the database if there is a user with such login (eve_id) and password.
 * If such user exists, the service returns its identity (eve_id). The identity
 * is saved to session and can be retrieved later with Identity view helper provided
 * by ZF3.
 */
class AuthAdapter implements AdapterInterface
{
    /**
     * User eve_id.
     * @var string 
     */
    private $eve_id;
    
    /**
     * Password
     * @var string 
     */
    private $eve_name;
    
    /**
     * Entity manager.
     * @var Doctrine\ORM\EntityManager 
     */
    private $entityManager;
        
    /**
     * Constructor.
     */
    public function __construct($entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    /**
     * Sets user eve_id.     
     */
    public function setEveId($eve_id) 
    {
        $this->eve_id = $eve_id;
    }
    
    /**
     * Sets password.     
     */
    public function setEveName($eve_name) 
    {
        $this->eve_name = (string)$eve_name;        
    }
    
    /**
     * Performs an authentication attempt.
     */
    public function authenticate()
    {                
        // Check the database if there is a user with such eve_id.
        $user = $this->entityManager->getRepository(User::class)
                ->findOneByEveUserid($this->eve_id);
        
        // If there is no such user, return 'Identity Not Found' status.
        if ($user == null) {
            return new Result(
                Result::FAILURE_IDENTITY_NOT_FOUND, 
                null, 
                ['Invalid credentials.']);        
        }   
        
        // If the user with such eve_id exists, we need to check if it is active or retired.
        // Do not allow retired users to log in.
        if ($user->getStatus()==User::STATUS_RETIRED) {
            return new Result(
                Result::FAILURE, 
                null, 
                ['User is retired.']);        
        }
        
		return new Result(
			Result::SUCCESS, 
			$this->eve_name, 
			['Authenticated successfully.']);        
    }
}


