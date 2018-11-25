<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EveCorporation
 *
 * @ORM\Table(name="eve_corporation",
 *        indexes={
 * @ORM\Index(name="ix_c_ticker",     columns={"ticker"}),
 * @ORM\Index(name="ix_c_name",       columns={"corporation_name"}),
 * @ORM\Index(name="ix_c_alliance",   columns={"alliance_id"})
 *        })
 * @ORM\Entity
 */
class EveCorporation
{
    /**
     * @var integer
     *
     * @ORM\Column(name="corporation_id",   type="integer", nullable=false, options={"default":0})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $corporationId = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="corporation_name", type="string", length=120, nullable=false, options={"default":""})
     */
    private $corporationName = '';

    /**
     * @var string
     *
     * @ORM\Column(name="ticker", type="string", length=5, nullable=false, options={"default":"", "fixed":true})
     */
    private $ticker = '';

    /**
     * @var integer
     *
     * @ORM\Column(name="alliance_id", type="integer", nullable=false, options={"default":0})
     */
    private $allianceId = '0';


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

    /**
     * Set allianceId
     *
     * @return EveCorporation
     */
    public function setAllianceId($allianceId)
    {
        $this->allianceId = $allianceId;

        return $this;
    }

    /**
     * Get allianceId
     *
     * @return integer
     */
    public function getAllianceId()
    {
        return $this->allianceId;
    }    

}
