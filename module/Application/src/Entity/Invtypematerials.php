<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Invtypematerials
 *
 * @ORM\Table(name="invTypeMaterials", 
 *	indexes={
 *		@ORM\Index(name="ix_tm_typeid", columns={"typeID"}), 
 *		@ORM\Index(name="ix_tm_mtypeid", columns={"materialTypeID"})})
 * @ORM\Entity
 */
class Invtypematerials
{
    /**
     * @var integer
     *
     * @ORM\Column(name="typeID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $typeid;

    /**
     * @var integer
     *
     * @ORM\Column(name="materialTypeID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $materialtypeid;

    /**
     * @var integer
     *
     * @ORM\Column(name="quantity", type="integer", nullable=false)
     */
    private $quantity;



    /**
     * Set typeid
     *
     * @param integer $typeid
     *
     * @return Invtypematerials
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
     * Set materialtypeid
     *
     * @param integer $materialtypeid
     *
     * @return Invtypematerials
     */
    public function setMaterialtypeid($materialtypeid)
    {
        $this->materialtypeid = $materialtypeid;

        return $this;
    }

    /**
     * Get materialtypeid
     *
     * @return integer
     */
    public function getMaterialtypeid()
    {
        return $this->materialtypeid;
    }

    /**
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return Invtypematerials
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return integer
     */
    public function getQuantity()
    {
        return $this->quantity;
    }
}
