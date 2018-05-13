<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EveCorporation
 *
 * @ORM\Table(name="eve_corporation", indexes={@ORM\Index(name="k_ticker", columns={"ticker"})})
 * @ORM\Entity
 */
class EveCorporation
{
    /**
     * @var integer
     *
     * @ORM\Column(name="corporation_id", type="integer", nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="NONE")     
	 */
    private $corporationId = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="corporation_name", type="string", length=120, nullable=false)
     */
    private $corporationName = '';

    /**
     * @var string
     *
     * @ORM\Column(name="ticker", type="string", length=5, nullable=false)
     */
    private $ticker = '';

	
	public function setCorporationId($corporationId)
    {
		$this->corporationId = $corporationId;

        return $this;
    }

    /**
     * Get corporationId
     *
     * @return integer
     */
    public function getCorporationId()
    {
        return $this->corporationId;
    }

    /**
     * Set corporationName
     *
     * @param string $corporationName
     *
     * @return EveCorporation
     */
    public function setCorporationName($corporationName)
    {
        $this->corporationName = $corporationName;

        return $this;
    }

    /**
     * Get corporationName
     *
     * @return string
     */
    public function getCorporationName()
    {
        return $this->corporationName;
    }

    /**
     * Set ticker
     *
     * @param string $ticker
     *
     * @return EveCorporation
     */
    public function setTicker($ticker)
    {
        $this->ticker = $ticker;

        return $this;
    }

    /**
     * Get ticker
     *
     * @return string
     */
    public function getTicker()
    {
        return $this->ticker;
    }
}
