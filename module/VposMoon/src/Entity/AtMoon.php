<?php

namespace VposMoon\Entity;

use Doctrine\ORM\Mapping as ORM;
use VposMoon\Entity\AtMoongoo;

/**
 * AtMoon
 *
 * @ORM\Table(name="at_moon", indexes={@ORM\Index(name="k_map", columns={"eve_mapdenormalize_itemid"}), @ORM\Index(name="k_item", columns={"eve_invtypes_typeid"}), @ORM\Index(name="k_owned", columns={"owned_by"})})
 * @ORM\Entity
 */
class AtMoon
{
    /**
     * @var integer
     *
	 * @ORM\Column(name="moon_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
	 * @ORM\OneToMany(targetEntity="VposMoon\Entity\AtMoongoo", mappedBy="moon_id")
     */
    private $moonId;

    /**
     * @var string
     *
     * @ORM\Column(name="named_structure", type="string", length=80, nullable=true)
     */
    private $namedStructure;

    /**
     * @var integer
     *
     * @ORM\Column(name="owned_by", type="integer", nullable=false)
     */
    private $ownedBy;

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
     * @var \Application\Entity\Mapdenormalize
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Mapdenormalize")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="eve_mapdenormalize_itemid", referencedColumnName="itemID")
     * })
     */
    private $eveMapdenormalizeItemid;

    /**
     * @var \Application\Entity\Invtypes
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Invtypes")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="eve_invtypes_typeid", referencedColumnName="typeID")
     * })
     */
    private $eveInvtypesTypeid;



    /**
     * Get moonId
     *
     * @return integer
     */
    public function getMoonId()
    {
        return $this->moonId;
    }

    /**
     * Set namedStructure
     *
     * @param string $namedStructure
     *
     * @return AtMoon
     */
    public function setNamedStructure($namedStructure)
    {
        $this->namedStructure = $namedStructure;

        return $this;
    }

    /**
     * Get namedStructure
     *
     * @return string
     */
    public function getNamedStructure()
    {
        return $this->namedStructure;
    }

    /**
     * Set ownedBy
     *
     * @param integer $ownedBy
     *
     * @return AtMoon
     */
    public function setOwnedBy($ownedBy)
    {
        $this->ownedBy = $ownedBy;

        return $this;
    }

    /**
     * Get ownedBy
     *
     * @return integer
     */
    public function getOwnedBy()
    {
        return $this->ownedBy;
    }

    /**
     * Set createdBy
     *
     * @param integer $createdBy
     *
     * @return AtMoon
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
     * @return AtMoon
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
     * @return AtMoon
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
     * @return AtMoon
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
     * Set eveMapdenormalizeItemid
     *
     * @param \Application\Entity\Mapdenormalize $eveMapdenormalizeItemid
     *
     * @return AtMoon
     */
    public function setEveMapdenormalizeItemid(\Application\Entity\Mapdenormalize $eveMapdenormalizeItemid = null)
    {
        $this->eveMapdenormalizeItemid = $eveMapdenormalizeItemid;

        return $this;
    }

    /**
     * Get eveMapdenormalizeItemid
     *
     * @return \Application\Entity\Mapdenormalize
     */
    public function getEveMapdenormalizeItemid()
    {
        return $this->eveMapdenormalizeItemid;
    }

    /**
     * Set eveInvtypesTypeid
     *
     * @param \Application\Entity\Invtypes $eveInvtypesTypeid
     *
     * @return AtMoon
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
}
