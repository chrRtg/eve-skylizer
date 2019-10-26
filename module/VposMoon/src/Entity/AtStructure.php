<?php

namespace VposMoon\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AtStructure
 *
 * @ORM\Table(name="at_structure", indexes={@ORM\Index(name="idx_targetsystem_id", columns={"target_system_id"}), @ORM\Index(name="idx_lastseen_data", columns={"lastseen_date"}), @ORM\Index(name="idx_structure_id", columns={"structure_id"}), @ORM\Index(name="idx_st_invtype", columns={"type_id"}), @ORM\Index(name="idx_cosmic_detail_id", columns={"at_cosmic_detail_id"}), @ORM\Index(name="idx_create_date", columns={"create_date"})})
 * @ORM\Entity
 */
class AtStructure
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int|null
     *
     * @ORM\Column(name="type_id", type="integer", precision=0, scale=0, nullable=true, options={"comment"="invTypes in case of structures"}, unique=false)
     */
    private $typeId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="corporation_id", type="integer", precision=0, scale=0, nullable=true, unique=false)
     */
    private $corporationId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="structure_name", type="string", length=255, precision=0, scale=0, nullable=true, options={"comment"="player give name"}, unique=false)
     */
    private $structureName;

    /**
     * @var int
     *
     * @ORM\Column(name="created_by", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $createdBy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="create_date", type="datetime", precision=0, scale=0, nullable=false, unique=false)
     */
    private $createDate;

    /**
     * @var int
     *
     * @ORM\Column(name="lastseen_by", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $lastseenBy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastseen_date", type="datetime", precision=0, scale=0, nullable=false, unique=false)
     */
    private $lastseenDate;

    /**
     * @var int|null
     *
     * @ORM\Column(name="group_id", type="bigint", precision=0, scale=0, nullable=true, unique=false)
     */
    private $groupId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="signature", type="string", length=10, precision=0, scale=0, nullable=true, options={"comment"="scan signature"}, unique=false)
     */
    private $signature;

    /**
     * @var int|null
     *
     * @ORM\Column(name="scan_quality", type="integer", precision=0, scale=0, nullable=true, unique=false)
     */
    private $scanQuality;

    /**
     * @var string|null
     *
     * @ORM\Column(name="scan_type", type="string", length=10, precision=0, scale=0, nullable=true, options={"comment"="scan signature"}, unique=false)
     */
    private $scanType;

    /**
     * @var int|null
     *
     * @ORM\Column(name="solar_system_id", type="integer", precision=0, scale=0, nullable=true, options={"comment"="maps to mapdenormalize.itemID to indicate the solarsystem"}, unique=false)
     */
    private $solarSystemId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="celestial_id", type="integer", precision=0, scale=0, nullable=true, options={"comment"="maps to mapdenormalize. Indicates the nearest celstial"}, unique=false)
     */
    private $celestialId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="celestial_distance", type="bigint", precision=0, scale=0, nullable=true, options={"comment"="in KM, how far is the structure away from the celestial"}, unique=false)
     */
    private $celestialDistance;

    /**
     * @var int|null
     *
     * @ORM\Column(name="at_cosmic_detail_id", type="integer", precision=0, scale=0, nullable=true, options={"comment"="maps to at_cosmic_detail if entity is a site"}, unique=false)
     */
    private $atCosmicDetailId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="target_system_id", type="integer", precision=0, scale=0, nullable=true, options={"comment"="to link this structure to another solarsystem - wh or gate"}, unique=false)
     */
    private $targetSystemId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="structure_id", type="bigint", precision=0, scale=0, nullable=true, unique=false)
     */
    private $structureId;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="fuel_expires", type="datetime", precision=0, scale=0, nullable=true, unique=false)
     */
    private $fuelExpires;

    /**
     * @var int|null
     *
     * @ORM\Column(name="reinforce_hour", type="integer", precision=0, scale=0, nullable=true, unique=false)
     */
    private $reinforceHour;

    /**
     * @var int|null
     *
     * @ORM\Column(name="reinforce_weekday", type="integer", precision=0, scale=0, nullable=true, unique=false)
     */
    private $reinforceWeekday;

    /**
     * @var string|null
     *
     * @ORM\Column(name="structure_state", type="string", length=80, precision=0, scale=0, nullable=true, unique=false)
     */
    private $structureState;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="state_timer_start", type="datetime", precision=0, scale=0, nullable=true, unique=false)
     */
    private $stateTimerStart;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="state_timer_end", type="datetime", precision=0, scale=0, nullable=true, unique=false)
     */
    private $stateTimerEnd;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="chunk_arrival_time", type="datetime", precision=0, scale=0, nullable=true, unique=false)
     */
    private $chunkArrivalTime;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="extraction_start_time", type="datetime", precision=0, scale=0, nullable=true, unique=false)
     */
    private $extractionStartTime;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="natural_decay_time", type="datetime", precision=0, scale=0, nullable=true, unique=false)
     */
    private $naturalDecayTime;


    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set typeId.
     *
     * @param int|null $typeId
     *
     * @return AtStructure
     */
    public function setTypeId($typeId = null)
    {
        $this->typeId = $typeId;

        return $this;
    }

    /**
     * Get typeId.
     *
     * @return int|null
     */
    public function getTypeId()
    {
        return $this->typeId;
    }

    /**
     * Set corporationId.
     *
     * @param int|null $corporationId
     *
     * @return AtStructure
     */
    public function setCorporationId($corporationId = null)
    {
        $this->corporationId = $corporationId;

        return $this;
    }

    /**
     * Get corporationId.
     *
     * @return int|null
     */
    public function getCorporationId()
    {
        return $this->corporationId;
    }

    /**
     * Set structureName.
     *
     * @param string|null $structureName
     *
     * @return AtStructure
     */
    public function setStructureName($structureName = null)
    {
        $this->structureName = $structureName;

        return $this;
    }

    /**
     * Get structureName.
     *
     * @return string|null
     */
    public function getStructureName()
    {
        return $this->structureName;
    }

    /**
     * Set createdBy.
     *
     * @param int $createdBy
     *
     * @return AtStructure
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy.
     *
     * @return int
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set createDate.
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
     * Get createDate.
     *
     * @return \DateTime
     */
    public function getCreateDate()
    {
        return $this->createDate;
    }

    /**
     * Set lastseenBy.
     *
     * @param int $lastseenBy
     *
     * @return AtStructure
     */
    public function setLastseenBy($lastseenBy)
    {
        $this->lastseenBy = $lastseenBy;

        return $this;
    }

    /**
     * Get lastseenBy.
     *
     * @return int
     */
    public function getLastseenBy()
    {
        return $this->lastseenBy;
    }

    /**
     * Set lastseenDate.
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
     * Get lastseenDate.
     *
     * @return \DateTime
     */
    public function getLastseenDate()
    {
        return $this->lastseenDate;
    }

    /**
     * Set groupId.
     *
     * @param int|null $groupId
     *
     * @return AtStructure
     */
    public function setGroupId($groupId = null)
    {
        $this->groupId = $groupId;

        return $this;
    }

    /**
     * Get groupId.
     *
     * @return int|null
     */
    public function getGroupId()
    {
        return $this->groupId;
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

    /**
     * Set structureId.
     *
     * @param int|null $structureId
     *
     * @return AtStructure
     */
    public function setStructureId($structureId = null)
    {
        $this->structureId = $structureId;

        return $this;
    }

    /**
     * Get structureId.
     *
     * @return int|null
     */
    public function getStructureId()
    {
        return $this->structureId;
    }

    /**
     * Set fuelExpires.
     *
     * @param \DateTime|null $fuelExpires
     *
     * @return AtStructure
     */
    public function setFuelExpires($fuelExpires = null)
    {
        $this->fuelExpires = $fuelExpires;

        return $this;
    }

    /**
     * Get fuelExpires.
     *
     * @return \DateTime|null
     */
    public function getFuelExpires()
    {
        return $this->fuelExpires;
    }

    /**
     * Set reinforceHour.
     *
     * @param int|null $reinforceHour
     *
     * @return AtStructure
     */
    public function setReinforceHour($reinforceHour = null)
    {
        $this->reinforceHour = $reinforceHour;

        return $this;
    }

    /**
     * Get reinforceHour.
     *
     * @return int|null
     */
    public function getReinforceHour()
    {
        return $this->reinforceHour;
    }

    /**
     * Set reinforceWeekday.
     *
     * @param int|null $reinforceWeekday
     *
     * @return AtStructure
     */
    public function setReinforceWeekday($reinforceWeekday = null)
    {
        $this->reinforceWeekday = $reinforceWeekday;

        return $this;
    }

    /**
     * Get reinforceWeekday.
     *
     * @return int|null
     */
    public function getReinforceWeekday()
    {
        return $this->reinforceWeekday;
    }

    /**
     * Set structureState.
     *
     * @param string|null $structureState
     *
     * @return AtStructure
     */
    public function setStructureState($structureState = null)
    {
        $this->structureState = $structureState;

        return $this;
    }

    /**
     * Get structureState.
     *
     * @return string|null
     */
    public function getStructureState()
    {
        return $this->structureState;
    }

    /**
     * Set stateTimerStart.
     *
     * @param \DateTime|null $stateTimerStart
     *
     * @return AtStructure
     */
    public function setStateTimerStart($stateTimerStart = null)
    {
        $this->stateTimerStart = $stateTimerStart;

        return $this;
    }

    /**
     * Get stateTimerStart.
     *
     * @return \DateTime|null
     */
    public function getStateTimerStart()
    {
        return $this->stateTimerStart;
    }

    /**
     * Set stateTimerEnd.
     *
     * @param \DateTime|null $stateTimerEnd
     *
     * @return AtStructure
     */
    public function setStateTimerEnd($stateTimerEnd = null)
    {
        $this->stateTimerEnd = $stateTimerEnd;

        return $this;
    }

    /**
     * Get stateTimerEnd.
     *
     * @return \DateTime|null
     */
    public function getStateTimerEnd()
    {
        return $this->stateTimerEnd;
    }

    /**
     * Set chunkArrivalTime.
     *
     * @param \DateTime|null $chunkArrivalTime
     *
     * @return AtStructure
     */
    public function setChunkArrivalTime($chunkArrivalTime = null)
    {
        $this->chunkArrivalTime = $chunkArrivalTime;

        return $this;
    }

    /**
     * Get chunkArrivalTime.
     *
     * @return \DateTime|null
     */
    public function getChunkArrivalTime()
    {
        return $this->chunkArrivalTime;
    }

    /**
     * Set extractionStartTime.
     *
     * @param \DateTime|null $extractionStartTime
     *
     * @return AtStructure
     */
    public function setExtractionStartTime($extractionStartTime = null)
    {
        $this->extractionStartTime = $extractionStartTime;

        return $this;
    }

    /**
     * Get extractionStartTime.
     *
     * @return \DateTime|null
     */
    public function getExtractionStartTime()
    {
        return $this->extractionStartTime;
    }

    /**
     * Set naturalDecayTime.
     *
     * @param \DateTime|null $naturalDecayTime
     *
     * @return AtStructure
     */
    public function setNaturalDecayTime($naturalDecayTime = null)
    {
        $this->naturalDecayTime = $naturalDecayTime;

        return $this;
    }

    /**
     * Get naturalDecayTime.
     *
     * @return \DateTime|null
     */
    public function getNaturalDecayTime()
    {
        return $this->naturalDecayTime;
    }
}
