<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * TempBcbpProfile
 *
 * @ORM\Table(name="tbl_temp_bcbp_profile")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TempBcbpProfileRepository")
 */
class TempBcbpProfile
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

     /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $firstname;

       /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $lastname;


    /**
     * @var string
     *
     * @ORM\Column(name="middlename", type="string", length=255)
     */
    private $middlename;
    
    /**
     * @var string
     *
     * @ORM\Column(name="nickname", type="string", length=255)
     */
    private $nickname;

    /**
     * @var string
     *
     * @ORM\Column(name="group_name", type="string", length=150)
     */
    private $groupName;

    /**
     * @var string
     *
     * @ORM\Column(name="batch_name", type="string", length=150)
     * @Assert\NotBlank()
     */
    private $batchName;

     /**
     * @var string
     *
     * @ORM\Column(name="chapter_name", type="string", length=150)
     * @Assert\NotBlank()
     */
    private $chapterName;
    
     /**
     * @var string
     *
     * @ORM\Column(name="unit_name", type="string", length=150)
     */
    private $unitName;
     
     /**
     * @var string
     *
     * @ORM\Column(name="position", type="string", length=150)
     */
    private $position;

      /**
     * @var string
     *
     * @ORM\Column(name="couple_name", type="string", length=150)
     */
    private $coupleName;

    /**
     * @var string
     *
     * @ORM\Column(name="contact_number", type="string", length=30)
     * @Assert\NotBlank()
     */
    private $contactNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="source_number", type="string", length=30)
     */
    private $sourceNumber;
    
    /**
     * @var string
     *
     * @ORM\Column(name="gender", type="string", length=15)
     * @Assert\NotBlank()
     */
    private $gender;

    /**
     * @var string
     *
     * @ORM\Column(name="birthdate", type="string", length=15)
     */
    private $birthdate;

    /**
     * @var datetime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var string
     *
     * @ORM\Column(name="created_by", type="string", length=150)
     */
    private $createdBy;

    /**
     * @var string
     *
     * @ORM\Column(name="remarks", type="string", length=256)
     */
    private $remarks;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=3)
     */
    private $status;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return TempBcbpProfile
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     *
     * @return TempBcbpProfile
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     *
     * @return TempBcbpProfile
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set middlename
     *
     * @param string $middlename
     *
     * @return TempBcbpProfile
     */
    public function setMiddlename($middlename)
    {
        $this->middlename = $middlename;

        return $this;
    }

    /**
     * Get middlename
     *
     * @return string
     */
    public function getMiddlename()
    {
        return $this->middlename;
    }

    /**
     * Set groupName
     *
     * @param string $groupName
     *
     * @return TempBcbpProfile
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
     * Set batchName
     *
     * @param string $batchName
     *
     * @return TempBcbpProfile
     */
    public function setBatchName($batchName)
    {
        $this->batchName = $batchName;

        return $this;
    }

    /**
     * Get batchName
     *
     * @return string
     */
    public function getBatchName()
    {
        return $this->batchName;
    }

    /**
     * Set chapterName
     *
     * @param string $chapterName
     *
     * @return TempBcbpProfile
     */
    public function setChapterName($chapterName)
    {
        $this->chapterName = $chapterName;

        return $this;
    }

    /**
     * Get chapterName
     *
     * @return string
     */
    public function getChapterName()
    {
        return $this->chapterName;
    }

    /**
     * Set contactNumber
     *
     * @param string $contactNumber
     *
     * @return TempBcbpProfile
     */
    public function setContactNumber($contactNumber)
    {
        $this->contactNumber = $contactNumber;

        return $this;
    }

    /**
     * Get contactNumber
     *
     * @return string
     */
    public function getContactNumber()
    {
        return $this->contactNumber;
    }

    /**
     * Set gender
     *
     * @param string $gender
     *
     * @return TempBcbpProfile
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender
     *
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set birthdate
     *
     * @param string $birthdate
     *
     * @return TempBcbpProfile
     */
    public function setBirthdate($birthdate)
    {
        $this->birthdate = $birthdate;

        return $this;
    }

    /**
     * Get birthdate
     *
     * @return string
     */
    public function getBirthdate()
    {
        return $this->birthdate;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return TempBcbpProfile
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set createdBy
     *
     * @param string $createdBy
     *
     * @return TempBcbpProfile
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return string
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set remarks
     *
     * @param string $remarks
     *
     * @return TempBcbpProfile
     */
    public function setRemarks($remarks)
    {
        $this->remarks = $remarks;

        return $this;
    }

    /**
     * Get remarks
     *
     * @return string
     */
    public function getRemarks()
    {
        return $this->remarks;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return TempBcbpProfile
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set sourceNumber
     *
     * @param string $sourceNumber
     *
     * @return TempBcbpProfile
     */
    public function setSourceNumber($sourceNumber)
    {
        $this->sourceNumber = $sourceNumber;

        return $this;
    }

    /**
     * Get sourceNumber
     *
     * @return string
     */
    public function getSourceNumber()
    {
        return $this->sourceNumber;
    }

    /**
     * Set unitName
     *
     * @param string $unitName
     *
     * @return TempBcbpProfile
     */
    public function setUnitName($unitName)
    {
        $this->unitName = $unitName;

        return $this;
    }

    /**
     * Get unitName
     *
     * @return string
     */
    public function getUnitName()
    {
        return $this->unitName;
    }

    /**
     * Set coupleName
     *
     * @param string $coupleName
     *
     * @return TempBcbpProfile
     */
    public function setCoupleName($coupleName)
    {
        $this->coupleName = $coupleName;

        return $this;
    }

    /**
     * Get coupleName
     *
     * @return string
     */
    public function getCoupleName()
    {
        return $this->coupleName;
    }

    /**
     * Set nickname
     *
     * @param string $nickname
     *
     * @return TempBcbpProfile
     */
    public function setNickname($nickname)
    {
        $this->nickname = $nickname;

        return $this;
    }

    /**
     * Get nickname
     *
     * @return string
     */
    public function getNickname()
    {
        return $this->nickname;
    }

    /**
     * Set position
     *
     * @param string $position
     *
     * @return TempBcbpProfile
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return string
     */
    public function getPosition()
    {
        return $this->position;
    }
}
