<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Invitems
 *
 * @ORM\Table(name="invItems", 
 *	indexes={
 *		@ORM\Index(name="ix_ii_locationid", columns={"locationID"}), 
 *		@ORM\Index(name="ix_ii_ownerid_locationid", columns={"ownerID", "locationID"})})
 * @ORM\Entity
 */
class Invitems
{
    /**
     * @var integer
     *
     * @ORM\Column(name="itemID", type="bigint", nullable=false)
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
     * @ORM\Column(name="ownerID", type="integer", nullable=true)
     */
    private $ownerid;

    /**
     * @var integer
     *
     * @ORM\Column(name="locationID", type="bigint", nullable=true)
     */
    private $locationid;

    /**
     * @var integer
     *
     * @ORM\Column(name="flagID", type="smallint", nullable=true)
     */
    private $flagid;

    /**
     * @var integer
     *
     * @ORM\Column(name="quantity", type="integer", nullable=true)
     */
    private $quantity;



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
     * @return Invitems
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
     * Set ownerid
     *
     * @param integer $ownerid
     *
     * @return Invitems
     */
    public function setOwnerid($ownerid)
    {
        $this->ownerid = $ownerid;

        return $this;
    }

    /**
     * Get ownerid
     *
     * @return integer
     */
    public function getOwnerid()
    {
        return $this->ownerid;
    }

    /**
     * Set locationid
     *
     * @param integer $locationid
     *
     * @return Invitems
     */
    public function setLocationid($locationid)
    {
        $this->locationid = $locationid;

        return $this;
    }

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
     * Set flagid
     *
     * @param integer $flagid
     *
     * @return Invitems
     */
    public function setFlagid($flagid)
    {
        $this->flagid = $flagid;

        return $this;
    }

    /**
     * Get flagid
     *
     * @return integer
     */
    public function getFlagid()
    {
        return $this->flagid;
    }

    /**
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return Invitems
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
