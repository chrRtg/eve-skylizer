<?php

namespace VposMoon\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AtMiningPeriod
 *
 * @ORM\Table(name="at_mining_period", uniqueConstraints={@ORM\UniqueConstraint(name="idx_uniq", columns={"structure_id", "date_start", "date_end"})})
 * @ORM\Entity
 */
class AtMiningPeriod
{
    /**
     * @var int
     *
     * @ORM\Column(name="amp_id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $ampId;

    /**
     * @var int
     *
     * @ORM\Column(name="structure_id", type="bigint", precision=0, scale=0, nullable=false, unique=false)
     */
    private $structureId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_start", type="datetime", precision=0, scale=0, nullable=false, unique=false)
     */
    private $dateStart;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_end", type="datetime", precision=0, scale=0, nullable=false, unique=false)
     */
    private $dateEnd;

    /**
     * Get mlId.
     *
     * @return int
     */
    public function getAmpId()
    {
        return $this->ampId;
    }

    /**
     * Set structureId.
     *
     * @param int $structureId
     *
     * @return AtMiningPeriod
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
     * Set DateStart.
     *
     * @param \DateTime $dateStart
     *
     * @return AtMiningPeriod
     */
    public function setDateStart($dateStart)
    {
        $this->dateStart = $dateStart;

        return $this;
    }

    /**
     * Get DateStart.
     *
     * @return \DateTime
     */
    public function getDateStart()
    {
        return $this->dateStart;
    }


    /**
     * Set DateEnd.
     *
     * @param \DateTime $dateEnd
     *
     * @return AtMiningPeriod
     */
    public function setDateEnd($dateEnd)
    {
        $this->dateEnd = $dateEnd;

        return $this;
    }

    /**
     * Get DateEnd.
     *
     * @return \DateTime
     */
    public function getDateEnd()
    {
        return $this->dateEnd;
    }
}
