<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ResultRepository")
 */
class Result
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Candidate", inversedBy="results")
     */
    private $candidate;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Criteria", inversedBy="results")
     */
    private $criteria;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $oral;

    /**
     * @ORM\Column(type="integer")
     */
    private $test;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $coeforal;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $coeftest;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $average;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $acquis;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCandidate(): ?Candidate
    {
        return $this->candidate;
    }

    public function setCandidate(?Candidate $candidate): self
    {
        $this->candidate = $candidate;

        return $this;
    }

    public function getCriteria(): ?Criteria
    {
        return $this->criteria;
    }

    public function setCriteria(?Criteria $criteria): self
    {
        $this->criteria = $criteria;

        return $this;
    }

    public function getOral(): ?int
    {
        return $this->oral;
    }

    public function setOral(?int $oral): self
    {
        $this->oral = $oral;

        return $this;
    }

    public function getTest(): ?int
    {
        return $this->test;
    }

    public function setTest(int $test): self
    {
        $this->test = $test;

        return $this;
    }

    public function getCoeforal(): ?float
    {
        return $this->coeforal;
    }

    public function setCoeforal(?float $coeforal): self
    {
        $this->coeforal = $coeforal;

        return $this;
    }

    public function getCoeftest(): ?float
    {
        return $this->coeftest;
    }

    public function setCoeftest(?float $coeftest): self
    {
        $this->coeftest = $coeftest;

        return $this;
    }

    public function getAverage(): ?float
    {
        return $this->average;
    }

    public function setAverage(?float $average): self
    {
        $this->average = $average;

        return $this;
    }

    public function getAcquis(): ?string
    {
        return $this->acquis;
    }

    public function setAcquis(?string $acquis): self
    {
        $this->acquis = $acquis;

        return $this;
    }
}
