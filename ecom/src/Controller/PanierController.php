<?php

namespace App\Controller;

use App\Entity\ContenuPanier;
use App\Entity\Panier;
use App\Entity\User;
use App\Form\PanierType;
use App\Repository\PanierRepository;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/panier")
 */
class PanierController extends AbstractController
{
    /**
     * @Route("/", name="panier_index", methods={"GET"})
     */
    public function index(PanierRepository $panierRepository): Response
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $user->getId();

        return $this->render('panier/index.html.twig', [
            'paniers' => $panierRepository->findPanierUser($user),
        ]);
    }

    /**
     * @Route("/new", name="panier_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $panier = new Panier();
        $form = $this->createForm(PanierType::class, $panier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($panier);
            $entityManager->flush();

            return $this->redirectToRoute('panier_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('panier/new.html.twig', [
            'panier' => $panier,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/show/{id}", name="panier_show", methods={"GET"})
     */
    public function show(Panier $panier): Response
    {
        return $this->render('panier/show.html.twig', [
            'panier' => $panier,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="panier_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Panier $panier): Response
    {
        $form = $this->createForm(PanierType::class, $panier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('panier_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('panier/edit.html.twig', [
            'panier' => $panier,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="panier_delete", methods={"POST"})
     */
    public function delete(Request $request, Panier $panier): Response
    {
        if ($this->isCsrfTokenValid('delete'.$panier->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($panier);
            $entityManager->flush();
        }

        return $this->redirectToRoute('panier_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/panier_article", name="panier_article", methods={"GET"})
     */
    public function addArticleToPanier(PanierRepository $panierRepository,Request $request,ProduitRepository $produitRepository): Response
    {
        // Récupération du user de session 
        $user = $this->get('security.token_storage')->getToken()->getUser();
        // Création d'un panier
        $newPanier = new Panier();
        $newPanier->setUtilisateur($user);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($newPanier);
        $entityManager->flush();

        // Récupération params
        $qte = $request->query->get('qte');
        $produitId = $request->query->get('produitId');

        // Récupération du produit
        $produit = $produitRepository->find($produitId);

        // Création du contenu Panier
        $newContenu = new ContenuPanier();
        $newContenu->setQuantite($qte);
        $newContenu->setPanier($newPanier);
        $newContenu->setProduit($produit);

        $entityManager->persist($newContenu);
        $entityManager->flush();

        return $this->redirectToRoute('panier_index', [], Response::HTTP_SEE_OTHER);
    }

}
