<?php

namespace User\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserCli
 *
 * @ORM\Table(name="user_cli", indexes={@ORM\Index(name="ids_inuse", columns={"in_use"}), @ORM\Index(name="idx_lifetime", columns={"eve_tokenlifetime"})})
 * @ORM\Entity
 */
class UserCli
{
    /**
     * @var int
     *
     * @ORM\Column(name="eve_userid", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $eveUserid;

    /**
     * @var int
     *
     * @ORM\Column(name="eve_corpid", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $eveCorpid;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="eve_tokenlifetime", type="datetime", precision=0, scale=0, nullable=false, unique=false)
     */
    private $eveTokenlifetime;

    /**
     * @var string|null
     *
     * @ORM\Column(name="authcontainer", type="text", length=16777215, precision=0, scale=0, nullable=true, unique=false)
     */
    private $authcontainer;

    /**
     * @var int
     *
     * @ORM\Column(name="in_use", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $inUse;


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
     * Get eveUserid.
     *
     * @return int
     */
    public function getEveUserid()
    {
        return $this->eveUserid;
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
    public function setAuthcontainer($authcontainer = null)
    {
        $this->authcontainer = $authcontainer;

        return $this;
    }

    /**
     * Get authcontainer.
     *
     * @return string|null
     */
    public function getAuthcontainer()
    {
        return $this->authcontainer;
    }

    /**
     * Set inUse.
     *
     * @param int $inUse
     *
     * @return UserCli
     */
    public function setInUse($inUse)
    {
        $this->inUse = $inUse;

        return $this;
    }

    /**
     * Get inUse.
     *
     * @return int
     */
    public function getInUse()
    {
        return $this->inUse;
    }
}
