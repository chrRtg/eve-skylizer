<?php

namespace VposMoon\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AtMiningObserver
 *
 * @ORM\Table(name="at_mining_observer", indexes={@ORM\Index(name="idx_struct", columns={"structure_id"})})
 * @ORM\Entity
 */
class AtMiningObserver
{
    /**
     * @var int
     *
     * @ORM\Column(name="emo_id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $emoId;

    /**
     * @var int
     *
     * @ORM\Column(name="structure_id", type="bigint", precision=0, scale=0, nullable=false, unique=false)
     */
    private $structureId;

    /**
     * @var string
     *
     * @ORM\Column(name="observer_type", type="string", length=80, precision=0, scale=0, nullable=false, unique=false)
     */
    private $observerType;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="last_updated", type="datetime", precision=0, scale=0, nullable=true, unique=false)
     */
    private $lastUpdated;


    /**
     * Get emoId.
     *
     * @return int
     */
    public function getEmoId()
    {
        return $this->emoId;
    }

    /**
     * Set structureId.
     *
     * @param int $structureId
     *
     * @return AtMiningObserver
     */
    public function setStructureId($structureId)
    {
        $this->structureId = $structureId;

        return $this;
    }

    /**
     * Get structureId.
     *
     * @return int
     */
    public function getStructureId()
    {
        return $this->structureId;
    }

    /**
     * Set observerType.
     *
     * @param string $observerType
     *
     * @return AtMiningObserver
     */
    public function setObserverType($observerType)
    {
        $this->observerType = $observerType;

        return $this;
    }

    /**
     * Get observerType.
     *
     * @return string
     */
    public function getObserverType()
    {
        return $this->observerType;
    }

    /**
     * Set lastUpdated.
     *
     * @param \DateTime|null $lastUpdated
     *
     * @return AtMiningObserver
     */
    public function setLastUpdated($lastUpdated = null)
    {
        $this->lastUpdated = $lastUpdated;

        return $this;
    }

    /**
     * Get lastUpdated.
     *
     * @return \DateTime|null
     */
    public function getLastUpdated()
    {
        return $this->lastUpdated;
    }
}
