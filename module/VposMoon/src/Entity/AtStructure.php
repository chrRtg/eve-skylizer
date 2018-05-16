<?php

namespace VposMoon\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AtStructure
 *
 * @ORM\Table(name="at_structure", 
 *		indexes={
 *			@ORM\Index(name="idx_invitem", columns={"item_id"}), 
 *			@ORM\Index(name="idx_group", columns={"group_id"}), 
 *			@ORM\Index(name="idx_invtype", columns={"type_id"})
 *		})
 * @ORM\Entity
 */
class AtStructure
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", precision=0, scale=0, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="type_id", type="integer", length=11, nullable=true, options={"comment":"invTypes in case of structures"})
     */
    private $typeId;

    /**
     * @var integer
     *
     * @ORM\Column(name="item_id", type="bigint", length=20, nullable=true, options={"comment":"invNames -> mapDenormalize in case of celestials"})
     */
    private $itemId;

    /**
     * @var integer
     *
     * @ORM\Column(name="group_id", type="integer", precision=0, scale=0, nullable=false)
     */
    private $groupId;

    /**
     * @var integer
     *
     * @ORM\Column(name="corporation_id", type="integer", precision=0, scale=0, nullable=true)
     */
    private $corporationId;

    /**
     * @var string
     *
     * @ORM\Column(name="structure_name", type="string", length=255, nullable=true, options={"comment":"player give name"})
     */
    private $structureName;

    /**
     * @var integer
     *
     * @ORM\Column(name="created_by", type="integer", length=11, nullable=false)
     */
    private $createdBy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="create_date", type="datetime", nullable=false)
     */
    private $createDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="lastseen_by", type="integer", nullable=false)
     */
    private $lastseenBy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastseen_date", type="datetime", nullable=false)
     */
    private $lastseenDate;


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
     * Set typeId
     *
     * @param integer $typeId
     *
     * @return AtStructure
     */
    public function setTypeId($typeId)
    {
        $this->typeId = $typeId;

        return $this;
    }

    /**
     * Get typeId
     *
     * @return integer
     */
    public function getTypeId()
    {
        return $this->typeId;
    }

    /**
     * Set itemId
     *
     * @param integer $itemId
     *
     * @return AtStructure
     */
    public function setItemId($itemId)
    {
        $this->itemId = $itemId;

        return $this;
    }

    /**
     * Get itemId
     *
     * @return integer
     */
    public function getItemId()
    {
        return $this->itemId;
    }

    /**
     * Set groupId
     *
     * @param integer $groupId
     *
     * @return AtStructure
     */
    public function setGroupId($groupId)
    {
        $this->groupId = $groupId;

        return $this;
    }

    /**
     * Get groupId
     *
     * @return integer
     */
    public function getGroupId()
    {
        return $this->groupId;
    }

    /**
     * Set corporationId
     *
     * @param integer $corporationId
     *
     * @return AtStructure
     */
    public function setCorporationId($corporationId)
    {
        $this->corporationId = $corporationId;

        return $this;
    }

    /**
     * Get corporationId
     *
     * @return integer
     */
    public function getCorporationId()
    {
        return $this->corporationId;
    }

    /**
     * Set structureName
     *
     * @param string $structureName
     *
     * @return AtStructure
     */
    public function setStructureName($structureName)
    {
        $this->structureName = $structureName;

        return $this;
    }

    /**
     * Get structureName
     *
     * @return string
     */
    public function getStructureName()
    {
        return $this->structureName;
    }

    /**
     * Set createdBy
     *
     * @param integer $createdBy
     *
     * @return AtStructure
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
     * @return AtStructure
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
     * @return AtStructure
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
     * @return AtStructure
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
}

