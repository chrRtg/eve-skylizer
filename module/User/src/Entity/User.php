<?php

namespace User\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * User
 *
 * @ORM\Table(name="user", uniqueConstraints={@ORM\UniqueConstraint(name="eve_userid", columns={"eve_userid"})}, indexes={@ORM\Index(name="eve_corpid", columns={"eve_corpid"}), @ORM\Index(name="eve_username", columns={"eve_username"})})
 * @ORM\Entity
 */
class User
{
    // User status constants.
    const STATUS_ACTIVE       = 1; // Active user.
    const STATUS_RETIRED      = 2; // Retired user.
    /**
     * @var integer
     *
     * @ORM\Column(name="id",                   type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_created", type="datetime", nullable=false)
     */
    private $dateCreated;

    /**
     * @var integer
     *
     * @ORM\Column(name="eve_userid", type="integer", nullable=false)
     */
    private $eveUserid = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="eve_username", type="string", length=80, nullable=false)
     */
    private $eveUsername = '';

    /**
     * @ORM\ManyToMany(targetEntity="User\Entity\Role")
     * @ORM\JoinTable(name="user_role",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
     *      )
     */
    private $roles;
    
    /**
     * @var \Application\Entity\EveCorporation
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\EveCorporation", fetch="EAGER")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="eve_corpid",                               referencedColumnName="corporation_id")
     * })
     */
    private $eveCorpid;

    /**
     * Constructor.
     */
    public function __construct() 
    {
        $this->roles = new ArrayCollection();
    }    

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return User
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }
    /**
     * Returns possible statuses as array.
     *
     * @return array
     */
    public static function getStatusList() 
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_RETIRED => 'Retired'
        ];
    }    
    
    /**
     * Returns user status as string.
     *
     * @return string
     */
    public function getStatusAsString()
    {
        $list = self::getStatusList();
        if (isset($list[$this->status])) {
            return $list[$this->status];
        }
        
        return 'Unknown';
    }

    /**
     * Set dateCreated
     *
     * @param \DateTime $dateCreated
     *
     * @return User
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    /**
     * Get dateCreated
     *
     * @return \DateTime
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * Set eveUserid
     *
     * @param integer $eveUserid
     *
     * @return User
     */
    public function setEveUserid($eveUserid)
    {
        $this->eveUserid = $eveUserid;

        return $this;
    }

    /**
     * Get eveUserid
     *
     * @return integer
     */
    public function getEveUserid()
    {
        return $this->eveUserid;
    }

    /**
     * Set eveUsername
     *
     * @param string $eveUsername
     *
     * @return User
     */
    public function setEveUsername($eveUsername)
    {
        $this->eveUsername = $eveUsername;

        return $this;
    }

    /**
     * Get eveUsername
     *
     * @return string
     */
    public function getEveUsername()
    {
        return $this->eveUsername;
    }

    /**
     * Set eveCorpid
     *
     * @param \Application\Entity\EveCorporation $eveCorpid
     *
     * @return User
     */
    public function setEveCorpid(\Application\Entity\EveCorporation $eveCorpid = null)
    {
        $this->eveCorpid = $eveCorpid;

        return $this;
    }

    /**
     * Get eveCorpid
     *
     * @return \Application\Entity\EveCorporation
     */
    public function getEveCorpid()
    {
        return $this->eveCorpid;
    }
    
    /**
     * Returns the array of roles assigned to this user.
     *
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }
    
    /**
     * Returns the string of assigned role names.
     */
    public function getRolesAsString()
    {
        $roleList = '';
        
        $count = count($this->roles);
        $i = 0;
        foreach ($this->roles as $role) {
            $roleList .= $role->getName();
            if ($i<$count-1) {
                $roleList .= ', ';
            }
            $i++;
        }
        
        return $roleList;
    }
    
    /**
     * Assigns a role to user.
     */
    public function addRole($role)
    {
        $this->roles->add($role);
    }    
}
