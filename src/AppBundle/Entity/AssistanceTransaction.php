<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * AssistanceTransaction
 *
 * @ORM\Table(name="tbl_assistance_trn")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AssistanceTransactionRepository")
 */
class AssistanceTransaction
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
     * @ORM\Column(name="trn_id", type="string", length=50)
     */
    private $trnId;

     /**
     * @var int
     *
     * @ORM\Column(name="assist_id", type="integer")
     */
    private $assistId;

    /**
     * @var int
     *
     * @ORM\Column(name="profile_id", type="integer")
     */
    private $profileId;

    /**
     * @var string
     *
     * @ORM\Column(name="client_name", type="string", length=255)
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
     * @var int
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
     * @ORM\Column(name="generated_id_no", type="string")
     */
    private $generatedIdNo;

    /**
     * @var int
     *
     * @ORM\Column(name="is_non_voter", type="integer", scale=1)
     */
    private $isNonVoter;

     /**
     * @var string
     *
     * @ORM\Column(name="trans_desc", type="string", length=50)
     */
    private $transDesc;

      /**
     * @var string
     *
     * @ORM\Column(name="trans_type", type="string", length=50)
     */
    private $transType;

     /**
     * @var string
     *
     * @ORM\Column(name="trans_date", type="string", length=30)
     */
    private $transDate;

     /**
     * @var float
     *
     * @ORM\Column(name="amount", type="float", scale=2)
     */
    private $amount;    

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
     * Set trnId
     *
     * @param string $trnId
     *
     * @return AssistanceTransaction
     */
    public function setTrnId($trnId)
    {
        $this->trnId = $trnId;

        return $this;
    }

    /**
     * Get trnId
     *
     * @return string
     */
    public function getTrnId()
    {
        return $this->trnId;
    }

    /**
     * Set assistId
     *
     * @param integer $assistId
     *
     * @return AssistanceTransaction
     */
    public function setAssistId($assistId)
    {
        $this->assistId = $assistId;

        return $this;
    }

    /**
     * Get assistId
     *
     * @return integer
     */
    public function getAssistId()
    {
        return $this->assistId;
    }

    /**
     * Set profileId
     *
     * @param integer $profileId
     *
     * @return AssistanceTransaction
     */
    public function setProfileId($profileId)
    {
        $this->profileId = $profileId;

        return $this;
    }

    /**
     * Get profileId
     *
     * @return integer
     */
    public function getProfileId()
    {
        return $this->profileId;
    }

    /**
     * Set clientName
     *
     * @param string $clientName
     *
     * @return AssistanceTransaction
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
     * @return AssistanceTransaction
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
     * Set municipalityName
     *
     * @param string $municipalityName
     *
     * @return AssistanceTransaction
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
     * @return AssistanceTransaction
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
     * @return AssistanceTransaction
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
     * @return AssistanceTransaction
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
     * Set proVoterId
     *
     * @param integer $proVoterId
     *
     * @return AssistanceTransaction
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
     * @return AssistanceTransaction
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
     * @return AssistanceTransaction
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
     * Set isNonVoter
     *
     * @param integer $isNonVoter
     *
     * @return AssistanceTransaction
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
     * Set transDesc
     *
     * @param string $transDesc
     *
     * @return AssistanceTransaction
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
     * @return AssistanceTransaction
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
     * Set transDate
     *
     * @param string $transDate
     *
     * @return AssistanceTransaction
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
     * Set amount
     *
     * @param float $amount
     *
     * @return AssistanceTransaction
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return AssistanceTransaction
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
     * @return AssistanceTransaction
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
     * @return AssistanceTransaction
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
     * @return AssistanceTransaction
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
     * @return AssistanceTransaction
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
     * @return AssistanceTransaction
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
