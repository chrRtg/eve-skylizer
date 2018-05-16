<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Maplocationwormholeclasses
 *
 * @ORM\Table(name="mapLocationWormholeClasses")
 * @ORM\Entity
 */
class Maplocationwormholeclasses
{
    /**
     * @var integer
     *
     * @ORM\Column(name="locationID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $locationid;

    /**
     * @var integer
     *
     * @ORM\Column(name="wormholeClassID", type="integer", nullable=true)
     */
    private $wormholeclassid;



    /**
     * Get locationid
     *
     * @return integer
     */
    public function getLocationid()
    {
        return $this->locationid;
    }

    /**
     * Set wormholeclassid
     *
     * @param integer $wormholeclassid
     *
     * @return Maplocationwormholeclasses
     */
    public function setWormholeclassid($wormholeclassid)
    {
        $this->wormholeclassid = $wormholeclassid;

        return $this;
    }

    /**
     * Get wormholeclassid
     *
     * @return integer
     */
    public function getWormholeclassid()
    {
        return $this->wormholeclassid;
    }
}
