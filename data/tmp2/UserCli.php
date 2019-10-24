<?php

namespace User\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserCli
 *
 * @ORM\Table(name="user_cli", uniqueConstraints={@ORM\UniqueConstraint(name="eve_userid_UNIQUE", columns={"eve_userid"})}, indexes={@ORM\Index(name="ids_inuse", columns={"in_use"}), @ORM\Index(name="idx_lifetime", columns={"eve_tokenlifetime"})})
 * @ORM\Entity
 */
class UserCli
{
    /**
     * @var int
     *
     * @ORM\Column(name="eve_userid", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $eveUserid;

    /**
     * @var int
     *
     * @ORM\Column(name="eve_corpid", type="integer", nullable=false)
     */
    private $eveCorpid;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="eve_tokenlifetime", type="datetime", nullable=false)
     */
    private $eveTokenlifetime;

    /**
     * @var string|null
     *
     * @ORM\Column(name="authcontainer", type="text", length=16777215, nullable=true)
     */
    private $authcontainer;

    /**
     * @var int
     *
     * @ORM\Column(name="in_use", type="integer", nullable=false)
     */
    private $inUse = '0';


    /**
     * Get eveUserid.
     *
     * @return int
     */
    public function getEveUserid()
    {
        return $this->eveUserid;
    }

    /**
     * Set eveUserid.
     *
     * @param int $eveUserid
     *
     * @return UserCli
     */
    public function setEveUserid($eveUserid)
    {
        $this->eveUserid = $eveUserid;

        return $this;
    }


    /**
     * Set eveCorpid.
     *
     * @param int $eveCorpid
     *
     * @return UserCli
     */
    public function setEveCorpid($eveCorpid)
    {
        $this->eveCorpid = $eveCorpid;

        return $this;
    }

    /**
     * Get eveCorpid.
     *
     * @return int
     */
    public function getEveCorpid()
    {
        return $this->eveCorpid;
    }

    /**
     * Set eveTokenlifetime.
     *
     * @param \DateTime $eveTokenlifetime
     *
     * @return UserCli
     */
    public function setEveTokenlifetime($eveTokenlifetime)
    {
        $this->eveTokenlifetime = $eveTokenlifetime;

        return $this;
    }

    /**
     * Get eveTokenlifetime.
     *
     * @return \DateTime
     */
    public function getEveTokenlifetime()
    {
        return $this->eveTokenlifetime;
    }

    /**
     * Set authcontainer.
     *
     * @param string|null $authcontainer
     *
     * @return UserCli
     */
    public function setAuthContainer($authcontainer = null)
    {
        $this->authcontainer = $authcontainer;

        return $this;
    }

    /**
     * Get authcontainer.
     *
     * @return string|null
     */
    public function getAuthContainer()
    {
        return $this->authcontainer;
    }

    /**
     * Set inUse.
     *
     * @param string|null $inUse
     *
     * @return UserCli
     */
    public function setInUse($inUse = null)
    {
        $this->inUse = $inUse;

        return $this;
    }

    /**
     * Get inUse.
     *
     * @return string|null
     */
    public function getInUse()
    {
        return $this->inUse;
    }
}
