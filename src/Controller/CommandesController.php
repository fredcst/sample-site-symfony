<?php

namespace App\Controller;

use App\Mapper\CommandeMapper;
use App\Repository\CommandeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

final class CommandesController extends AbstractController
{
    #[Route('/commandes', name: 'app_commandes')]
    public function index(CommandeRepository $commandeRepository): Response
    {
        $userCommandes = $commandeRepository->findBy(['createdBy' => $this->getUser()], ['createdAt' => 'DESC']);
        $allCommandes = $commandeRepository->findAll();
        return $this->render('commandes/index.html.twig', [
            'userCommandes' => $userCommandes,
            'allCommandes' => $allCommandes,
        ]);
    }

    #[Route('/api/user/{user_id}/commandes', name: 'api_commandes_user')]
    public function userCommandes($user_id, UserInterface $user, CommandeRepository $commandeRepository): Response
    {
        $commandes = $commandeRepository->findBy(['createdBy' => $user_id]);

        $dtos = array_map(
            fn($commande) => CommandeMapper::toDTO($commande),
            $commandes
        );

        return $this->json($dtos);

    }
}
