<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Invnames
 *
 * @ORM\Table(name="invNames")
 * @ORM\Entity
 */
class Invnames
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
     * @var string
     *
     * @ORM\Column(name="itemName", type="text", nullable=true)
     */
    private $itemname;



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
     * Set itemname
     *
     * @param string $itemname
     *
     * @return Invnames
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
}
