<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Invmetatypes
 *
 * @ORM\Table(name="invMetaTypes")
 * @ORM\Entity
 */
class Invmetatypes
{
    /**
     * @var integer
     *
     * @ORM\Column(name="typeID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $typeid;

    /**
     * @var integer
     *
     * @ORM\Column(name="parentTypeID", type="integer", nullable=true)
     */
    private $parenttypeid;

    /**
     * @var integer
     *
     * @ORM\Column(name="metaGroupID", type="smallint", nullable=true)
     */
    private $metagroupid;



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
     * Set parenttypeid
     *
     * @param integer $parenttypeid
     *
     * @return Invmetatypes
     */
    public function setParenttypeid($parenttypeid)
    {
        $this->parenttypeid = $parenttypeid;

        return $this;
    }

    /**
     * Get parenttypeid
     *
     * @return integer
     */
    public function getParenttypeid()
    {
        return $this->parenttypeid;
    }

    /**
     * Set metagroupid
     *
     * @param integer $metagroupid
     *
     * @return Invmetatypes
     */
    public function setMetagroupid($metagroupid)
    {
        $this->metagroupid = $metagroupid;

        return $this;
    }

    /**
     * Get metagroupid
     *
     * @return integer
     */
    public function getMetagroupid()
    {
        return $this->metagroupid;
    }
}
