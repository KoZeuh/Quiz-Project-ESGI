<?php

namespace App\Service;

use App\Entity\Quiz;
use App\Entity\User;
use App\Entity\Reponse;


class StatistiqueQuizzService
{
    public function calculateStatisticsByQuizAndUser(Quiz $quiz, User $user): array
    {
        $questions = $quiz->getQuestions();
        $responses = [];

        foreach ($questions as $question) {
            $questionId = $question->getId();
            $questionResponses = $question->getReponses();
    
            $responses[$questionId]['question'] = $question;
            $responses[$questionId]['reponses'] = $questionResponses;
        }

        $userResponses = $user->getReponses();

        $userQuizResponses = [];
        foreach ($userResponses as $userResponse) {
            $userQuestionId = $userResponse->getQuestion()->getId();
            $userQuizResponses[$userQuestionId][] = $userResponse;
        }

        foreach ($questions as $question) {
            $questionId = $question->getId();
    
            if ($question->isQcm() && isset($userQuizResponses[$questionId])) {
                foreach ($userQuizResponses[$questionId] as $userResponse) {
                    $responseIsCorrect = $userResponse->isBonneReponse();
                    $userResponse->setBonneReponse($responseIsCorrect);
                }
            }
        }
    
        foreach ($responses as $questionId => $response) {
            if (!isset($userQuizResponses[$questionId])) {
                $userQuizResponses[$questionId] = ['Non répondu'];
            }
        }

        return $this->calculateStatistics($responses, $userQuizResponses);
    } 

    public function calculateAverageTimePerQuestion($start, $end, $countOfQuestions): array
    {
        if (!$start || !$end) {
            return [
                'hours' => 0,
                'minutes' => 0,
                'seconds' => 0,
            ];
        }
        
        $diff = $end->diff($start);
        $totalSeconds = $diff->s + $diff->i * 60 + $diff->h * 3600 + $diff->d * 86400;
    
        $averageTimePerQuestion = $totalSeconds / $countOfQuestions;
    
        $hours = floor($averageTimePerQuestion / 3600);
        $minutes = floor(($averageTimePerQuestion % 3600) / 60);
        $seconds = $averageTimePerQuestion % 60;
    
        return [
            'hours' => $hours,
            'minutes' => $minutes,
            'seconds' => $seconds,
        ];
    }
    

    public function calculateStatistics(array $responses, array $userQuizResponses): array
    {
        $correctAnswers = 0;
        $totalQuestions = count($responses);
        $answeredQuestions = 0;
    
        foreach ($responses as $questionId => $response) {
            $userResponses = $userQuizResponses[$questionId] ?? [];
            if (count($userResponses) > 0 && $userResponses[0] instanceof Reponse) {
                $answeredQuestions++;
            }
    
            if ($response['question']->isQCM()) {
                $correctResponses = $response['reponses']->filter(fn ($r) => $r instanceof Reponse && $r->isBonneReponse());
                $userSelectedCorrectAnswers = array_filter($userResponses, fn ($r) => $r instanceof Reponse && $r->isBonneReponse());

                if (
                    count($userSelectedCorrectAnswers) === count($correctResponses) &&
                    count(array_diff($userResponses, $userSelectedCorrectAnswers)) === 0
                ) {
                    $correctAnswers++;
                }
            } else {
                if (count($userResponses) > 0 && $userResponses[0] instanceof Reponse) {
                    if ($userResponses[0]->isBonneReponse()) {
                        $correctAnswers++;
                    }
                }
            }
        }
    
        $successRate = $totalQuestions > 0 ? ($correctAnswers / $totalQuestions) * 100 : 0;
    
        return [
            'totalQuestions' => $totalQuestions,
            'answeredQuestions' => $answeredQuestions,
            'correctAnswers' => $correctAnswers,
            'successRate' => $successRate,
            'responses' => $responses,
            'userQuizResponses' => $userQuizResponses
        ];
    }
    
}