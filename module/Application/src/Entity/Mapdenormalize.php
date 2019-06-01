<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Mapdenormalize
 *
 * @ORM\Table(name="mapDenormalize",
 *        indexes={
 * @ORM\Index(name="ix_md_groupconstellation", columns={"groupID", "constellationID"}),
 * @ORM\Index(name="ix_md_groupregion",        columns={"groupID", "regionID"}),
 * @ORM\Index(name="ix_md_typeid",             columns={"typeID"}),
 * @ORM\Index(name="ix_md_regionid",           columns={"regionID"}),
 * @ORM\Index(name="ix_md_solarSystemid",      columns={"solarSystemID"}),
 * @ORM\Index(name="ix_md_constellationid",    columns={"constellationID"}),
 * @ORM\Index(name="ix_md_orbitid",            columns={"orbitID"}),
 * @ORM\Index(name="ix_md_groupsystem",        columns={"groupID"})
 *    })
 * @ORM\Entity
 */
class Mapdenormalize
{
    /**
     * @var integer
     *
     * @ORM\Column(name="itemID",           type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $itemid;

    /**
     * @var integer
     *
     * @ORM\Column(name="typeID", type="integer", nullable=true)
     */
    private $typeid;

    /**
     * @var integer
     *
     * @ORM\Column(name="groupID", type="integer", nullable=true)
     */
    private $groupid;

    /**
     * @var integer
     *
     * @ORM\Column(name="solarSystemID", type="integer", nullable=true)
     */
    private $solarsystemid;

    /**
     * @var integer
     *
     * @ORM\Column(name="constellationID", type="integer", nullable=true)
     */
    private $constellationid;

    /**
     * @var integer
     *
     * @ORM\Column(name="orbitID", type="integer", nullable=true)
     */
    private $orbitid;

    /**
     * @var float
     *
     * @ORM\Column(name="x", type="float", precision=10, scale=0, nullable=true)
     */
    private $x;

    /**
     * @var float
     *
     * @ORM\Column(name="y", type="float", precision=10, scale=0, nullable=true)
     */
    private $y;

    /**
     * @var float
     *
     * @ORM\Column(name="z", type="float", precision=10, scale=0, nullable=true)
     */
    private $z;

    /**
     * @var float
     *
     * @ORM\Column(name="radius", type="float", precision=10, scale=0, nullable=true)
     */
    private $radius;

    /**
     * @var string
     *
     * @ORM\Column(name="itemName", type="string", length=100, nullable=true)
     */
    private $itemname;

    /**
     * @var float
     *
     * @ORM\Column(name="security", type="float", precision=10, scale=0, nullable=true)
     */
    private $security;

    /**
     * @var integer
     *
     * @ORM\Column(name="celestialIndex", type="integer", nullable=true)
     */
    private $celestialindex;

    /**
     * @var integer
     *
     * @ORM\Column(name="orbitIndex", type="integer", nullable=true)
     */
    private $orbitindex;

    /**
     * @var \Application\Entity\Maplocationwormholeclasses
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Maplocationwormholeclasses")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="regionID",                                             referencedColumnName="locationID")
     * })
     */
    private $regionid;



    /**
     * Get itemid
     *
     * @return integer
     */
    public function getItemid()
    {
        return $this->itemid;
    }

    /**
     * Set typeid
     *
     * @param integer $typeid
     *
     * @return Mapdenormalize
     */
    public function setTypeid($typeid)
    {
        $this->typeid = $typeid;

        return $this;
    }

    /**
     * Get typeid
     *
     * @return integer
     */
    public function getTypeid()
    {
        return $this->typeid;
    }

    /**
     * Set groupid
     *
     * @param integer $groupid
     *
     * @return Mapdenormalize
     */
    public function setGroupid($groupid)
    {
        $this->groupid = $groupid;

        return $this;
    }

    /**
     * Get groupid
     *
     * @return integer
     */
    public function getGroupid()
    {
        return $this->groupid;
    }

    /**
     * Set constellationid
     *
     * @param integer $constellationid
     *
     * @return Mapdenormalize
     */
    public function setConstellationid($constellationid)
    {
        $this->constellationid = $constellationid;

        return $this;
    }

