<?php

namespace App\Entity;

use App\Repository\QuizPromotionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuizPromotionRepository::class)]
class QuizPromotion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Quiz::class)]
    #[ORM\JoinColumn(name: "quiz_id", referencedColumnName: "id")]
    private ?Quiz $quiz = null;

    #[ORM\ManyToOne(targetEntity: Promotion::class)]
    #[ORM\JoinColumn(name: "promotion_id", referencedColumnName: "id")]
    private ?Promotion $promotion = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuiz(): ?Quiz
    {
        return $this->quiz;
    }

    public function setQuiz(?Quiz $quiz): self
    {
        $this->quiz = $quiz;
        return $this;
    }

    public function getPromotion(): ?Promotion
    {
        return $this->promotion;
    }

    public function setPromotion(?Promotion $promotion): self
    {
        $this->promotion = $promotion;
        return $this;
    }

}

