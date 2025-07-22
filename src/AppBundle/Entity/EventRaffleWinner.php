<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * ApEventRaffle
 *
 * @ORM\Table(name="tbl_project_event_raffle_winner")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EventRaffleWinnerRepository")
 */
class EventRaffleWinner
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
     * @ORM\Column(name="event_id", type="integer")
     */
    private $eventId;


     /**
     * @var int
     *
     * @ORM\Column(name="pro_voter_id", type="integer")
     * @Assert\NotBlank()
     */
    private $proVoterId;

    /**
     * @var string
     *
     * @ORM\Column(name="generated_id_no", type="string", length=150)
     */
    private $generatedIdNo;

    /**
     * @var string
     *
     * @ORM\Column(name="voter_name", type="string", length=255)
     */
    private $voterName;

    /**
     * @var string
     *
     * @ORM\Column(name="municipality_name", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $municipalityName;

    /**
     * @var string
     *
     * @ORM\Column(name="barangay_name", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $barangayName;

    /**
     * @var int
     *
     * @ORM\Column(name="has_claimed", type="integer", length=1)
     */
    private $hasClaimed;
    
    /**
     * @var string
     *
     * @ORM\Column(name="remarks", type="string", length=256)
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
     * Set eventId
     *
     * @param integer $eventId
     *
     * @return EventRaffleWinner
     */
    public function setEventId($eventId)
    {
        $this->eventId = $eventId;

        return $this;
    }

    /**
     * Get eventId
     *
     * @return integer
     */
    public function getEventId()
    {
        return $this->eventId;
    }

    /**
     * Set proVoterId
     *
     * @param integer $proVoterId
     *
     * @return EventRaffleWinner
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
     * Set generatedIdNo
     *
     * @param string $generatedIdNo
     *
     * @return EventRaffleWinner
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
     * @return EventRaffleWinner
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
     * Set municipalityName
     *
     * @param string $municipalityName
     *
     * @return EventRaffleWinner
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
     * Set barangayName
     *
     * @param string $barangayName
     *
     * @return EventRaffleWinner
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
     * Set hasClaimed
     *
     * @param integer $hasClaimed
     *
     * @return EventRaffleWinner
     */
    public function setHasClaimed($hasClaimed)
    {
        $this->hasClaimed = $hasClaimed;

        return $this;
    }

    /**
     * Get hasClaimed
     *
     * @return integer
     */
    public function getHasClaimed()
    {
        return $this->hasClaimed;
    }

    /**
     * Set remarks
     *
     * @param string $remarks
     *
     * @return EventRaffleWinner
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
}
