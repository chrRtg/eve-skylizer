<?php

namespace User\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserCli
 *
 * @ORM\Table(name="user_cli", uniqueConstraints={@ORM\UniqueConstraint(name="eve_userid_UNIQUE", columns={"eve_userid"})}, indexes={@ORM\Index(name="idx_lifetime", columns={"eve_tokenlifetime"})})
 * @ORM\Entity
 */
class UserCli
{
    /**
     * @var int
     *
     * @ORM\Column(name="eve_userid", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     */
    private $eveUserid;

    /**
     * @var int
     *
     * @ORM\Column(name="eve_corpid", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $eveCorpid;

    /**
     * @var string
     *
     * @ORM\Column(name="eve_token", type="string", length=255, precision=0, scale=0, nullable=false, unique=false)
     */
    private $eveToken;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="eve_tokenlifetime", type="datetime", precision=0, scale=0, nullable=false, unique=false)
     */
    private $eveTokenlifetime;

    /**
     * @var string|null
     *
     * @ORM\Column(name="eve_scope", type="text", length=16777215, precision=0, scale=0, nullable=true, unique=false)
     */
    private $eveScope;


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
     * Set eveToken.
     *
     * @param string $eveToken
     *
     * @return UserCli
     */
    public function setEveToken($eveToken)
    {
        $this->eveToken = $eveToken;

        return $this;
    }

    /**
     * Get eveToken.
     *
     * @return string
     */
    public function getEveToken()
    {
        return $this->eveToken;
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
     * Set eveScope.
     *
     * @param string|null $eveScope
     *
     * @return UserCli
     */
    public function setEveScope($eveScope = null)
    {
        $this->eveScope = $eveScope;

        return $this;
    }

    /**
     * Get eveScope.
     *
     * @return string|null
     */
    public function getEveScope()
    {
        return $this->eveScope;
    }
}
