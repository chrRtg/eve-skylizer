<?php

namespace VposMoon\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AtCosmicDetail
 *
 * @ORM\Table(name="at_cosmic_detail", indexes={@ORM\Index(name="idx_cosmic_main_id", columns={"cosmic_main_id"}), @ORM\Index(name="idx_name_en", columns={"typeName"}), @ORM\Index(name="idx_name_de", columns={"typeName_de"}), @ORM\Index(name="idx_type", columns={"type"}), @ORM\Index(name="idx_class", columns={"class"})})
 * @ORM\Entity
 */
class AtCosmicDetail
{
    /**
     * @var integer
     *
     * @ORM\Column(name="cosmic_detail_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $cosmicDetailId;

    /**
     * @var string
     *
     * @ORM\Column(name="typeName", type="string", length=100, nullable=true)
     */
    private $typename;

    /**
     * @var string
     *
     * @ORM\Column(name="typeName_de", type="string", length=100, nullable=true)
     */
    private $typenameDe;

    /**
     * @var string
     *
     * @ORM\Column(name="url_en", type="string", length=250, nullable=true)
     */
    private $urlEn;

    /**
     * @var string
     *
     * @ORM\Column(name="url_de", type="string", length=255, nullable=true)
     */
    private $urlDe;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=40, nullable=true)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="class", type="string", length=40, nullable=true)
     */
    private $class;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="string", length=255, nullable=true)
     */
    private $comment;

    /**
     * @var \VposMoon\Entity\AtCosmicMain
     *
     * @ORM\ManyToOne(targetEntity="VposMoon\Entity\AtCosmicMain")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cosmic_main_id", referencedColumnName="cosmic_main_id")
     * })
     */
    private $cosmicMain;



    /**
     * Get cosmicDetailId
     *
     * @return integer
     */
    public function getCosmicDetailId()
    {
        return $this->cosmicDetailId;
    }

    /**
     * Set typename
     *
     * @param string $typename
     *
     * @return AtCosmicDetail
     */
    public function setTypename($typename)
    {
        $this->typename = $typename;

        return $this;
    }

    /**
     * Get typename
     *
     * @return string
     */
    public function getTypename()
    {
        return $this->typename;
    }

    /**
     * Set typenameDe
     *
     * @param string $typenameDe
     *
     * @return AtCosmicDetail
     */
    public function setTypenameDe($typenameDe)
    {
        $this->typenameDe = $typenameDe;

        return $this;
    }

    /**
     * Get typenameDe
     *
     * @return string
     */
    public function getTypenameDe()
    {
        return $this->typenameDe;
    }

    /**
     * Set urlEn
     *
     * @param string $urlEn
     *
     * @return AtCosmicDetail
     */
    public function setUrlEn($urlEn)
    {
        $this->urlEn = $urlEn;

        return $this;
    }

    /**
     * Get urlEn
     *
     * @return string
     */
    public function getUrlEn()
    {
        return $this->urlEn;
    }

    /**
     * Set urlDe
     *
     * @param string $urlDe
     *
     * @return AtCosmicDetail
     */
    public function setUrlDe($urlDe)
    {
        $this->urlDe = $urlDe;

        return $this;
    }

    /**
     * Get urlDe
     *
     * @return string
     */
    public function getUrlDe()
    {
        return $this->urlDe;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return AtCosmicDetail
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set class
     *
     * @param string $class
     *
     * @return AtCosmicDetail
     */
    public function setClass($class)
    {
        $this->class = $class;

        return $this;
    }

    /**
     * Get class
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Set comment
     *
     * @param string $comment
     *
     * @return AtCosmicDetail
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set cosmicMain
     *
     * @param \VposMoon\Entity\AtCosmicMain $cosmicMain
     *
     * @return AtCosmicDetail
     */
    public function setCosmicMain(\VposMoon\Entity\AtCosmicMain $cosmicMain = null)
    {
        $this->cosmicMain = $cosmicMain;

        return $this;
    }

    /**
     * Get cosmicMain
     *
     * @return \VposMoon\Entity\AtCosmicMain
     */
    public function getCosmicMain()
    {
        return $this->cosmicMain;
    }
}
