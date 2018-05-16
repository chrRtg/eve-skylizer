<?php

namespace VposMoon\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AtCosmicMain
 *
 * @ORM\Table(name="at_cosmic_main", indexes={@ORM\Index(name="idx_name_en", columns={"group_name"}), @ORM\Index(name="idx_name_de", columns={"group_name_de"}), @ORM\Index(name="idx_groupID", columns={"groupID"}), @ORM\Index(name="idx_categoryID", columns={"categoryID"}), @ORM\Index(name="idx_type_en", columns={"type"}), @ORM\Index(name="idx_type_de", columns={"type_de"})})
 * @ORM\Entity
 */
class AtCosmicMain
{
    /**
     * @var integer
     *
     * @ORM\Column(name="cosmic_main_id", type="integer", length=11, nullable=false)
     * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $cosmicMainId;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=100, nullable=true)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="group_name", type="string", length=100, nullable=true)
     */
    private $groupName;

    /**
     * @var string
     *
     * @ORM\Column(name="type_de", type="string", length=100, nullable=true)
     */
    private $typeDe;

    /**
     * @var string
     *
     * @ORM\Column(name="group_name_de", type="string", length=100, nullable=true)
     */
    private $groupNameDe;

    /**
     * @var \Application\Entity\Invcategories
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Invcategories")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="categoryID", referencedColumnName="categoryID")
     * })
     */
    private $categoryid;

    /**
     * @var \Application\Entity\Invgroups
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Invgroups")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="groupID", referencedColumnName="groupID")
     * })
     */
    private $groupid;



    /**
     * Get cosmicMainId
     *
     * @return integer
     */
    public function getCosmicMainId()
    {
        return $this->cosmicMainId;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return AtCosmicMain
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
     * Set groupName
     *
     * @param string $groupName
     *
     * @return AtCosmicMain
     */
    public function setGroupName($groupName)
    {
        $this->groupName = $groupName;

        return $this;
    }

    /**
     * Get groupName
     *
     * @return string
     */
    public function getGroupName()
    {
        return $this->groupName;
    }

    /**
     * Set typeDe
     *
     * @param string $typeDe
     *
     * @return AtCosmicMain
     */
    public function setTypeDe($typeDe)
    {
        $this->typeDe = $typeDe;

        return $this;
    }

    /**
     * Get typeDe
     *
     * @return string
     */
    public function getTypeDe()
    {
        return $this->typeDe;
    }

    /**
     * Set groupNameDe
     *
     * @param string $groupNameDe
     *
     * @return AtCosmicMain
     */
    public function setGroupNameDe($groupNameDe)
    {
        $this->groupNameDe = $groupNameDe;

        return $this;
    }

    /**
     * Get groupNameDe
     *
     * @return string
     */
    public function getGroupNameDe()
    {
        return $this->groupNameDe;
    }

    /**
     * Set categoryid
     *
     * @param \Application\Entity\Invcategories $categoryid
     *
     * @return AtCosmicMain
     */
    public function setCategoryid(\Application\Entity\Invcategories $categoryid = null)
    {
        $this->categoryid = $categoryid;

        return $this;
    }

    /**
     * Get categoryid
     *
     * @return \Application\Entity\Invcategories
     */
    public function getCategoryid()
    {
        return $this->categoryid;
    }

    /**
     * Set groupid
     *
     * @param \Application\Entity\Invgroups $groupid
     *
     * @return AtCosmicMain
     */
    public function setGroupid(\Application\Entity\Invgroups $groupid = null)
    {
        $this->groupid = $groupid;

        return $this;
    }

    /**
     * Get groupid
     *
     * @return \Application\Entity\Invgroups
     */
    public function getGroupid()
    {
        return $this->groupid;
    }
}
