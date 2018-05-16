<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Invmetagroups
 *
 * @ORM\Table(name="invMetaGroups")
 * @ORM\Entity
 */
class Invmetagroups
{
    /**
     * @var integer
     *
     * @ORM\Column(name="metaGroupID", type="smallint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $metagroupid;

    /**
     * @var string
     *
     * @ORM\Column(name="metaGroupName", type="text", nullable=true)
     */
    private $metagroupname;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var integer
     *
     * @ORM\Column(name="iconID", type="integer", nullable=true)
     */
    private $iconid;



    /**
     * Get metagroupid
     *
     * @return integer
     */
    public function getMetagroupid()
    {
        return $this->metagroupid;
    }

    /**
     * Set metagroupname
     *
     * @param string $metagroupname
     *
     * @return Invmetagroups
     */
    public function setMetagroupname($metagroupname)
    {
        $this->metagroupname = $metagroupname;

        return $this;
    }

    /**
     * Get metagroupname
     *
     * @return string
     */
    public function getMetagroupname()
    {
        return $this->metagroupname;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Invmetagroups
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
     * @return Invmetagroups
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
}
