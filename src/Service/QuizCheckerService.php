<?php

namespace App\Service;

use App\Entity\Quiz;
use App\Entity\User;
use App\Entity\Reponse;
use App\Entity\Question;
use App\Entity\UserQuizSuivi;

use Doctrine\ORM\EntityManagerInterface;

class QuizCheckerService
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function isAllowedToAccessQuiz(User $user, Quiz $quiz): bool
    {
        $promotions = $user->getPromotions();

        foreach ($promotions as $promotion) {
            $quizzes = $promotion->getQuizzes();

            if ($quizzes->contains($quiz)) {
                return true;
            }
        }

        return false;
    }

    public function getUserHasExceededTimeLimit(User $user, Quiz $quiz, $userQuizSuivi): bool
    {
        if (!$userQuizSuivi) {
            $userQuizSuivi = new UserQuizSuivi();

            $userQuizSuivi->setUser($user);
            $userQuizSuivi->setQuiz($quiz);

            $this->entityManager->persist($userQuizSuivi);
            $this->entityManager->flush();

            return false;
        }

        $currentTime = new \DateTime();

        return ($currentTime->getTimestamp() - $userQuizSuivi->getStartTime()->getTimestamp() > ($quiz->getDuration() * 60));
    }

    public function getFollowingQuestionIfAvailable(User $user, Quiz $quiz, $userQuizSuivi): array {
        $userResponses = $user->getReponses();
        $quizQuestionIds = $this->getQuizQuestionIds($quiz);
        $answeredQuestionIds = $this->getAnsweredQuestionIds($userResponses, $quiz->getQuestions());
    
        if (count($quizQuestionIds) === count($answeredQuestionIds)) {
            if (!$userQuizSuivi->getEndTime()) {
                $userQuizSuivi->setEndTime(new \DateTime());
                $this->entityManager->flush();
            }

            return ["status" => "toutes_repondues"];
        }
    
        $nextQuestionId = $this->getNextUnansweredQuestionId($quizQuestionIds, $answeredQuestionIds);
        $nextQuestion = $nextQuestionId ? $this->entityManager->getRepository(Question::class)->find($nextQuestionId) : null;
    
        if (!$nextQuestion) {
            return ["status" => "plus_de_question"];
        }
    
        return [
            "status" => "question_restante",
            "question" => $nextQuestion,
            "nombreQuestionsTotal" => count($quizQuestionIds),
            "nombreQuestionsRepondues" => count($answeredQuestionIds),
        ];
    }

    private function getQuizQuestionIds(Quiz $quiz): array {
        return $quiz->getQuestions()->map(fn ($question) => $question->getId())->toArray();
    }
    
    private function getAnsweredQuestionIds($userResponses, $quizQuestions): array {
        $answeredQuestionIds = [];

        foreach ($quizQuestions as $question) {
            $questionId = $question->getId();

            $userResponse = $userResponses->filter(
                fn ($response) => $response->getQuestion()->getId() === $questionId
            );

            if ($userResponse->count() > 0) {
                $answeredQuestionIds[] = $questionId;
            }
        }

        return $answeredQuestionIds;
    }
    
    private function getNextUnansweredQuestionId($quizQuestionIds, $answeredQuestionIds): ?int {
        $remainingQuestionIds = array_diff($quizQuestionIds, $answeredQuestionIds);

        return reset($remainingQuestionIds);
    }
    

    
}