    /**
     * Get constellationid
     *
     * @return integer
     */
    public function getConstellationid()
    {
        return $this->constellationid;
    }

    /**
     * Set solarsystemid
     *
     * @param integer $solarsystemid
     *
     * @return Mapdenormalize
     */
    public function setSolarSystemid($solarsystemid)
    {
        $this->solarsystemid = $solarsystemid;

        return $this;
    }

    /**
     * Get solarsystemid
     *
     * @return integer
     */
    public function getSolarSystemid()
    {
        return $this->solarsystemid;
    }

    /**
     * Set orbitid
     *
     * @param integer $orbitid
     *
     * @return Mapdenormalize
     */
    public function setOrbitid($orbitid)
    {
        $this->orbitid = $orbitid;

        return $this;
    }

    /**
     * Get orbitid
     *
     * @return integer
     */
    public function getOrbitid()
    {
        return $this->orbitid;
    }

    /**
     * Set x
     *
     * @param float $x
     *
     * @return Mapdenormalize
     */
    public function setX($x)
    {
        $this->x = $x;

        return $this;
    }

    /**
     * Get x
     *
     * @return float
     */
    public function getX()
    {
        return $this->x;
    }

    /**
     * Set y
     *
     * @param float $y
     *
     * @return Mapdenormalize
     */
    public function setY($y)
    {
        $this->y = $y;

        return $this;
    }

    /**
     * Get y
     *
     * @return float
     */
    public function getY()
    {
        return $this->y;
    }

    /**
     * Set z
     *
     * @param float $z
     *
     * @return Mapdenormalize
     */
    public function setZ($z)
    {
        $this->z = $z;

        return $this;
    }

    /**
     * Get z
     *
     * @return float
     */
    public function getZ()
    {
        return $this->z;
    }

    /**
     * Set radius
     *
     * @param float $radius
     *
     * @return Mapdenormalize
     */
    public function setRadius($radius)
    {
        $this->radius = $radius;

        return $this;
    }

    /**
     * Get radius
     *
     * @return float
     */
    public function getRadius()
    {
        return $this->radius;
    }

    /**
     * Set itemname
     *
     * @param string $itemname
     *
     * @return Mapdenormalize
     */
    public function setItemname($itemname)
    {
        $this->itemname = $itemname;

        return $this;
    }

    /**
     * Get itemname
     *
     * @return string
     */
    public function getItemname()
    {
        return $this->itemname;
    }

    /**
     * Set security
     *
     * @param float $security
     *
     * @return Mapdenormalize
     */
    public function setSecurity($security)
    {
        $this->security = $security;

        return $this;
    }

    /**
     * Get security
     *
     * @return float
     */
    public function getSecurity()
    {
        return $this->security;
    }

    /**
     * Set celestialindex
     *
     * @param integer $celestialindex
     *
     * @return Mapdenormalize
     */
    public function setCelestialindex($celestialindex)
    {
        $this->celestialindex = $celestialindex;

        return $this;
    }

    /**
     * Get celestialindex
     *
     * @return integer
     */
    public function getCelestialindex()
    {
        return $this->celestialindex;
    }

    /**
     * Set orbitindex
     *
     * @param integer $orbitindex
     *
     * @return Mapdenormalize
     */
    public function setOrbitindex($orbitindex)
    {
        $this->orbitindex = $orbitindex;

        return $this;
    }

    /**
     * Get orbitindex
     *
     * @return integer
     */
    public function getOrbitindex()
    {
        return $this->orbitindex;
    }

    /**
     * Set regionid
     *
     * @param \Application\Entity\Maplocationwormholeclasses $regionid
     *
     * @return Mapdenormalize
     */
    public function setRegionid(\Application\Entity\Maplocationwormholeclasses $regionid = null)
    {
        $this->regionid = $regionid;

        return $this;
    }

    /**
     * Get regionid
     *
     * @return \Application\Entity\Maplocationwormholeclasses
     */
    public function getRegionid()
    {
        return $this->regionid;
    }
}
