<?php

namespace App\Entity;

use App\Repository\UserQuizSuiviRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserQuizSuiviRepository::class)]
/**
 * @ORM\Table(name="user_quiz_suivi", uniqueConstraints={
 *     @ORM\UniqueConstraint(name="unique_user_quiz", columns={"user_id", "quiz_id"})
 * })
*/

class UserQuizSuivi
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id")]
    private ?User $user = null;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Quiz::class)]
    #[ORM\JoinColumn(name: "quiz_id", referencedColumnName: "id")]
    private ?Quiz $quiz = null;

    #[ORM\Column(type: "datetime", nullable: false)]
    private \DateTimeInterface $startTime;

    #[ORM\Column(type: "datetime", nullable: true)]
    private ?\DateTimeInterface $endTime = null;

    public function __construct()
    {
        $this->startTime = new \DateTime();
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->startTime;
    }

    public function setStartTime(\DateTimeInterface $startTime): self
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getEndTime(): ?\DateTimeInterface
    {
        return $this->endTime;
    }

    public function setEndTime(?\DateTimeInterface $endTime): self
    {
        $this->endTime = $endTime;

        return $this;
    }
}
