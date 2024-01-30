<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Categorie;
use App\Entity\Quiz;
use App\Entity\User;
use App\Entity\UserQuizSuivi;

use Symfony\Component\Translation\LocaleSwitcher;

class DefaultController extends AbstractController
{
    #[Route('/', name: "default_index", methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager, Request $request, LocaleSwitcher $localeSwitcher): Response
    {
        $user = $this->getUser();
        
        $nombreCategories = $entityManager->getRepository(Categorie::class)->count([]);
        $nombreUtilisateurs = $entityManager->getRepository(User::class)->count([]);
        $nombreQuiz = $entityManager->getRepository(Quiz::class)->count([]);
        $quizTermines = [];

        if ($user) {
            $userSuivi = $entityManager->getRepository(UserQuizSuivi::class)->findBy(['user' => $user]);

            foreach ($userSuivi as $suivi) {
                if ($suivi->getEndTime() != null) {
                    $quizTermines[] = [
                        'quiz' => $suivi->getQuiz(),
                        'endTime' => $suivi->getEndTime()
                    ];
                }
            }
        }

        $currentLocale = $request->getSession()->get('_locale');

        if ($currentLocale == null) {
            $currentLocale = $localeSwitcher->getLocale();
        }
        
        $localeSwitcher->setLocale($currentLocale);

        return $this->render('default/index.html.twig', [
            'quizEnCours' => [],
            'quizTermines' => $quizTermines,

            'nombreCategories' => $nombreCategories,
            'nombreUtilisateurs' => $nombreUtilisateurs,
            'nombreQuiz' => $nombreQuiz,
        ]);
    }

    #[Route('/change-locale/{locale}', name: "default_change_locale", methods: ['GET'])]
    public function changeLocale(string $locale, Request $request, LocaleSwitcher $localeSwitcher): Response
    {
        $request->getSession()->set('_locale', $locale);
        $localeSwitcher->setLocale($locale);

        return $this->redirectToRoute('default_index');
    }
}
