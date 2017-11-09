<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Invgroups
 *
 * @ORM\Table(name="invGroups", indexes={@ORM\Index(name="ix_invGroups_categoryID", columns={"categoryID"})})
 * @ORM\Entity
 */
class Invgroups
{
    /**
     * @var integer
     *
     * @ORM\Column(name="groupID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $groupid;

    /**
     * @var string
     *
     * @ORM\Column(name="groupName", type="string", length=100, nullable=true)
     */
    private $groupname;

    /**
     * @var integer
     *
     * @ORM\Column(name="iconID", type="integer", nullable=true)
     */
    private $iconid;

    /**
     * @var boolean
     *
     * @ORM\Column(name="useBasePrice", type="boolean", nullable=true)
     */
    private $usebaseprice;

    /**
     * @var boolean
     *
     * @ORM\Column(name="anchored", type="boolean", nullable=true)
     */
    private $anchored;

    /**
     * @var boolean
     *
     * @ORM\Column(name="anchorable", type="boolean", nullable=true)
     */
    private $anchorable;

    /**
     * @var boolean
     *
     * @ORM\Column(name="fittableNonSingleton", type="boolean", nullable=true)
     */
    private $fittablenonsingleton;

    /**
     * @var boolean
     *
     * @ORM\Column(name="published", type="boolean", nullable=true)
     */
    private $published;

    /**
     * @var \Application\Entity\Invcategories
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Invcategories")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="categoryID", referencedColumnName="categoryID")
     * })
     */
    private $categoryid;



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
     * Set groupname
     *
     * @param string $groupname
     *
     * @return Invgroups
     */
    public function setGroupname($groupname)
    {
        $this->groupname = $groupname;

        return $this;
    }

    /**
     * Get groupname
     *
     * @return string
     */
    public function getGroupname()
    {
        return $this->groupname;
    }

    /**
     * Set iconid
     *
     * @param integer $iconid
     *
     * @return Invgroups
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
     * Set usebaseprice
     *
     * @param boolean $usebaseprice
     *
     * @return Invgroups
     */
    public function setUsebaseprice($usebaseprice)
    {
        $this->usebaseprice = $usebaseprice;

        return $this;
    }

    /**
     * Get usebaseprice
     *
     * @return boolean
     */
    public function getUsebaseprice()
    {
        return $this->usebaseprice;
    }

    /**
     * Set anchored
     *
     * @param boolean $anchored
     *
     * @return Invgroups
     */
    public function setAnchored($anchored)
    {
        $this->anchored = $anchored;

        return $this;
    }

    /**
     * Get anchored
     *
     * @return boolean
     */
    public function getAnchored()
    {
        return $this->anchored;
    }

    /**
     * Set anchorable
     *
     * @param boolean $anchorable
     *
     * @return Invgroups
     */
    public function setAnchorable($anchorable)
    {
        $this->anchorable = $anchorable;

        return $this;
    }

    /**
     * Get anchorable
     *
     * @return boolean
     */
    public function getAnchorable()
    {
        return $this->anchorable;
    }

    /**
     * Set fittablenonsingleton
     *
     * @param boolean $fittablenonsingleton
     *
     * @return Invgroups
     */
    public function setFittablenonsingleton($fittablenonsingleton)
    {
        $this->fittablenonsingleton = $fittablenonsingleton;

        return $this;
    }

    /**
     * Get fittablenonsingleton
     *
     * @return boolean
     */
    public function getFittablenonsingleton()
    {
        return $this->fittablenonsingleton;
    }

    /**
     * Set published
     *
     * @param boolean $published
     *
     * @return Invgroups
     */
    public function setPublished($published)
    {
        $this->published = $published;

        return $this;
    }

    /**
     * Get published
     *
     * @return boolean
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * Set categoryid
     *
     * @param \Application\Entity\Invcategories $categoryid
     *
     * @return Invgroups
     */
    public function setCategoryid(\Application\Entity\Invcategories $categoryid = null)
    {
        $this->categoryid = $categoryid;

        return $this;
    }

    /**
     * Get categoryid
     *
     * @return \Application\Entity\Invcategories
     */
    public function getCategoryid()
    {
        return $this->categoryid;
    }
}
