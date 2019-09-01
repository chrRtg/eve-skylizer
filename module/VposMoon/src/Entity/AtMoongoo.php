<?php

namespace VposMoon\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AtMoongoo
 *
 * @ORM\Table(name="at_moongoo", uniqueConstraints={@ORM\UniqueConstraint(name="key_moon_goo_combine", columns={"moon_id", "eve_invtypes_typeid"})}, indexes={@ORM\Index(name="key_mapdenormalize", columns={"moon_id"}), @ORM\Index(name="key_invtype", columns={"eve_invtypes_typeid"}), @ORM\Index(name="key_lastseen_date", columns={"lastseen_date"}), @ORM\Index(name="key_moonid", columns={"moon_id"})})
 * @ORM\Entity
 */
class AtMoongoo
{
    /**
     * @var integer
     *
     * @ORM\Column(name="moongoo_id",           type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $moongooId;

    /**
     * @var string
     *
     * @ORM\Column(name="name_en", type="string", length=80, nullable=true)
     */
    private $nameEn;

    /**
     * @var float
     *
     * @ORM\Column(name="goo_amount", type="float", precision=12, scale=6, nullable=false)
     */
    private $gooAmount = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="created_by", type="integer", nullable=false, options={"default":0})
     */
    private $createdBy = '0';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="create_date", type="datetime", nullable=false)
     */
    private $createDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="lastseen_by", type="integer", nullable=false, options={"default":0})
     */
    private $lastseenBy = '0';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastseen_date", type="datetime", nullable=false)
     */
    private $lastseenDate;

    /**
     * @var \Application\Entity\Invtypes
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Invtypes")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="eve_invtypes_typeid",                referencedColumnName="typeID")
     * })
     */
    private $eveInvtypesTypeid;

    /**
     * @var \VposMoon\Entity\AtMoon
     *
     * @ORM\ManyToOne(targetEntity="VposMoon\Entity\AtMoon", inversedBy="moon_id")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="moon_id", referencedColumnName="moon_id")
     * })
     */
    private $moon;



    /**
     * Get moongooId
     *
     * @return integer
     */
    public function getMoongooId()
    {
        return $this->moongooId;
    }

    /**
     * Set nameEn
     *
     * @param string $nameEn
     *
     * @return AtMoongoo
     */
    public function setNameEn($nameEn)
    {
        $this->nameEn = $nameEn;

        return $this;
    }

    /**
     * Get nameEn
     *
     * @return string
     */
    public function getNameEn()
    {
        return $this->nameEn;
    }

    /**
     * Set gooAmount
     *
     * @param float $gooAmount
     *
     * @return AtMoongoo
     */
    public function setGooAmount($gooAmount)
    {
        $this->gooAmount = $gooAmount;

        return $this;
    }

    /**
     * Get gooAmount
     *
     * @return float
     */
    public function getGooAmount()
    {
        return $this->gooAmount;
    }

    /**
     * Set createdBy
     *
     * @param integer $createdBy
     *
     * @return AtMoongoo
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return integer
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set createDate
     *
     * @param \DateTime $createDate
     *
     * @return AtMoongoo
     */
    public function setCreateDate($createDate)
    {
        $this->createDate = $createDate;

        return $this;
    }

    /**
     * Get createDate
     *
     * @return \DateTime
     */
    public function getCreateDate()
    {
        return $this->createDate;
    }

    /**
     * Set lastseenBy
     *
     * @param integer $lastseenBy
     *
     * @return AtMoongoo
     */
    public function setLastseenBy($lastseenBy)
    {
        $this->lastseenBy = $lastseenBy;

        return $this;
    }

    /**
     * Get lastseenBy
     *
     * @return integer
     */
    public function getLastseenBy()
    {
        return $this->lastseenBy;
    }

    /**
     * Set lastseenDate
     *
     * @param \DateTime $lastseenDate
     *
     * @return AtMoongoo
     */
    public function setLastseenDate($lastseenDate)
    {
        $this->lastseenDate = $lastseenDate;

        return $this;
    }

    /**
     * Get lastseenDate
     *
     * @return \DateTime
     */
    public function getLastseenDate()
    {
        return $this->lastseenDate;
    }

    /**
     * Set eveInvtypesTypeid
     *
     * @param \Application\Entity\Invtypes $eveInvtypesTypeid
     *
     * @return AtMoongoo
     */
    public function setEveInvtypesTypeid(\Application\Entity\Invtypes $eveInvtypesTypeid = null)
    {
        $this->eveInvtypesTypeid = $eveInvtypesTypeid;

        return $this;
    }

    /**
     * Get eveInvtypesTypeid
     *
     * @return \Application\Entity\Invtypes
     */
    public function getEveInvtypesTypeid()
    {
        return $this->eveInvtypesTypeid;
    }

    /**
     * Set moon
     *
     * @param \VposMoon\Entity\AtMoon $moon
     *
     * @return AtMoongoo
     */
    public function setMoon(\VposMoon\Entity\AtMoon $moon = null)
    {
        $this->moon = $moon;

        return $this;
    }

    /**
     * Get moon
     *
     * @return \VposMoon\Entity\AtMoon
     */
    public function getMoon()
    {
        return $this->moon;
    }
}
