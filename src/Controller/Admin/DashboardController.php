<?php

namespace App\Controller\Admin;


use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;


use App\Entity\Promotion;
use App\Entity\User;
use App\Entity\Question;
use App\Entity\Quiz;
use App\Entity\Categorie;
use App\Entity\Reponse;
use App\Entity\UserReponse;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);

        return $this->redirect($adminUrlGenerator->setController(CategorieCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('ESGI Quiz - Admin');
    }

    public function configureCrud(): Crud
    {
        return Crud::new()
            ->setDateTimeFormat('medium', 'short');
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        return parent::configureUserMenu($user)
            ->setName($user->getPrenom().' '.$user->getNom());
    }
    public function configureMenuItems(): iterable
    {
        return [
            MenuItem::linkToDashboard('Accueil', 'fa fa-home'),
            MenuItem::linkToUrl('Retour au site', 'fa fa-home', '/'),

            MenuItem::section('Gestion'),
            MenuItem::linkToCrud('Categories', 'fa fa-tags', Categorie::class),
            MenuItem::linkToCrud('Quiz', 'fa fa-check', Quiz::class),
            MenuItem::linkToCrud('Questions', 'fa fa-file-text', Question::class),
            MenuItem::linkToCrud('Reponses', 'fa fa-comment', Reponse::class),
            MenuItem::linkToRoute('Statistiques', 'fa fa-home', 'admin_statistiques_choice_promotion'),
            
            MenuItem::linkToCrud('Utilisateurs', 'fa fa-comment', User::class)
            ->setPermission('ROLE_ADMIN'),
            MenuItem::linkToCrud('Promotions', 'fa fa-user', Promotion::class)
            ->setPermission('ROLE_ADMIN'),

        ];
    }
}
