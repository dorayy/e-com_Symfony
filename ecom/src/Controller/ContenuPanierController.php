<?php

namespace App\Controller;

use App\Entity\ContenuPanier;
use App\Form\ContenuPanierType;
use App\Repository\ContenuPanierRepository;
use App\Repository\PanierRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/contenu/panier")
 */
class ContenuPanierController extends AbstractController
{
    /**
     * @Route("/", name="contenu_panier_index", methods={"GET"})
     */
    public function index(ContenuPanierRepository $contenuPanierRepository): Response
    {
        return $this->render('contenu_panier/index.html.twig', [
            'contenu_paniers' => $contenuPanierRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="contenu_panier_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $contenuPanier = new ContenuPanier();
        $form = $this->createForm(ContenuPanierType::class, $contenuPanier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($contenuPanier);
            $entityManager->flush();

            return $this->redirectToRoute('contenu_panier_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('contenu_panier/new.html.twig', [
            'contenu_panier' => $contenuPanier,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/show/{id}", name="contenu_panier_show", methods={"GET"})
     */
    public function show(ContenuPanier $contenuPanier): Response
    {
        return $this->render('contenu_panier/show.html.twig', [
            'contenu_panier' => $contenuPanier,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="contenu_panier_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, ContenuPanier $contenuPanier): Response
    {
        $form = $this->createForm(ContenuPanierType::class, $contenuPanier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('contenu_panier_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('contenu_panier/edit.html.twig', [
            'contenu_panier' => $contenuPanier,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/delete/{id}", name="contenu_panier_delete", methods={"POST"})
     */
    public function delete(Request $request, ContenuPanier $contenuPanier): Response
    {
        if ($this->isCsrfTokenValid('delete'.$contenuPanier->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($contenuPanier);
            $entityManager->flush();
        }

        return $this->redirectToRoute('panier_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/commande/{id}", name="contenu_show", methods={"GET"})
     */
    public function showContenuByUserId($id,ContenuPanierRepository $contenuPanierRepository, PanierRepository $panierRepository): Response
    {
        $userId = $this->get('security.token_storage')->getToken()->getUser();
        $user = $userId->getId();
        
        return $this->render('contenu_panier/commande.html.twig', [
            'user' =>  $user,
            'contenu_panier' => $contenuPanierRepository->findCommandeUser($id),
            'panier' => $panierRepository->findPanierUserAchete($id),
        ]);
    }

    /**
     * @Route("/detail/{id}", name="commande_show", methods={"GET"})
     */
    public function showCommandeByProduitId($id,ContenuPanierRepository $contenuPanierRepository): Response
    {
        $userId = $this->get('security.token_storage')->getToken()->getUser();
        $user = $userId->getId();
        
        return $this->render('commande/index.html.twig', [
            'user' =>  $user,
            'commande_detail' => $contenuPanierRepository->findCommandeDetailUser($user, $id),
        ]);
    }

}
