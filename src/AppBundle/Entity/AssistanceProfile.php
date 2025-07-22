<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * AssistanceProfile
 *
 * @ORM\Table(name="tbl_assistance_profile")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AssistanceProfileRepository")
 * @UniqueEntity(fields={"voterName"},message="Voter name already exists.", errorPath="proVoterId")
 * @UniqueEntity(fields={"fullname"},message="Voter name already exists.", errorPath="fullname")
 */
class AssistanceProfile
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
     * @ORM\Column(name="profile_id", type="string", length=50)
     */
    private $profile_id;

    /**
     * @var string
     *
     * @ORM\Column(name="district", type="string", length=15)
     * @Assert\NotBlank()
     */
    private $district;

     /**
     * @var string
     *
     * @ORM\Column(name="municipality_name", type="string", length=150)
     */
    private $municipalityName;

    /**
     * @var string
     *
     * @ORM\Column(name="municipality_no", type="string", length=30)
     * @Assert\NotBlank()
     * 
     */
    private $municipalityNo;

    /**
     * @var string
     *
     * @ORM\Column(name="barangay_name", type="string", length=150)
     */
    private $barangayName;

    /**
     * @var string
     *
     * @ORM\Column(name="barangay_no", type="string", length=30)
     * @Assert\NotBlank()
     */
    private $barangayNo;

     /**
     * @var string
     *
     * @ORM\Column(name="purok", type="string", length=30)
     */
    private $purok;

    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=150)
     * @Assert\NotBlank()
     */
    private $firstname;

     /**
     * @var string
     *
     * @ORM\Column(name="middlename", type="string", length=150)
     * @Assert\NotBlank()
     */
    private $middlename;


     /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=150)
     * @Assert\NotBlank()
     */
    private $lastname;

    /**
     * @var string
     *
     * @ORM\Column(name="extname", type="string", length=30)
     */
    private $extname;

     /**
     * @var string
     *
     * @ORM\Column(name="fullname", type="string", length=255)
     */
    private $fullname;

    /**
     * @var string
     *
     * @ORM\Column(name="birthdate", type="string", length=30)
     * @Assert\NotBlank()
     */
    private $birthdate;

    /**
     * @var string
     *
     * @ORM\Column(name="contact_no", type="string", length=30)
     */
    private $contactNo;

     /**
     * @var string
     *
     * @ORM\Column(name="pro_voter_id", type="integer")
     */
    private $proVoterId;

    /**
     * @var string
     *
     * @ORM\Column(name="pro_id_code", type="string", length=30)
     */
    private $proIdCode;

    /**
     * @var string
     *
     * @ORM\Column(name="generated_id_no", type="string", length=30)
     */
    private $generatedIdNo;

     /**
     * @var string
     *
     * @ORM\Column(name="voter_name", type="string", length=255)
     */
    private $voterName;

     /**
     * @var int
     *
     * @ORM\Column(name="is_non_voter", type="integer", length=1)
     */
    private $isNonVoter;

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
     * @ORM\Column(name="educ_level", type="string", length=50)
     */
    private $educLevel;

    /**
     * @var string
     *
     * @ORM\Column(name="mothers_maiden_name", type="string", length=255)
     */
    private $mothersMaidenName;

    /**
     * @var string
     *
     * @ORM\Column(name="civil_status", type="string", length=15)
     */
    private $civilStatus;

    /**
     * @var string
     *
     * @ORM\Column(name="skills", type="string", length=50)
     */
    private $skills;

    /**
     * @var string
     *
     * @ORM\Column(name="occupation", type="string", length=50)
     */
    private $occupation;

    /**
     * @var string
     *
     * @ORM\Column(name="monthly_income", type="string", length=50)
     */
    private $monthlyIncome;

     /**
     * @var string
     *
     * @ORM\Column(name="v_municipality_name", type="string", length=150)
     */
    private $vMunicipalityName;

    /**
     * @var string
     *
     * @ORM\Column(name="v_barangay_name", type="string", length=150)
     */
    private $vBarangayName;

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
     * @var datetime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedAt;

    /**
     * @var string
     *
     * @ORM\Column(name="updated_by", type="string", length=150)
     */
    private $updatedBy;

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
     * @Assert\NotBlank()
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
     * Set profileId
     *
     * @param string $profileId
     *
     * @return AssistanceProfile
     */
    public function setProfileId($profileId)
    {
        $this->profile_id = $profileId;

        return $this;
    }

    /**
     * Get profileId
     *
     * @return string
     */
    public function getProfileId()
    {
        return $this->profile_id;
    }

    /**
     * Set district
     *
     * @param string $district
     *
     * @return AssistanceProfile
     */
    public function setDistrict($district)
    {
        $this->district = $district;

        return $this;
    }

    /**
     * Get district
     *
     * @return string
     */
    public function getDistrict()
    {
        return $this->district;
    }

    /**
     * Set municipalityName
     *
     * @param string $municipalityName
     *
     * @return AssistanceProfile
     */
    public function setMunicipalityName($municipalityName)
    {
        $this->municipalityName = $municipalityName;

        return $this;
    }

    /**
     * Get municipalityName
     *
     * @return string
     */
    public function getMunicipalityName()
    {
        return $this->municipalityName;
    }

    /**
     * Set municipalityNo
     *
     * @param string $municipalityNo
     *
     * @return AssistanceProfile
     */
    public function setMunicipalityNo($municipalityNo)
    {
        $this->municipalityNo = $municipalityNo;

        return $this;
    }

    /**
     * Get municipalityNo
     *
     * @return string
     */
    public function getMunicipalityNo()
    {
        return $this->municipalityNo;
    }

    /**
     * Set barangayName
     *
     * @param string $barangayName
     *
     * @return AssistanceProfile
     */
    public function setBarangayName($barangayName)
    {
        $this->barangayName = $barangayName;

        return $this;
    }

    /**
     * Get barangayName
     *
     * @return string
     */
    public function getBarangayName()
    {
        return $this->barangayName;
    }

    /**
     * Set barangayNo
     *
     * @param string $barangayNo
     *
     * @return AssistanceProfile
     */
    public function setBarangayNo($barangayNo)
    {
        $this->barangayNo = $barangayNo;

        return $this;
    }

    /**
     * Get barangayNo
     *
     * @return string
     */
    public function getBarangayNo()
    {
        return $this->barangayNo;
    }

    /**
     * Set purok
     *
     * @param string $purok
     *
     * @return AssistanceProfile
     */
    public function setPurok($purok)
    {
        $this->purok = $purok;

        return $this;
    }

    /**
     * Get purok
     *
     * @return string
     */
    public function getPurok()
    {
        return $this->purok;
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     *
     * @return AssistanceProfile
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
     * Set middlename
     *
     * @param string $middlename
     *
     * @return AssistanceProfile
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
     * Set lastname
     *
     * @param string $lastname
     *
     * @return AssistanceProfile
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
     * Set extname
     *
     * @param string $extname
     *
     * @return AssistanceProfile
     */
    public function setExtname($extname)
    {
        $this->extname = $extname;

        return $this;
    }

    /**
     * Get extname
     *
     * @return string
     */
    public function getExtname()
    {
        return $this->extname;
    }

    /**
     * Set fullname
     *
     * @param string $fullname
     *
     * @return AssistanceProfile
     */
    public function setFullname($fullname)
    {
        $this->fullname = $fullname;

        return $this;
    }

    /**
     * Get fullname
     *
     * @return string
     */
    public function getFullname()
    {
        return $this->fullname;
    }

    /**
     * Set birthdate
     *
     * @param string $birthdate
     *
     * @return AssistanceProfile
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
     * Set contactNo
     *
     * @param string $contactNo
     *
     * @return AssistanceProfile
     */
    public function setContactNo($contactNo)
    {
        $this->contactNo = $contactNo;

        return $this;
    }

    /**
     * Get contactNo
     *
     * @return string
     */
    public function getContactNo()
    {
        return $this->contactNo;
    }

    /**
     * Set proVoterId
     *
     * @param integer $proVoterId
     *
     * @return AssistanceProfile
     */
    public function setProVoterId($proVoterId)
    {
        $this->proVoterId = $proVoterId;

        return $this;
    }

    /**
     * Get proVoterId
     *
     * @return integer
     */
    public function getProVoterId()
    {
        return $this->proVoterId;
    }

    /**
     * Set proIdCode
     *
     * @param string $proIdCode
     *
     * @return AssistanceProfile
     */
    public function setProIdCode($proIdCode)
    {
        $this->proIdCode = $proIdCode;

        return $this;
    }

    /**
     * Get proIdCode
     *
     * @return string
     */
    public function getProIdCode()
    {
        return $this->proIdCode;
    }

    /**
     * Set generatedIdNo
     *
     * @param string $generatedIdNo
     *
     * @return AssistanceProfile
     */
    public function setGeneratedIdNo($generatedIdNo)
    {
        $this->generatedIdNo = $generatedIdNo;

        return $this;
    }

    /**
     * Get generatedIdNo
     *
     * @return string
     */
    public function getGeneratedIdNo()
    {
        return $this->generatedIdNo;
    }

    /**
     * Set voterName
     *
     * @param string $voterName
     *
     * @return AssistanceProfile
     */
    public function setVoterName($voterName)
    {
        $this->voterName = $voterName;

        return $this;
    }

    /**
     * Get voterName
     *
     * @return string
     */
    public function getVoterName()
    {
        return $this->voterName;
    }

    /**
     * Set isNonVoter
     *
     * @param integer $isNonVoter
     *
     * @return AssistanceProfile
     */
    public function setIsNonVoter($isNonVoter)
    {
        $this->isNonVoter = $isNonVoter;

        return $this;
    }

    /**
     * Get isNonVoter
     *
     * @return integer
     */
    public function getIsNonVoter()
    {
        return $this->isNonVoter;
    }

    /**
     * Set gender
     *
     * @param string $gender
     *
     * @return AssistanceProfile
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
     * Set educLevel
     *
     * @param string $educLevel
     *
     * @return AssistanceProfile
     */
    public function setEducLevel($educLevel)
    {
        $this->educLevel = $educLevel;

        return $this;
    }

    /**
     * Get educLevel
     *
     * @return string
     */
    public function getEducLevel()
    {
        return $this->educLevel;
    }

    /**
     * Set mothersMaidenName
     *
     * @param string $mothersMaidenName
     *
     * @return AssistanceProfile
     */
    public function setMothersMaidenName($mothersMaidenName)
    {
        $this->mothersMaidenName = $mothersMaidenName;

        return $this;
    }

    /**
     * Get mothersMaidenName
     *
     * @return string
     */
    public function getMothersMaidenName()
    {
        return $this->mothersMaidenName;
    }

    /**
     * Set civilStatus
     *
     * @param string $civilStatus
     *
     * @return AssistanceProfile
     */
    public function setCivilStatus($civilStatus)
    {
        $this->civilStatus = $civilStatus;

        return $this;
    }

    /**
     * Get civilStatus
     *
     * @return string
     */
    public function getCivilStatus()
    {
        return $this->civilStatus;
    }

    /**
     * Set skills
     *
     * @param string $skills
     *
     * @return AssistanceProfile
     */
    public function setSkills($skills)
    {
        $this->skills = $skills;

        return $this;
    }

    /**
     * Get skills
     *
     * @return string
     */
    public function getSkills()
    {
        return $this->skills;
    }

    /**
     * Set occupation
     *
     * @param string $occupation
     *
     * @return AssistanceProfile
     */
    public function setOccupation($occupation)
    {
        $this->occupation = $occupation;

        return $this;
    }

    /**
     * Get occupation
     *
     * @return string
     */
    public function getOccupation()
    {
        return $this->occupation;
    }

    /**
     * Set monthlyIncome
     *
     * @param string $monthlyIncome
     *
     * @return AssistanceProfile
     */
    public function setMonthlyIncome($monthlyIncome)
    {
        $this->monthlyIncome = $monthlyIncome;

        return $this;
    }

    /**
     * Get monthlyIncome
     *
     * @return string
     */
    public function getMonthlyIncome()
    {
        return $this->monthlyIncome;
    }

    /**
     * Set vMunicipalityName
     *
     * @param string $vMunicipalityName
     *
     * @return AssistanceProfile
     */
    public function setVMunicipalityName($vMunicipalityName)
    {
        $this->vMunicipalityName = $vMunicipalityName;

        return $this;
    }

    /**
     * Get vMunicipalityName
     *
     * @return string
     */
    public function getVMunicipalityName()
    {
        return $this->vMunicipalityName;
    }

    /**
     * Set vBarangayName
     *
     * @param string $vBarangayName
     *
     * @return AssistanceProfile
     */
    public function setVBarangayName($vBarangayName)
    {
        $this->vBarangayName = $vBarangayName;

        return $this;
    }

    /**
     * Get vBarangayName
     *
     * @return string
     */
    public function getVBarangayName()
    {
        return $this->vBarangayName;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return AssistanceProfile
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
     * @return AssistanceProfile
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
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return AssistanceProfile
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set updatedBy
     *
     * @param string $updatedBy
     *
     * @return AssistanceProfile
     */
    public function setUpdatedBy($updatedBy)
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    /**
     * Get updatedBy
     *
     * @return string
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }

    /**
     * Set remarks
     *
     * @param string $remarks
     *
     * @return AssistanceProfile
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
     * @return AssistanceProfile
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
}
