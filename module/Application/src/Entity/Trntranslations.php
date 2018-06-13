<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Trntranslations
 *
 * @ORM\Table(name="trnTranslations", 
 *	indexes={
 *		@ORM\Index(name="ix_tl_tcid", columns={"tcID"}), 
 *		@ORM\Index(name="ix_tl_kid", columns={"keyID"}), 
 *		@ORM\Index(name="ix_tl_text", columns={"text"}), 
 *		@ORM\Index(name="ix_tl_lang", columns={"languageID"})})
 * @ORM\Entity
 */
class Trntranslations
{
    /**
     * @var integer
     *
     * @ORM\Column(name="tcID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $tcid;

    /**
     * @var integer
     *
     * @ORM\Column(name="keyID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $keyid;

    /**
     * @var string
     *
     * @ORM\Column(name="languageID", type="string", length=50, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $languageid;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text", length=65535, nullable=false)
     */
    private $text;



    /**
     * Set tcid
     *
     * @param integer $tcid
     *
     * @return Trntranslations
     */
    public function setTcid($tcid)
    {
        $this->tcid = $tcid;

        return $this;
    }

    /**
     * Get tcid
     *
     * @return integer
     */
    public function getTcid()
    {
        return $this->tcid;
    }

    /**
     * Set keyid
     *
     * @param integer $keyid
     *
     * @return Trntranslations
     */
    public function setKeyid($keyid)
    {
        $this->keyid = $keyid;

        return $this;
    }

    /**
     * Get keyid
     *
     * @return integer
     */
    public function getKeyid()
    {
        return $this->keyid;
    }

    /**
     * Set languageid
     *
     * @param string $languageid
     *
     * @return Trntranslations
     */
    public function setLanguageid($languageid)
    {
        $this->languageid = $languageid;

        return $this;
    }

    /**
     * Get languageid
     *
     * @return string
     */
    public function getLanguageid()
    {
        return $this->languageid;
    }

    /**
     * Set text
     *
     * @param string $text
     *
     * @return Trntranslations
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }
}
