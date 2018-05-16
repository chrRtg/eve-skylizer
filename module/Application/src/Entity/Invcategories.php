<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Invcategories
 *
 * @ORM\Table(name="invCategories")
 * @ORM\Entity
 */
class Invcategories
{
    /**
     * @var integer
     *
     * @ORM\Column(name="categoryID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $categoryid;

    /**
     * @var string
     *
     * @ORM\Column(name="categoryName", type="string", length=100, nullable=true)
     */
    private $categoryname;

    /**
     * @var integer
     *
     * @ORM\Column(name="iconID", type="integer", nullable=true)
     */
    private $iconid;

    /**
     * @var boolean
     *
     * @ORM\Column(name="published", type="boolean", nullable=true)
     */
    private $published;



    /**
     * Get categoryid
     *
     * @return integer
     */
    public function getCategoryid()
    {
        return $this->categoryid;
    }

    /**
     * Set categoryname
     *
     * @param string $categoryname
     *
     * @return Invcategories
     */
    public function setCategoryname($categoryname)
    {
        $this->categoryname = $categoryname;

        return $this;
    }

    /**
     * Get categoryname
     *
     * @return string
     */
    public function getCategoryname()
    {
        return $this->categoryname;
    }

    /**
     * Set iconid
     *
     * @param integer $iconid
     *
     * @return Invcategories
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
     * Set published
     *
     * @param boolean $published
     *
     * @return Invcategories
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
}
