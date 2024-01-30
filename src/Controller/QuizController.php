<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Quiz;
use App\Entity\UserQuizSuivi;
use App\Entity\Question;
use App\Entity\Reponse;
use App\Entity\UserReponse;
use App\Entity\User;

use App\Service\StatistiqueQuizzService;
use App\Service\QuizCheckerService;


class QuizController extends AbstractController
{
    #[Route('/quiz/list', name: "list_quiz_index", methods: ['GET'])]
    public function index(): Response
    {
        $promotions = $this->getUser()->getPromotions();
        $quizList = [];

        foreach ($promotions as $promotion) {
            $quizzes = $promotion->getQuizzes();
            $quizList = array_merge($quizList, $quizzes->toArray());
        }

        return $this->render('quiz/list.html.twig', [
            'quizList' => $quizList
        ]);
    }

    #[Route('/quiz/resultat/list', name: "list_resultat_quiz_index", methods: ['GET'])]
    public function listResultatQuiz(): Response
    {
        $userResponses = $this->getUser()->getReponses();
        $quizResults = [];
    
        foreach ($userResponses as $userResponse) {
            $quiz = $userResponse->getQuestion()->getQuiz();
            $quizId = $quiz->getId();
    
            if (!isset($quizResults[$quizId])) {
                $quizResults[$quizId]['quiz'] = $quiz;
                $quizResults[$quizId]['responses'] = [];
            }
    
            $questionId = $userResponse->getQuestion()->getId();
            $quizResults[$quizId]['responses'][$questionId][] = $userResponse;
        }
    
        return $this->render('quiz/list_termines.html.twig', [
            'quizResults' => $quizResults,
        ]);
    }

    #[Route('/quiz/{id}', name: "show_quiz", methods: ['GET', 'POST'])]
    public function show(EntityManagerInterface $entityManager, QuizCheckerService $quizCheckerService, Quiz $quiz): Response
    {
        $user = $this->getUser();

        if (!$quizCheckerService->isAllowedToAccessQuiz($user, $quiz)) {
            $this->addFlash('error', "Le quiz que vous avez essayé de rejoindre n'est pas accessible.");
            return $this->redirectToRoute('list_quiz_index');
        }

        $userQuizSuiviRepository = $entityManager->getRepository(UserQuizSuivi::class);
        $userSuivi = $userQuizSuiviRepository->findOneBy(['user' => $user, 'quiz' => $quiz]);
        $specificData = $quizCheckerService->getFollowingQuestionIfAvailable($user, $quiz, $userSuivi);
        $currentTime = new \DateTime();

        if ($specificData["status"] === "toutes_repondues") {
            return $this->redirectToRoute('show_resultat_quiz', ['id' => $quiz->getId()]);
        } elseif ($specificData["status"] === "plus_de_question") {
            $this->addFlash('error', "Le quiz que vous avez essayé de rejoindre n'est pas accessible.");
            return $this->redirectToRoute('list_quiz_index');
        } 
        
        
        if ($quizCheckerService->getUserHasExceededTimeLimit($user, $quiz, $userSuivi)) {
            $userSuivi->setEndTime($currentTime);
            $entityManager->flush();

            return $this->redirectToRoute('show_resultat_quiz', ['id' => $quiz->getId(), 'timeExceeded' => true]);
        }

        if (!$userSuivi) {
            $userSuivi = $userQuizSuiviRepository->findOneBy(['user' => $user, 'quiz' => $quiz]);
        }

        return $this->render('quiz/show.html.twig', [
            'quiz' => $quiz,
            'userSuivi' => $userSuivi,
            'question' => $specificData["question"],
            'nombreQuestionsTotal' => $specificData["nombreQuestionsTotal"],
            'nombreQuestionsRepondues' => $specificData["nombreQuestionsRepondues"],
        ]);
    }

    #[Route('/quiz/reponse/{id}/{question}', name: "envoi_reponse_question_quiz", methods: ['POST'])]
    public function submitAnswer(EntityManagerInterface $entityManager, Request $request, Quiz $quiz, Question $question): Response
    {
        $selectedAnswers = [];
        $selectedAnswer = null;
    
        if ($question->isQcm()) {
            $selectedAnswers =  $request->get('reponses', []);
        } else {
            $selectedAnswer = $request->request->get('reponse');
        }
    
        $user = $this->getUser();

        if (!empty($selectedAnswers)) {
            foreach ($selectedAnswers as $selectedAnswerId) {
                $selectedAnswer = $entityManager->getRepository(Reponse::class)->find($selectedAnswerId);
                if ($selectedAnswer) {
                    $userResponse = new UserReponse();
                    $userResponse->setUser($user);
                    $userResponse->setReponse($selectedAnswer);
                    $entityManager->persist($userResponse);
                }
            }
        } elseif ($selectedAnswer) {
            $selectedAnswer = $entityManager->getRepository(Reponse::class)->find($selectedAnswer);
            if ($selectedAnswer) {
                $userResponse = new UserReponse();
                $userResponse->setUser($user);
                $userResponse->setReponse($selectedAnswer);
                $entityManager->persist($userResponse);
            }
        }
        
        $entityManager->flush();

        return $this->redirectToRoute('show_quiz', ['id' => $quiz->getId()]);
    }
    

    #[Route('/quiz/resultat/{id}', name: "show_resultat_quiz", methods: ['GET'])]
    public function showResultat(EntityManagerInterface $entityManager, StatistiqueQuizzService $statistiqueQuizzService, QuizCheckerService $quizCheckerService, Quiz $quiz, bool $timeExceeded = false): Response
    {
        $user = $this->getUser();
        $statistiques = $statistiqueQuizzService->calculateStatisticsByQuizAndUser($quiz, $user);
        $userSuivi = $entityManager->getRepository(UserQuizSuivi::class)->findOneBy(['user' => $user,'quiz' => $quiz]);
        $drawResult = true;

        if (!$userSuivi) {
            $drawResult = false;
        }

        if ($drawResult) {
            if ($statistiques['answeredQuestions'] < $statistiques['totalQuestions']) {
                $drawResult = $quizCheckerService->getUserHasExceededTimeLimit($user, $quiz, $userSuivi);
            }
        }

        if (!$drawResult) {
            $this->addFlash('error', "Vous n'avez pas encore terminé ce quiz.");
            return $this->redirectToRoute('show_quiz', ['id' => $quiz->getId()]);
        }

        $startTime = $userSuivi->getStartTime();
        $endTime = $userSuivi->getEndTime();
        $diff = $endTime->diff($startTime);

        $averageTimePerQuestion = $statistiqueQuizzService->calculateAverageTimePerQuestion($startTime, $endTime, $statistiques['totalQuestions']);
        
        $statistiques['averageTimePerQuestion'] = $averageTimePerQuestion;
        $statistiques['totalDurationInHours'] = $diff->h;
        $statistiques['totalDurationInMinutes'] = $diff->i;
        $statistiques['totalDurationInSeconds'] = $diff->s;

        
        return $this->render('quiz/result.html.twig', [
            'quiz' => $quiz,
            'userSuivi' => $userSuivi,
            'statistiques' => $statistiques,
        ]);
    }
    
}
