<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Invmarketgroups
 *
 * @ORM\Table(name="invMarketGroups", 
 *        indexes={
 * @ORM\Index(name="idx_mg_parent",   columns={"parentGroupID"}),
 * @ORM\Index(name="idx_mg_name",     columns={"marketGroupName"})
 *    })
 * @ORM\Entity
 */
class Invmarketgroups
{
    /**
     * @var integer
     *
     * @ORM\Column(name="marketGroupID",    type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $marketgroupid;

    /**
     * @var integer
     *
     * @ORM\Column(name="parentGroupID", type="integer", nullable=true)
     */
    private $parentgroupid;

    /**
     * @var string
     *
     * @ORM\Column(name="marketGroupName", type="string", length=100, nullable=true)
     */
    private $marketgroupname;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=3000, nullable=true)
     */
    private $description;

    /**
     * @var integer
     *
     * @ORM\Column(name="iconID", type="integer", nullable=true)
     */
    private $iconid;

    /**
     * @var boolean
     *
     * @ORM\Column(name="hasTypes", type="boolean", nullable=true)
     */
    private $hastypes;



    /**
     * Get marketgroupid
     *
     * @return integer
     */
    public function getMarketgroupid()
    {
        return $this->marketgroupid;
    }

    /**
     * Set parentgroupid
     *
     * @param integer $parentgroupid
     *
     * @return Invmarketgroups
     */
    public function setParentgroupid($parentgroupid)
    {
        $this->parentgroupid = $parentgroupid;

        return $this;
    }

    /**
     * Get parentgroupid
     *
     * @return integer
     */
    public function getParentgroupid()
    {
        return $this->parentgroupid;
    }

    /**
     * Set marketgroupname
     *
     * @param string $marketgroupname
     *
     * @return Invmarketgroups
     */
    public function setMarketgroupname($marketgroupname)
    {
        $this->marketgroupname = $marketgroupname;

        return $this;
    }

    /**
     * Get marketgroupname
     *
     * @return string
     */
    public function getMarketgroupname()
    {
        return $this->marketgroupname;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Invmarketgroups
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set iconid
     *
     * @param integer $iconid
     *
     * @return Invmarketgroups
     */
    public function setIconid($iconid)
    {
        $this->iconid = $iconid;

        return $this;
    }

    /**
     * Get iconid
     *
     * @return integer
     */
    public function getIconid()
    {
        return $this->iconid;
    }

    /**
     * Set hastypes
     *
     * @param boolean $hastypes
     *
     * @return Invmarketgroups
     */
    public function setHastypes($hastypes)
    {
        $this->hastypes = $hastypes;

        return $this;
    }

    /**
     * Get hastypes
     *
     * @return boolean
     */
    public function getHastypes()
    {
        return $this->hastypes;
    }
}
