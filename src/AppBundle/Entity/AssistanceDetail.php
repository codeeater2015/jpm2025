<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * AssistanceDetail
 *
 * @ORM\Table(name="tbl_assistance_dtl")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AssistanceDetailRepository")
 */
class AssistanceDetail
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
     * @ORM\Column(name="hdr_id", type="integer")
     * @Assert\NotBlank()
     */
    private $hdrId;

      /**
     * @var string
     *
     * @ORM\Column(name="profile_id", type="integer")
     * @Assert\NotBlank()
     */
    private $profileId;

     /**
     * @var string
     *
     * @ORM\Column(name="beneficiary_name", type="string", length=255)
     */
    private $beneficiaryName;

     /**
     * @var string
     *
     * @ORM\Column(name="municipality_name", type="string", length=150)
     * @Assert\NotBlank()
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
     * @Assert\NotBlank()
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
     * Set hdrId
     *
     * @param integer $hdrId
     *
     * @return AssistanceDetail
     */
    public function setHdrId($hdrId)
    {
        $this->hdrId = $hdrId;

        return $this;
    }

    /**
     * Get hdrId
     *
     * @return integer
     */
    public function getHdrId()
    {
        return $this->hdrId;
    }

    /**
     * Set profileId
     *
     * @param integer $profileId
     *
     * @return AssistanceDetail
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
     * Set beneficiaryName
     *
     * @param string $beneficiaryName
     *
     * @return AssistanceDetail
     */
    public function setBeneficiaryName($beneficiaryName)
    {
        $this->beneficiaryName = $beneficiaryName;

        return $this;
    }

    /**
     * Get beneficiaryName
     *
     * @return string
     */
    public function getBeneficiaryName()
    {
        return $this->beneficiaryName;
    }

    /**
     * Set municipalityName
     *
     * @param string $municipalityName
     *
     * @return AssistanceDetail
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
     * @return AssistanceDetail
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
     * @return AssistanceDetail
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
     * @return AssistanceDetail
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
     * @return AssistanceDetail
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
     * @return AssistanceDetail
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
     * @return AssistanceDetail
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
     * @return AssistanceDetail
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
     * @return AssistanceDetail
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return AssistanceDetail
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
     * @return AssistanceDetail
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
     * @return AssistanceDetail
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
     * @return AssistanceDetail
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
     * @return AssistanceDetail
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
     * @return AssistanceDetail
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
