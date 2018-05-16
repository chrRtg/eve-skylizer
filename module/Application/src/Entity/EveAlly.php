<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EveAlly
 *
 * @ORM\Table(name="eve_ally")
 * @ORM\Entity
 */
class EveAlly
{
    /**
     * @var integer
     *
     * @ORM\Column(name="alliance_id", type="integer", nullable=false, options={"default":0})
     * @ORM\Id
     */
    private $allianceId = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="alliance_name", type="string", length=128, nullable=false, options={"default":""})
     */
    private $allianceName = '';

    /**
     * @var string
     *
     * @ORM\Column(name="ticker", type="string", length=5, nullable=false, options={"default":"", "fixed":true})
     */
    private $ticker = '';



    /**
     * Get allianceId
     *
     * @return integer
     */
    public function getAllianceId()
    {
        return $this->allianceId;
    }

    /**
     * Set allianceName
     *
     * @param string $allianceName
     *
     * @return EveAlly
     */
    public function setAllianceName($allianceName)
    {
        $this->allianceName = $allianceName;

        return $this;
    }

    /**
     * Get allianceName
     *
     * @return string
     */
    public function getAllianceName()
    {
        return $this->allianceName;
    }

    /**
     * Set ticker
     *
     * @param string $ticker
     *
     * @return EveAlly
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
