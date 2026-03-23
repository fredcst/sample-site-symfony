<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Form\CommandeType;
use App\Mapper\CommandeMapper;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CommandesController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

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

    #[Route('/commandes/new', name: 'app_commandes_new')]
    public function new(Request $request): Response
    {
        if (!$this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $commande = new Commande();
        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commande->setCreatedBy($this->getUser());

            $this->entityManager->persist($commande);
            $this->entityManager->flush();

            $this->addFlash('success', 'Commande ajoutée');

            return $this->redirectToRoute('app_commandes');
        }

        return $this->render('commandes/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/commandes/{id}/delete', name: 'app_commandes_delete', methods: ['POST'])]
    public function delete(Request $request, Commande $commande): Response
    {
        if ($this->isCsrfTokenValid('delete'.$commande->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($commande);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('app_commandes');
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
