<?php

namespace VposMoon\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AtStructure
 *
 * @ORM\Table(name="at_structure",         indexes={
 * @ORM\Index(name="idx_targetsystem_id",   columns={"solar_system_id"}),
 * @ORM\Index(name="idx_cosmic_detail_id", columns={"at_cosmic_detail_id"}),
 * @ORM\Index(name="idx_lastseen_data",    columns={"lastseen_date"}),
 * @ORM\Index(name="idx_create_date",      columns={"create_date"}),
 * @ORM\Index(name="idx_st_invtype",       columns={"type_id"}),
 * @ORM\Index(name="idx_targetsystem_id",   columns={"target_system_id"})
 * })
 * @ORM\Entity
 */
class AtStructure
{
    /**
     * @var int
     *
     * @ORM\Column(name="id",                   type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int|null
     *
     * @ORM\Column(name="type_id", type="integer", nullable=true, options={"comment"="invTypes in case of structures"})
     */
    private $typeId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="corporation_id", type="integer", nullable=true)
     */
    private $corporationId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="structure_name", type="string", length=255, nullable=true, options={"comment"="player give name"})
     */
    private $structureName;

    /**
     * @var int
     *
     * @ORM\Column(name="created_by", type="integer", nullable=false)
     */
    private $createdBy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="create_date", type="datetime", nullable=false)
     */
    private $createDate;

    /**
     * @var int
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
     * @var int|null
     *
     * @ORM\Column(name="group_id", type="bigint", nullable=true)
     */
    private $groupId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="signature", type="string", length=10, nullable=true, options={"comment"="scan signature"})
     */
    private $signature;

    /**
     * @var int|null
     *
     * @ORM\Column(name="scan_quality", type="integer", nullable=true)
     */
    private $scanQuality;

    /**
     * @var string|null
     *
     * @ORM\Column(name="scan_type", type="string", length=10, nullable=true, options={"comment"="scan signature"})
     */
    private $scanType;

    /**
     * @var int|null
     *
     * @ORM\Column(name="solar_system_id", type="integer", nullable=true, options={"comment"="maps to mapdenormalize.itemID to indicate the solarsystem"})
     */
    private $solarSystemId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="celestial_id", type="integer", nullable=true, options={"comment"="maps to mapdenormalize. Indicates the nearest celstial"})
     */
    private $celestialId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="celestial_distance", type="bigint", nullable=true, options={"comment"="in KM, how far is the structure away from the celestial"})
     */
    private $celestialDistance;

    /**
     * @var int|null
     *
     * @ORM\Column(name="at_cosmic_detail_id", type="integer", nullable=true, options={"comment"="maps to at_cosmic_detail if entity is a site"})
     */
    private $atCosmicDetailId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="target_system_id", type="integer", nullable=true, options={"comment"="to link this structure to another solarsystem - wh or gate"})
     */
    private $targetSystemId;

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

    /**
     * Set signature.
     *
     * @param string|null $signature
     *
     * @return AtStructure
     */
    public function setSignature($signature = null)
    {
        $this->signature = $signature;

        return $this;
    }

    /**
     * Get signature.
     *
     * @return string|null
     */
    public function getSignature()
    {
        return $this->signature;
    }

    /**
     * Set scanQuality.
     *
     * @param int|null $scanQuality
     *
     * @return AtStructure
     */
    public function setScanQuality($scanQuality = null)
    {
        $this->scanQuality = $scanQuality;

        return $this;
    }

    /**
     * Get scanQuality.
     *
     * @return int|null
     */
    public function getScanQuality()
    {
        return $this->scanQuality;
    }

    /**
     * Set scanType.
     *
     * @param string|null $scanType
     *
     * @return AtStructure
     */
    public function setScanType($scanType = null)
    {
        $this->scanType = $scanType;

        return $this;
    }

    /**
     * Get scanType.
     *
     * @return string|null
     */
    public function getScanType()
    {
        return $this->scanType;
    }

    /**
     * Set solarSystemId.
     *
     * @param int|null $solarSystemId
     *
     * @return AtStructure
     */
    public function setSolarSystemId($solarSystemId = null)
    {
        $this->solarSystemId = $solarSystemId;

        return $this;
    }

    /**
     * Get solarSystemId.
     *
     * @return int|null
     */
    public function getSolarSystemId()
    {
        return $this->solarSystemId;
    }

    /**
     * Set celestialId.
     *
     * @param int|null $celestialId
     *
     * @return AtStructure
     */
    public function setCelestialId($celestialId = null)
    {
        $this->celestialId = $celestialId;

        return $this;
    }

    /**
     * Get celestialId.
     *
     * @return int|null
     */
    public function getCelestialId()
    {
        return $this->celestialId;
    }

    /**
     * Set celestialDistance.
     *
     * @param int|null $celestialDistance
     *
     * @return AtStructure
     */
    public function setCelestialDistance($celestialDistance = null)
    {
        $this->celestialDistance = $celestialDistance;

        return $this;
    }

    /**
     * Get celestialDistance.
     *
     * @return int|null
     */
    public function getCelestialDistance()
    {
        return $this->celestialDistance;
    }

    /**
     * Set atCosmicDetailId.
     *
     * @param int|null $atCosmicDetailId
     *
     * @return AtStructure
     */
    public function setAtCosmicDetailId($atCosmicDetailId = null)
    {
        $this->atCosmicDetailId = $atCosmicDetailId;

        return $this;
    }

    /**
     * Get atCosmicDetailId.
     *
     * @return int|null
     */
    public function getAtCosmicDetailId()
    {
        return $this->atCosmicDetailId;
    }

    /**
     * Set targetSystemId.
     *
     * @param int|null $targetSystemId
     *
     * @return AtStructure
     */
    public function setTargetSystemId($targetSystemId = null)
    {
        $this->targetSystemId = $targetSystemId;

        return $this;
    }

    /**
     * Get targetSystemId.
     *
     * @return int|null
     */
    public function getTargetSystemId()
    {
        return $this->targetSystemId;
    }
}
