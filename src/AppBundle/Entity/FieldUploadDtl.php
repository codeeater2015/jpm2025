<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * FieldUploadDtl
 *
 * @ORM\Table(name="tbl_field_upload_dtl")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FieldUploadDtlRepository")
 * @UniqueEntity(fields={"filename","hdrId"},message="This filename already exists.", errorPath="filename")
 */

class FieldUploadDtl
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
     * @ORM\Column(name="hdr_id", type="integer")
     * @Assert\NotBlank()
     */
    private $hdrId;

    /**
     * @var string
     *
     * @ORM\Column(name="filename", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $filename;

     /**
     * @var string
     *
     * @ORM\Column(name="file_display_name", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $fileDisplayName;

    /**
     * @var string
     *
     * @ORM\Column(name="pro_id_code", type="string", length=30)
     */
    private $proIdCode;

     /**
     * @var string
     *
     * @ORM\Column(name="pro_voter_id", type="integer")
     */
    private $proVoterId;

     /**
     * @var string
     *
     * @ORM\Column(name="is_downloaded", type="integer", scale = 1)
     */
    private $isDownloaded;

     /**
     * @var string
     *
     * @ORM\Column(name="is_cleared", type="integer", scale = 1)
     */
    private $isCleared;

    /**
     * @var string
     *
     * @ORM\Column(name="is_new_upload", type="integer", scale = 1)
     */
    private $isNewUpload;

     /**
     * @var string
     *
     * @ORM\Column(name="is_not_found", type="integer", scale = 1)
     */
    private $isNotFound;

     /**
     * @var string
     *
     * @ORM\Column(name="is_duplicate", type="integer", scale = 1)
     */
    private $isDuplicate;
     
    /**
     * @var string
     *
     * @ORM\Column(name="display_name", type="string", length=255)
     */
    private $displayName;

    /**
     * @var string
     *
     * @ORM\Column(name="generated_id_no", type="string", length=255)
     */
    private $generatedIdNo;

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
     * @ORM\Column(name="status", type="string", length=3)
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="remarks", type="string", length=255)
     */
    private $remarks;

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
     * @return FieldUploadDtl
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
     * Set filename
     *
     * @param string $filename
     *
     * @return FieldUploadDtl
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Get filename
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return FieldUploadDtl
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
     * @return FieldUploadDtl
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
     * Set status
     *
     * @param string $status
     *
     * @return FieldUploadDtl
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
     * Set remarks
     *
     * @param string $remarks
     *
     * @return FieldUploadDtl
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
     * Set proIdCode
     *
     * @param string $proIdCode
     *
     * @return FieldUploadDtl
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
     * Set proVoterId
     *
     * @param integer $proVoterId
     *
     * @return FieldUploadDtl
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
     * Set isDownloaded
     *
     * @param integer $isDownloaded
     *
     * @return FieldUploadDtl
     */
    public function setIsDownloaded($isDownloaded)
    {
        $this->isDownloaded = $isDownloaded;

        return $this;
    }

    /**
     * Get isDownloaded
     *
     * @return integer
     */
    public function getIsDownloaded()
    {
        return $this->isDownloaded;
    }

    /**
     * Set displayName
     *
     * @param string $displayName
     *
     * @return FieldUploadDtl
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;

        return $this;
    }

    /**
     * Get displayName
     *
     * @return string
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * Set generatedIdNo
     *
     * @param string $generatedIdNo
     *
     * @return FieldUploadDtl
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
     * Set fileDisplayName
     *
     * @param string $fileDisplayName
     *
     * @return FieldUploadDtl
     */
    public function setFileDisplayName($fileDisplayName)
    {
        $this->fileDisplayName = $fileDisplayName;

        return $this;
    }

    /**
     * Get fileDisplayName
     *
     * @return string
     */
    public function getFileDisplayName()
    {
        return $this->fileDisplayName;
    }

    /**
     * Set isNotFound
     *
     * @param integer $isNotFound
     *
     * @return FieldUploadDtl
     */
    public function setIsNotFound($isNotFound)
    {
        $this->isNotFound = $isNotFound;

        return $this;
    }

    /**
     * Get isNotFound
     *
     * @return integer
     */
    public function getIsNotFound()
    {
        return $this->isNotFound;
    }

    /**
     * Set isDuplicate
     *
     * @param integer $isDuplicate
     *
     * @return FieldUploadDtl
     */
    public function setIsDuplicate($isDuplicate)
    {
        $this->isDuplicate = $isDuplicate;

        return $this;
    }

    /**
     * Get isDuplicate
     *
     * @return integer
     */
    public function getIsDuplicate()
    {
        return $this->isDuplicate;
    }

    /**
     * Set isCleared
     *
     * @param integer $isCleared
     *
     * @return FieldUploadDtl
     */
    public function setIsCleared($isCleared)
    {
        $this->isCleared = $isCleared;

        return $this;
    }

    /**
     * Get isCleared
     *
     * @return integer
     */
    public function getIsCleared()
    {
        return $this->isCleared;
    }

    /**
     * Set isNewUpload
     *
     * @param integer $isNewUpload
     *
     * @return FieldUploadDtl
     */
    public function setIsNewUpload($isNewUpload)
    {
        $this->isNewUpload = $isNewUpload;

        return $this;
    }

    /**
     * Get isNewUpload
     *
     * @return integer
     */
    public function getIsNewUpload()
    {
        return $this->isNewUpload;
    }
}
