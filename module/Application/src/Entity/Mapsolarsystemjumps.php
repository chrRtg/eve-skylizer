<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Mapsolarsystemjumps
 *
 * @ORM\Table(name="mapSolarSystemJumps", 
 *    indexes={
 * @ORM\Index(name="ix_mssj_fromsolar",   columns={"fromSolarSystemID"}), 
 * @ORM\Index(name="ix_mssj_tosolar",     columns={"toSolarSystemID"})})
 * @ORM\Entity
 */
class Mapsolarsystemjumps
{
    /**
     * @var integer
     *
     * @ORM\Column(name="fromRegionID", type="integer", nullable=true)
     */
    private $fromregionid;

    /**
     * @var integer
     *
     * @ORM\Column(name="fromConstellationID", type="integer", nullable=true)
     */
    private $fromconstellationid;

    /**
     * @var integer
     *
     * @ORM\Column(name="fromSolarSystemID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $fromsolarsystemid;

    /**
     * @var integer
     *
     * @ORM\Column(name="toSolarSystemID",  type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $tosolarsystemid;

    /**
     * @var integer
     *
     * @ORM\Column(name="toConstellationID", type="integer", nullable=true)
     */
    private $toconstellationid;

    /**
     * @var integer
     *
     * @ORM\Column(name="toRegionID", type="integer", nullable=true)
     */
    private $toregionid;



    /**
     * Set fromregionid
     *
     * @param integer $fromregionid
     *
     * @return Mapsolarsystemjumps
     */
    public function setFromregionid($fromregionid)
    {
        $this->fromregionid = $fromregionid;

        return $this;
    }

    /**
     * Get fromregionid
     *
     * @return integer
     */
    public function getFromregionid()
    {
        return $this->fromregionid;
    }

    /**
     * Set fromconstellationid
     *
     * @param integer $fromconstellationid
     *
     * @return Mapsolarsystemjumps
     */
    public function setFromconstellationid($fromconstellationid)
    {
        $this->fromconstellationid = $fromconstellationid;

        return $this;
    }

    /**
     * Get fromconstellationid
     *
     * @return integer
     */
    public function getFromconstellationid()
    {
        return $this->fromconstellationid;
    }

    /**
     * Set fromsolarsystemid
     *
     * @param integer $fromsolarsystemid
     *
     * @return Mapsolarsystemjumps
     */
    public function setFromsolarsystemid($fromsolarsystemid)
    {
        $this->fromsolarsystemid = $fromsolarsystemid;

        return $this;
    }

    /**
     * Get fromsolarsystemid
     *
     * @return integer
     */
    public function getFromsolarsystemid()
    {
        return $this->fromsolarsystemid;
    }

    /**
     * Set tosolarsystemid
     *
     * @param integer $tosolarsystemid
     *
     * @return Mapsolarsystemjumps
     */
    public function setTosolarsystemid($tosolarsystemid)
    {
        $this->tosolarsystemid = $tosolarsystemid;

        return $this;
    }

    /**
     * Get tosolarsystemid
     *
     * @return integer
     */
    public function getTosolarsystemid()
    {
        return $this->tosolarsystemid;
    }

    /**
     * Set toconstellationid
     *
     * @param integer $toconstellationid
     *
     * @return Mapsolarsystemjumps
     */
    public function setToconstellationid($toconstellationid)
    {
        $this->toconstellationid = $toconstellationid;

        return $this;
    }

    /**
     * Get toconstellationid
     *
     * @return integer
     */
    public function getToconstellationid()
    {
        return $this->toconstellationid;
    }

    /**
     * Set toregionid
     *
     * @param integer $toregionid
     *
     * @return Mapsolarsystemjumps
     */
    public function setToregionid($toregionid)
    {
        $this->toregionid = $toregionid;

        return $this;
    }

    /**
     * Get toregionid
     *
     * @return integer
     */
    public function getToregionid()
    {
        return $this->toregionid;
    }
}
