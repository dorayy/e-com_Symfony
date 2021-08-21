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
use App\Service\Securizer;
use DateTime;

/**
 * @Route("{_locale}/panier")
 */
class PanierController extends AbstractController
{
    /**
     * @Route("/", name="panier_index", methods={"GET"})
     */
    public function index(PanierRepository $panierRepository,Securizer $securizer): Response
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $user->getId();

        $isAdmin = 0;
        $username = $this->get('security.token_storage')->getToken()->getUser();
        $username->getNom();

        if($securizer->isGranted($username, 'ROLE_ADMIN')){
            $isAdmin = 1;
        }

        return $this->render('panier/index.html.twig', [
            'paniers' => $panierRepository->findPanierUser($user),
            'isAdmin' =>  $isAdmin,
        ]);
    }

    /**
     * @Route("/new", name="panier_new", methods={"GET","POST"})
     */
    public function new(Request $request, Securizer $securizer): Response
    {
        $panier = new Panier();
        $form = $this->createForm(PanierType::class, $panier);
        $form->handleRequest($request);

        $isAdmin = 0;
        $username = $this->get('security.token_storage')->getToken()->getUser();
        $username->getNom();

        if($securizer->isGranted($username, 'ROLE_ADMIN')){
            $isAdmin = 1;
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($panier);
            $entityManager->flush();

            return $this->redirectToRoute('panier_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('panier/new.html.twig', [
            'panier' => $panier,
            'form' => $form,
            'isAdmin' =>  $isAdmin,
        ]);
    }

    /**
     * @Route("/show/{id}", name="panier_show", methods={"GET"})
     */
    public function show(Panier $panier, Securizer $securizer): Response
    {
        $isAdmin = 0;
        $username = $this->get('security.token_storage')->getToken()->getUser();
        $username->getNom();

        if($securizer->isGranted($username, 'ROLE_ADMIN')){
            $isAdmin = 1;
        }

        return $this->render('panier/show.html.twig', [
            'panier' => $panier,
            'isAdmin' =>  $isAdmin,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="panier_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Panier $panier, Securizer $securizer): Response
    {
        $form = $this->createForm(PanierType::class, $panier);
        $form->handleRequest($request);

        $isAdmin = 0;
        $username = $this->get('security.token_storage')->getToken()->getUser();
        $username->getNom();

        if($securizer->isGranted($username, 'ROLE_ADMIN')){
            $isAdmin = 1;
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('panier_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('panier/edit.html.twig', [
            'panier' => $panier,
            'form' => $form,
            'isAdmin' =>  $isAdmin,
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

        $userId = $this->get('security.token_storage')->getToken()->getUser();
        $userId->getId();

        // Vérification si panier deja existant
        $panierUser = $panierRepository->findPanierUser($user);
        $entityManager = $this->getDoctrine()->getManager();

        // Récupération params
        $qte = $request->query->get('qte');
        $produitId = $request->query->get('produitId');

        // Récupération du produit
        $produit = $produitRepository->find($produitId);

        // Création du contenu Panier
        $newContenu = new ContenuPanier();

        if(empty($panierUser)){ 
            // Création du panier
            $panier = new Panier();
            $panier->setUtilisateur($user);

            $entityManager->persist($panier);
            $entityManager->flush();

            $newContenu->setPanier($panier);
            $newContenu->setQuantite($qte);
            $newContenu->setProduit($produit);
    
            $entityManager->persist($newContenu);
            $entityManager->flush();

        }else{
            $panier = $panierRepository->findOneBy(array('utilisateur' => $userId),array('id'=>'DESC'),1,0);
      
            $newContenu->setPanier($panier);
            $newContenu->setQuantite($qte);
            $newContenu->setProduit($produit);
    
            $entityManager->persist($newContenu);
            $entityManager->flush();
        }

        return $this->redirectToRoute('panier_index', [], Response::HTTP_SEE_OTHER);
    }

     /**
     * @Route("/achat/{id}", name="panier_achat", methods={"GET","POST"})
     */
    public function achat(PanierRepository $panierRepository ,Request $request): Response
    {
        // Récupération du user
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $user->getId();

        // Acces database
        $entityManager = $this->getDoctrine()->getManager();

        // Recherche et maj concernant l'achat
        $panier = $panierRepository->findOneBy(array('utilisateur' => $user),array('id'=>'DESC'),1,0);
        $panier->setDateAchat(new \DateTime("now"));
        $panier->setEtat(1);

        // Envoie data vers la base
        $entityManager->persist($panier);
        $entityManager->flush();

        // Création d'un new panier apres commande
        if($panier->getEtat() == true){
            $userT = $this->get('security.token_storage')->getToken()->getUser();

            $panier = new Panier();
            $panier->setUtilisateur($userT);
            
            $entityManager->persist($panier);
            $entityManager->flush();
        }

        return $this->redirectToRoute('panier_index', [], Response::HTTP_SEE_OTHER);
    }


}
