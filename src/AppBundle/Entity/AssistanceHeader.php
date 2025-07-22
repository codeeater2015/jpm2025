<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * AssistanceHeader
 *
 * @ORM\Table(name="tbl_assistance_hdr")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AssistanceHeaderRepository")
 * @UniqueEntity(groups={"medicalCreate","groupCreate"},fields={"clientName","transDate"},message="Transaction already exists!", errorPath="clientProfileId")
 * @UniqueEntity(groups={"medicalCreate","groupCreate"},fields={"dependentName","transDate"},message="Transaction already exists!", errorPath="dependentProfileId")
 * @UniqueEntity(fields={"controlNo"},message="Control no already exists!", errorPath="controlNo")
 */
class AssistanceHeader
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
     * @var int
     *
     * @ORM\Column(name="group_id", type="integer")
     * @Assert\NotBlank(groups={"groupCreate"})
     */
    private $groupId;

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
     */
    private $barangayNo;

     /**
     * @var string
     *
     * @ORM\Column(name="district", type="string", length=30)
     */
    private $district;

     /**
     * @var string
     *
     * @ORM\Column(name="purok", type="string", length=50)
     */
    private $purok;

    /**
     * @var int
     *
     * @ORM\Column(name="is_non_voter", type="integer")
     */
    private $isNonVoter;

     /**
     * @var int
     *
     * @ORM\Column(name="client_profile_id", type="integer")
     * @Assert\NotBlank(groups={"medicalCreate"})
     */
    private $clientProfileId;

     /**
     * @var string
     *
     * @ORM\Column(name="client_name", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $clientName;

     /**
     * @var string
     *
     * @ORM\Column(name="client_voter_name", type="string", length=255)
     */
    private $clientVoterName;

     /**
     * @var string
     *
     * @ORM\Column(name="client_generated_id_no", type="string", length=150)
     */
    private $clientGeneratedIdNo;

    /**
     * @var int
     *
     * @ORM\Column(name="dependent_profile_id", type="integer")
     * @Assert\NotBlank(groups={"medicalCreate"})
     */
    private $dependentProfileId;

     /**
     * @var string
     *
     * @ORM\Column(name="dependent_name", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $dependentName;

     /**
     * @var string
     *
     * @ORM\Column(name="dependent_voter_name", type="string", length=255)
     */
    private $dependentVoterName;

     /**
     * @var string
     *
     * @ORM\Column(name="dependent_generated_id_no", type="string", length=150)
     */
    private $dependentGeneratedIdNo;

     /**
     * @var string
     *
     * @ORM\Column(name="dependent_diagnosis", type="string", length=150)
     * @Assert\NotBlank(groups={"medicalCreate"})
     */
    private $dependentDiagnosis;

    
     /**
     * @var string
     *
     * @ORM\Column(name="dependent_address", type="string", length=150)
     * @Assert\NotBlank()
     */
    private $dependentAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="dependent_educ_level", type="string", length=50)
     * @Assert\NotBlank(groups={"groupCreate"})
     */
    private $dependentEducLevel;

      /**
     * @var string
     *
     * @ORM\Column(name="dependent_maiden_name", type="string", length=150)
     * @Assert\NotBlank(groups={"groupCreate"})
     */
    private $dependentMaidenName;

    /**
     * @var string
     *
     * @ORM\Column(name="hospital", type="string", length=80)
     * @Assert\NotBlank(groups={"medicalCreate"})
     */
    private $hospital;

    /**
     * @var float
     *
     * @ORM\Column(name="final_bill", type="float", scale=2)
     * @Assert\NotBlank(groups={"medicalCreate"})
     */
    private $finalBill;

    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="float", scale=2)
     * @Assert\NotBlank(groups={"medicalCreate"})
     */
    private $amount;

    /**
     * @var float
     *
     * @ORM\Column(name="monthly_income", type="float", scale=2)
     * @Assert\NotBlank(groups={"groupCreate"})
     */
    private $monthlyIncome;

    /**
     * @var string
     *
     * @ORM\Column(name="occupation", type="string", length=50)
     * @Assert\NotBlank(groups={"groupCreate"})
     */
    private $occupation;

     /**
     * @var string
     *
     * @ORM\Column(name="type_of_id", type="string", length=30)
     * @Assert\NotBlank(groups={"groupCreate"})
     */
    private $typeOfId;

    /**
     * @var string
     *
     * @ORM\Column(name="submitted_id_no", type="string", length=50)
     */
    private $submittedIdNo;

     /**
     * @var string
     *
     * @ORM\Column(name="account_no", type="string", length=50)
     */
    private $accountNo;

     /**
     * @var string
     *
     * @ORM\Column(name="contact_no", type="string", length=50)
     * @Assert\NotBlank(groups={"medicalCreate","groupCreate"})
     */
    private $contactNo;

    /**
     * @var string
     *
     * @ORM\Column(name="control_no", type="string", length=30)
     */
    private $controlNo;

    /**
     * @var string
     *
     * @ORM\Column(name="station_id", type="string", length=30)
     */
    private $stationId;

    /**
     * @var string
     *
     * @ORM\Column(name="trans_date", type="string", length=30)
     * @Assert\NotBlank(groups={"medicalCreate"})
     */
    private $transDate;

     /**
     * @var string
     *
     * @ORM\Column(name="trans_desc", type="string", length=255)
     */
    private $transDesc;

    /**
     * @var string
     *
     * @ORM\Column(name="trans_type", type="string", length=30)
     * @Assert\NotBlank(groups={"medicalCreate"})
     */
    private $transType;

     /**
     * @var string
     *
     * @ORM\Column(name="release_date", type="string", length=30)
     */
    private $releaseDate;

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
     * Set municipalityName
     *
     * @param string $municipalityName
     *
     * @return AssistanceHeader
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
     * @return AssistanceHeader
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
     * @return AssistanceHeader
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
     * @return AssistanceHeader
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
     * Set district
     *
     * @param string $district
     *
     * @return AssistanceHeader
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
     * Set purok
     *
     * @param string $purok
     *
     * @return AssistanceHeader
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
     * Set isNonVoter
     *
     * @param integer $isNonVoter
     *
     * @return AssistanceHeader
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
     * Set clientProfileId
     *
     * @param integer $clientProfileId
     *
     * @return AssistanceHeader
     */
    public function setClientProfileId($clientProfileId)
    {
        $this->clientProfileId = $clientProfileId;

        return $this;
    }

    /**
     * Get clientProfileId
     *
     * @return integer
     */
    public function getClientProfileId()
    {
        return $this->clientProfileId;
    }

    /**
     * Set clientName
     *
     * @param string $clientName
     *
     * @return AssistanceHeader
     */
    public function setClientName($clientName)
    {
        $this->clientName = $clientName;

        return $this;
    }

    /**
     * Get clientName
     *
     * @return string
     */
    public function getClientName()
    {
        return $this->clientName;
    }

    /**
     * Set clientVoterName
     *
     * @param string $clientVoterName
     *
     * @return AssistanceHeader
     */
    public function setClientVoterName($clientVoterName)
    {
        $this->clientVoterName = $clientVoterName;

        return $this;
    }

    /**
     * Get clientVoterName
     *
     * @return string
     */
    public function getClientVoterName()
    {
        return $this->clientVoterName;
    }

    /**
     * Set clientGeneratedIdNo
     *
     * @param string $clientGeneratedIdNo
     *
     * @return AssistanceHeader
     */
    public function setClientGeneratedIdNo($clientGeneratedIdNo)
    {
        $this->clientGeneratedIdNo = $clientGeneratedIdNo;

        return $this;
    }

    /**
     * Get clientGeneratedIdNo
     *
     * @return string
     */
    public function getClientGeneratedIdNo()
    {
        return $this->clientGeneratedIdNo;
    }

    /**
     * Set dependentProfileId
     *
     * @param integer $dependentProfileId
     *
     * @return AssistanceHeader
     */
    public function setDependentProfileId($dependentProfileId)
    {
        $this->dependentProfileId = $dependentProfileId;

        return $this;
    }

    /**
     * Get dependentProfileId
     *
     * @return integer
     */
    public function getDependentProfileId()
    {
        return $this->dependentProfileId;
    }

    /**
     * Set dependentName
     *
     * @param string $dependentName
     *
     * @return AssistanceHeader
     */
    public function setDependentName($dependentName)
    {
        $this->dependentName = $dependentName;

        return $this;
    }

    /**
     * Get dependentName
     *
     * @return string
     */
    public function getDependentName()
    {
        return $this->dependentName;
    }

    /**
     * Set dependentVoterName
     *
     * @param string $dependentVoterName
     *
     * @return AssistanceHeader
     */
    public function setDependentVoterName($dependentVoterName)
    {
        $this->dependentVoterName = $dependentVoterName;

        return $this;
    }

    /**
     * Get dependentVoterName
     *
     * @return string
     */
    public function getDependentVoterName()
    {
        return $this->dependentVoterName;
    }

    /**
     * Set dependentGeneratedIdNo
     *
     * @param string $dependentGeneratedIdNo
     *
     * @return AssistanceHeader
     */
    public function setDependentGeneratedIdNo($dependentGeneratedIdNo)
    {
        $this->dependentGeneratedIdNo = $dependentGeneratedIdNo;

        return $this;
    }

    /**
     * Get dependentGeneratedIdNo
     *
     * @return string
     */
    public function getDependentGeneratedIdNo()
    {
        return $this->dependentGeneratedIdNo;
    }

    /**
     * Set dependentDiagnosis
     *
     * @param string $dependentDiagnosis
     *
     * @return AssistanceHeader
     */
    public function setDependentDiagnosis($dependentDiagnosis)
    {
        $this->dependentDiagnosis = $dependentDiagnosis;

        return $this;
    }

    /**
     * Get dependentDiagnosis
     *
     * @return string
     */
    public function getDependentDiagnosis()
    {
        return $this->dependentDiagnosis;
    }

    /**
     * Set hospital
     *
     * @param string $hospital
     *
     * @return AssistanceHeader
     */
    public function setHospital($hospital)
    {
        $this->hospital = $hospital;

        return $this;
    }

    /**
     * Get hospital
     *
     * @return string
     */
    public function getHospital()
    {
        return $this->hospital;
    }

    /**
     * Set finalBill
     *
     * @param float $finalBill
     *
     * @return AssistanceHeader
     */
    public function setFinalBill($finalBill)
    {
        $this->finalBill = $finalBill;

        return $this;
    }

    /**
     * Get finalBill
     *
     * @return float
     */
    public function getFinalBill()
    {
        return $this->finalBill;
    }

    /**
     * Set amount
     *
     * @param float $amount
     *
     * @return AssistanceHeader
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set monthlyIncome
     *
     * @param float $monthlyIncome
     *
     * @return AssistanceHeader
     */
    public function setMonthlyIncome($monthlyIncome)
    {
        $this->monthlyIncome = $monthlyIncome;

        return $this;
    }

    /**
     * Get monthlyIncome
     *
     * @return float
     */
    public function getMonthlyIncome()
    {
        return $this->monthlyIncome;
    }

    /**
     * Set occupation
     *
     * @param string $occupation
     *
     * @return AssistanceHeader
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
     * Set typeOfId
     *
     * @param string $typeOfId
     *
     * @return AssistanceHeader
     */
    public function setTypeOfId($typeOfId)
    {
        $this->typeOfId = $typeOfId;

        return $this;
    }

    /**
     * Get typeOfId
     *
     * @return string
     */
    public function getTypeOfId()
    {
        return $this->typeOfId;
    }

    /**
     * Set submittedIdNo
     *
     * @param string $submittedIdNo
     *
     * @return AssistanceHeader
     */
    public function setSubmittedIdNo($submittedIdNo)
    {
        $this->submittedIdNo = $submittedIdNo;

        return $this;
    }

    /**
     * Get submittedIdNo
     *
     * @return string
     */
    public function getSubmittedIdNo()
    {
        return $this->submittedIdNo;
    }

    /**
     * Set accountNo
     *
     * @param string $accountNo
     *
     * @return AssistanceHeader
     */
    public function setAccountNo($accountNo)
    {
        $this->accountNo = $accountNo;

        return $this;
    }

    /**
     * Get accountNo
     *
     * @return string
     */
    public function getAccountNo()
    {
        return $this->accountNo;
    }

    /**
     * Set contactNo
     *
     * @param string $contactNo
     *
     * @return AssistanceHeader
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
     * Set controlNo
     *
     * @param string $controlNo
     *
     * @return AssistanceHeader
     */
    public function setControlNo($controlNo)
    {
        $this->controlNo = $controlNo;

        return $this;
    }

    /**
     * Get controlNo
     *
     * @return string
     */
    public function getControlNo()
    {
        return $this->controlNo;
    }

    /**
     * Set stationId
     *
     * @param string $stationId
     *
     * @return AssistanceHeader
     */
    public function setStationId($stationId)
    {
        $this->stationId = $stationId;

        return $this;
    }

    /**
     * Get stationId
     *
     * @return string
     */
    public function getStationId()
    {
        return $this->stationId;
    }

    /**
     * Set transDate
     *
     * @param string $transDate
     *
     * @return AssistanceHeader
     */
    public function setTransDate($transDate)
    {
        $this->transDate = $transDate;

        return $this;
    }

    /**
     * Get transDate
     *
     * @return string
     */
    public function getTransDate()
    {
        return $this->transDate;
    }

    /**
     * Set transDesc
     *
     * @param string $transDesc
     *
     * @return AssistanceHeader
     */
    public function setTransDesc($transDesc)
    {
        $this->transDesc = $transDesc;

        return $this;
    }

    /**
     * Get transDesc
     *
     * @return string
     */
    public function getTransDesc()
    {
        return $this->transDesc;
    }

    /**
     * Set transType
     *
     * @param string $transType
     *
     * @return AssistanceHeader
     */
    public function setTransType($transType)
    {
        $this->transType = $transType;

        return $this;
    }

    /**
     * Get transType
     *
     * @return string
     */
    public function getTransType()
    {
        return $this->transType;
    }

    /**
     * Set releaseDate
     *
     * @param string $releaseDate
     *
     * @return AssistanceHeader
     */
    public function setReleaseDate($releaseDate)
    {
        $this->releaseDate = $releaseDate;

        return $this;
    }

    /**
     * Get releaseDate
     *
     * @return string
     */
    public function getReleaseDate()
    {
        return $this->releaseDate;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return AssistanceHeader
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
     * @return AssistanceHeader
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
     * @return AssistanceHeader
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
     * @return AssistanceHeader
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
     * @return AssistanceHeader
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
     * @return AssistanceHeader
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
     * Set dependentAddress
     *
     * @param string $dependentAddress
     *
     * @return AssistanceHeader
     */
    public function setDependentAddress($dependentAddress)
    {
        $this->dependentAddress = $dependentAddress;

        return $this;
    }

    /**
     * Get dependentAddress
     *
     * @return string
     */
    public function getDependentAddress()
    {
        return $this->dependentAddress;
    }

    /**
     * Set groupId
     *
     * @param integer $groupId
     *
     * @return AssistanceHeader
     */
    public function setGroupId($groupId)
    {
        $this->groupId = $groupId;

        return $this;
    }

    /**
     * Get groupId
     *
     * @return integer
     */
    public function getGroupId()
    {
        return $this->groupId;
    }

    /**
     * Set dependentEducLevel
     *
     * @param string $dependentEducLevel
     *
     * @return AssistanceHeader
     */
    public function setDependentEducLevel($dependentEducLevel)
    {
        $this->dependentEducLevel = $dependentEducLevel;

        return $this;
    }

    /**
     * Get dependentEducLevel
     *
     * @return string
     */
    public function getDependentEducLevel()
    {
        return $this->dependentEducLevel;
    }

    /**
     * Set dependentMaidenName
     *
     * @param string $dependentMaidenName
     *
     * @return AssistanceHeader
     */
    public function setDependentMaidenName($dependentMaidenName)
    {
        $this->dependentMaidenName = $dependentMaidenName;

        return $this;
    }

    /**
     * Get dependentMaidenName
     *
     * @return string
     */
    public function getDependentMaidenName()
    {
        return $this->dependentMaidenName;
    }
}
