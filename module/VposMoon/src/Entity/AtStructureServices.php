<?php

namespace VposMoon\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AtStructureServices
 *
 * @ORM\Table(name="at_structure_services", indexes={@ORM\Index(name="idx_structure_id", columns={"structure_id"}), @ORM\Index(name="idx_state", columns={"state"}), @ORM\Index(name="idx_service", columns={"service"})})
 * @ORM\Entity
 */
class AtStructureServices
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="structure_id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $structureId;

    /**
     * @var string
     *
     * @ORM\Column(name="service", type="string", length=120, precision=0, scale=0, nullable=false, unique=false)
     */
    private $service;

    /**
     * @var string
     *
     * @ORM\Column(name="state", type="string", length=45, precision=0, scale=0, nullable=false, unique=false)
     */
    private $state;


    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set structureId.
     *
     * @param int $structureId
     *
     * @return AtStructureServices
     */
    public function setStructureId($structureId)
    {
        $this->structureId = $structureId;

        return $this;
    }

    /**
     * Get structureId.
     *
     * @return int
     */
    public function getStructureId()
    {
        return $this->structureId;
    }

    /**
     * Set service.
     *
     * @param string $service
     *
     * @return AtStructureServices
     */
    public function setService($service)
    {
        $this->service = $service;

        return $this;
    }

    /**
     * Get service.
     *
     * @return string
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * Set state.
     *
     * @param string $state
     *
     * @return AtStructureServices
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state.
     *
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }
}
