<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * GroupAssistance
 *
 * @ORM\Table(name="tbl_group_assistance")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\GroupAssistanceRepository")
 * @UniqueEntity(fields={"batchLabel"},message="This batch already exists.", errorPath="batchLabel")
 */

class GroupAssistance
{
    /**
     * @var int
     *
     * @ORM\Column(name="hdr_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $hdrId;

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
     * @ORM\Column(name="batch_label", type="string", length=150)
     * @Assert\NotBlank()
     */
    private $batchLabel;

     /**
     * @var string
     *
     * @ORM\Column(name="batch_date", type="string", length=50)
     * @Assert\NotBlank()
     */
    private $batchDate;

    /**
     * @var string
     *
     * @ORM\Column(name="assist_type", type="string", length=50)
     * @Assert\NotBlank()
     */
    private $assistType;

    /**
     * @var int
     *
     * @ORM\Column(name="total_profiles", type="integer")
     */
    private $totalProfiles;
   
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
     * Get hdrId
     *
     * @return integer
     */
    public function getHdrId()
    {
        return $this->hdrId;
    }

    /**
     * Set municipalityName
     *
     * @param string $municipalityName
     *
     * @return GroupAssistance
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
     * Set batchLabel
     *
     * @param string $batchLabel
     *
     * @return GroupAssistance
     */
    public function setBatchLabel($batchLabel)
    {
        $this->batchLabel = $batchLabel;

        return $this;
    }

    /**
     * Get batchLabel
     *
     * @return string
     */
    public function getBatchLabel()
    {
        return $this->batchLabel;
    }

    /**
     * Set batchDate
     *
     * @param string $batchDate
     *
     * @return GroupAssistance
     */
    public function setBatchDate($batchDate)
    {
        $this->batchDate = $batchDate;

        return $this;
    }

    /**
     * Get batchDate
     *
     * @return string
     */
    public function getBatchDate()
    {
        return $this->batchDate;
    }

    /**
     * Set assistType
     *
     * @param string $assistType
     *
     * @return GroupAssistance
     */
    public function setAssistType($assistType)
    {
        $this->assistType = $assistType;

        return $this;
    }

    /**
     * Get assistType
     *
     * @return string
     */
    public function getAssistType()
    {
        return $this->assistType;
    }

    /**
     * Set totalProfiles
     *
     * @param integer $totalProfiles
     *
     * @return GroupAssistance
     */
    public function setTotalProfiles($totalProfiles)
    {
        $this->totalProfiles = $totalProfiles;

        return $this;
    }

    /**
     * Get totalProfiles
     *
     * @return integer
     */
    public function getTotalProfiles()
    {
        return $this->totalProfiles;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return GroupAssistance
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
     * @return GroupAssistance
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
     * @return GroupAssistance
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
     * @return GroupAssistance
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
     * @return GroupAssistance
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
     * @return GroupAssistance
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
