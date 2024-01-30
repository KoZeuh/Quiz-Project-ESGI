<?php
namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

use App\Service\StatistiqueQuizzService;
use App\Service\QuizCheckerService;

use App\Entity\Promotion;
use App\Entity\User;
use App\Entity\Question;
use App\Entity\Quiz;
use App\Entity\Categorie;
use App\Entity\Reponse;
use App\Entity\UserReponse;
use App\Entity\UserQuizSuivi;



class AdminStatistiquesController extends AbstractDashboardController
{
    public function __construct(EntityManagerInterface $entityManager, StatistiqueQuizzService $statistiqueQuizzService, QuizCheckerService $quizCheckerService)
    {
        $this->entityManager = $entityManager;
        $this->statistiqueQuizzService = $statistiqueQuizzService;
        $this->quizCheckerService = $quizCheckerService;
    }

    #[Route('/admin/statistiques/choice_promotion', name: 'admin_statistiques_choice_promotion')]
    public function index(): Response
    {
        $user = $this->getUser();

        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            $promotions = $this->entityManager->getRepository(Promotion::class)->findAll();
        } else {
            $promotions = $user->getPromotions();
        }

        return $this->render('admin/statistiques/_choice_promotion.html.twig', [
            'promotionsList' => $promotions,
        ]);
    }

    #[Route('/admin/statistiques/promotion/{id}', name: 'show_admin_statistiques_quiz_list')]
    public function showQuizList($id): Response
    {
        $promotion = $this->entityManager->getRepository(Promotion::class)->find($id);
        $quizzes = $promotion->getQuizzes();
    
        foreach ($quizzes as $quiz) {
            $usersInPromotion = $promotion->getUsers();
            $quizDuration = 0;
            $quizSuccessRate = 0;
            $userCount = count($usersInPromotion);
    
            foreach ($usersInPromotion as $user) {
                $userQuizSuivi = $this->entityManager->getRepository(UserQuizSuivi::class)->findOneBy(['user' => $user, 'quiz' => $quiz]);
                $statistiques = $this->statistiqueQuizzService->calculateStatisticsByQuizAndUser($quiz, $user);
    
                if ($userQuizSuivi) {
                    $startTime = $userQuizSuivi->getStartTime();
                    $endTime = $userQuizSuivi->getEndTime();
                    $diff = $endTime->diff($startTime);
                    $quizDuration += $diff->h * 60 + $diff->i;
                }
    
                $quizSuccessRate += $statistiques['successRate'];
            }
    
            if ($userCount > 0) {
                $quiz->averageDuration = $quizDuration / $userCount;
                $quiz->averageSuccessRate = $quizSuccessRate / $userCount;
            } else {
                $quiz->averageDuration = 0;
                $quiz->averageSuccessRate = 0;
            }
        }
    
        return $this->render('admin/statistiques/_quiz_list.html.twig', [
            'promotion' => $promotion,
            'quizList' => $quizzes,
        ]);
    }
    
    
    #[Route('/admin/statistiques/quiz/{promotion_id}/quiz/{id}', name: 'show_admin_statistiques_quiz_selected')]
    public function showQuizSelected($promotion_id, $id): Response
    {
        $quiz = $this->entityManager->getRepository(Quiz::class)->find($id);
        $promotion = $this->entityManager->getRepository(Promotion::class)->find($promotion_id);
        $usersInPromotion = $promotion->getUsers();

        $usersData = [];

        foreach ($usersInPromotion as $user) {
            $statistiques = $this->statistiqueQuizzService->calculateStatisticsByQuizAndUser($quiz, $user);
            $userSuivi = $this->entityManager->getRepository(UserQuizSuivi::class)->findOneBy(['user' => $user,'quiz' => $quiz]);

            if ($userSuivi) {
                $startTime = $userSuivi->getStartTime();
                $endTime = $userSuivi->getEndTime();

                if ($startTime && $endTime) {
                    $averageTimePerQuestion = $this->statistiqueQuizzService->calculateAverageTimePerQuestion($startTime, $endTime, $statistiques['totalQuestions']);
                    $statistiques['averageTimePerQuestion'] = $averageTimePerQuestion;
            
            
                    $diff = $endTime->diff($startTime);
                    $statistiques['totalDurationInHours'] = $diff->h;
                    $statistiques['totalDurationInMinutes'] = $diff->i;
                    $statistiques['totalDurationInSeconds'] = $diff->s;
                } else {
                    $userSuivi = null;
                }
            }

            if (!$userSuivi) {
                $statistiques['averageTimePerQuestion'] = [
                    'hours' => 0,
                    'minutes' => 0,
                    'seconds' => 0,
                ];
                $statistiques['totalDurationInHours'] = 0;
                $statistiques['totalDurationInMinutes'] = 0;
                $statistiques['totalDurationInSeconds'] = 0;
            }

            $usersData[] = [
                'user' => $user,
                'statistiques' => $statistiques
            ];
        }

        return $this->render('admin/statistiques/_quiz_selected.html.twig', [
            'quiz' => $quiz,
            'usersAndStatistiques' => $usersData,
        ]);
    }

    #[Route('/admin/statistiques/quiz/{id}/user/{user_id}', name: 'show_admin_statistiques_quiz_user_selected')]
    public function showQuizUserSelected($id, $user_id): Response
    {
        $user = $this->entityManager->getRepository(User::class)->find($user_id);
        $quiz = $this->entityManager->getRepository(Quiz::class)->find($id);

        $statistiques = $this->statistiqueQuizzService->calculateStatisticsByQuizAndUser($quiz, $user);
        $userSuivi = $this->entityManager->getRepository(UserQuizSuivi::class)->findOneBy(['user' => $user,'quiz' => $quiz]);
        $drawResult = true;

        if (!$userSuivi) {
            $drawResult = false;
        }

        if ($drawResult) {
            if ($statistiques['answeredQuestions'] < $statistiques['totalQuestions']) {
                $drawResult = $this->quizCheckerService->getUserHasExceededTimeLimit($user, $quiz, $userSuivi);
            }
        }

        if ($drawResult) {
            $startTime = $userSuivi->getStartTime();
            $endTime = $userSuivi->getEndTime();
    
            $averageTimePerQuestion = $this->statistiqueQuizzService->calculateAverageTimePerQuestion($startTime, $endTime, $statistiques['totalQuestions']);
            $statistiques['averageTimePerQuestion'] = $averageTimePerQuestion;
    
    
            $diff = $endTime->diff($startTime);
            $statistiques['totalDurationInHours'] = $diff->h;
            $statistiques['totalDurationInMinutes'] = $diff->i;
            $statistiques['totalDurationInSeconds'] = $diff->s;
        } else {
            return $this->redirectToRoute('show_admin_statistiques_quiz_selected', [
                'promotion_id' => $user->getPromotions()[0]->getId(),
                'id' => $quiz->getId(),
            ]);
        }
        
        return $this->render('admin/statistiques/_quiz_user_selected.html.twig', [
            'quiz' => $quiz,
            'user' => $user,
            'userSuivi' => $userSuivi,
            'statistiques' => $statistiques,
        ]);
    }
}