<?php

namespace VposMoon\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AtMiningLedger
 *
 * @ORM\Table(name="at_mining_ledger", uniqueConstraints={@ORM\UniqueConstraint(name="idx_uniq", columns={"structure_id", "eve_userid", "last_updated", "eve_invtypes_typeid"})}, indexes={@ORM\Index(name="idx_struct", columns={"structure_id"}), @ORM\Index(name="idx_goo", columns={"eve_invtypes_typeid"}), @ORM\Index(name="idx_celestial_id", columns={"celestial_id"}), @ORM\Index(name="idx_user", columns={"eve_userid"}), @ORM\Index(name="idx_date", columns={"last_updated"})})
 * @ORM\Entity
 */
class AtMiningLedger
{
    /**
     * @var int
     *
     * @ORM\Column(name="ml_id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $mlId;

    /**
     * @var int
     *
     * @ORM\Column(name="structure_id", type="bigint", precision=0, scale=0, nullable=false, unique=false)
     */
    private $structureId;

    /**
     * @var int
     *
     * @ORM\Column(name="eve_userid", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $eveUserid;

    /**
     * @var int
     *
     * @ORM\Column(name="eve_corpid", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $eveCorpid;

    /**
     * @var int
     *
     * @ORM\Column(name="goo_quantity", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $gooQuantity;

    /**
     * @var int
     *
     * @ORM\Column(name="eve_invtypes_typeid", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $eveInvtypesTypeid;

    /**
     * @var int
     *
     * @ORM\Column(name="celestial_id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $celestialId;

    /**
     * @var string
     *
     * @ORM\Column(name="structure_name", type="string", length=255, precision=0, scale=0, nullable=false, unique=false)
     */
    private $structureName;

    /**
     * @var string
     *
     * @ORM\Column(name="basePrice", type="decimal", precision=19, scale=4, nullable=false, unique=false)
     */
    private $baseprice;

    /**
     * @var string
     *
     * @ORM\Column(name="refinedPrice", type="decimal", precision=19, scale=4, nullable=false, unique=false)
     */
    private $refinedprice;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_updated", type="datetime", precision=0, scale=0, nullable=false, unique=false)
     */
    private $lastUpdated;


    /**
     * Get mlId.
     *
     * @return int
     */
    public function getMlId()
    {
        return $this->mlId;
    }

    /**
     * Set structureId.
     *
     * @param int $structureId
     *
     * @return AtMiningLedger
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
     * Set eveUserid.
     *
     * @param int $eveUserid
     *
     * @return AtMiningLedger
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
     * @return AtMiningLedger
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
     * Set gooQuantity.
     *
     * @param int $gooQuantity
     *
     * @return AtMiningLedger
     */
    public function setGooQuantity($gooQuantity)
    {
        $this->gooQuantity = $gooQuantity;

        return $this;
    }

    /**
     * Get gooQuantity.
     *
     * @return int
     */
    public function getGooQuantity()
    {
        return $this->gooQuantity;
    }

    /**
     * Set eveInvtypesTypeid.
     *
     * @param int $eveInvtypesTypeid
     *
     * @return AtMiningLedger
     */
    public function setEveInvtypesTypeid($eveInvtypesTypeid)
    {
        $this->eveInvtypesTypeid = $eveInvtypesTypeid;

        return $this;
    }

    /**
     * Get eveInvtypesTypeid.
     *
     * @return int
     */
    public function getEveInvtypesTypeid()
    {
        return $this->eveInvtypesTypeid;
    }

    /**
     * Set celestialId.
     *
     * @param int $celestialId
     *
     * @return AtMiningLedger
     */
    public function setCelestialId($celestialId)
    {
        $this->celestialId = $celestialId;

        return $this;
    }

    /**
     * Get celestialId.
     *
     * @return int
     */
    public function getCelestialId()
    {
        return $this->celestialId;
    }

    /**
     * Set structureName.
     *
     * @param string $structureName
     *
     * @return AtMiningLedger
     */
    public function setStructureName($structureName)
    {
        $this->structureName = $structureName;

        return $this;
    }

    /**
     * Get structureName.
     *
     * @return string
     */
    public function getStructureName()
    {
        return $this->structureName;
    }

    /**
     * Set baseprice.
     *
     * @param string $baseprice
     *
     * @return AtMiningLedger
     */
    public function setBaseprice($baseprice)
    {
        $this->baseprice = $baseprice;

        return $this;
    }

    /**
     * Get baseprice.
     *
     * @return string
     */
    public function getBaseprice()
    {
        return $this->baseprice;
    }

    /**
     * Set refinedprice.
     *
     * @param string $refinedprice
     *
     * @return AtMiningLedger
     */
    public function setRefinedprice($refinedprice)
    {
        $this->refinedprice = $refinedprice;

        return $this;
    }

    /**
     * Get refinedprice.
     *
     * @return string
     */
    public function getRefinedprice()
    {
        return $this->refinedprice;
    }

    /**
     * Set lastUpdated.
     *
     * @param \DateTime $lastUpdated
     *
     * @return AtMiningLedger
     */
    public function setLastUpdated($lastUpdated)
    {
        $this->lastUpdated = $lastUpdated;

        return $this;
    }

    /**
     * Get lastUpdated.
     *
     * @return \DateTime
     */
    public function getLastUpdated()
    {
        return $this->lastUpdated;
    }
}
