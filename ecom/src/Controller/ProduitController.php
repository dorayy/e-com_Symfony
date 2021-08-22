<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Entity\User;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Securizer;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
* @Route("{_locale}")
*/
class ProduitController extends AbstractController
{
    /**
     * @Route("/home", name="produit_index", methods={"GET"})
     */
    public function index(ProduitRepository $produitRepository, Securizer $securizer): Response
    {
        $isAdmin = 0;
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $user->getNom();

        if($securizer->isGranted($user, 'ROLE_ADMIN')){
            $isAdmin = 1;
        }

        return $this->render('produit/index.html.twig', [
            'produits' => $produitRepository->findAll(),
            'user' => $user,
            'isAdmin' => $isAdmin,
        ]);
    }

    /**
     * @Route("/produit/new", name="produit_new", methods={"GET","POST"})
     */

    public function new(Request $request, Securizer $securizer, TranslatorInterface $t): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        $isAdmin = 0;
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $user->getNom();

        if($securizer->isGranted($user, 'ROLE_ADMIN')){
            $isAdmin = 1;
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($produit);
            $entityManager->flush();

            $this->addFlash('success', $t->trans('création effectué'));

            return $this->redirectToRoute('produit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('produit/new.html.twig', [
            'produit' => $produit,
            'form' => $form,
            'isAdmin' => $isAdmin,
        ]);
    }

    /**
     * @Route("/produit/{id}", name="produit_show", methods={"GET","POST"})
     */
    public function show(Produit $produit = null, Securizer $securizer, Request $request): Response
    {
        $isAdmin = 0;
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $user->getNom();
        $data = null;
        

        if($produit === null){
            $this->addFlash('danger', 'produit introuvable');
            return $this->redirectToRoute('produit_index');
        }

        if($securizer->isGranted($user, 'ROLE_ADMIN')){
            $isAdmin = 1;
        }

        $form = $this->createFormBuilder()
            ->add('quantite', NumberType::class, [
                'label' => 'Global.Quantite',
            ])
            ->add('Entrer', SubmitType::class, [
                'label' => 'Global.Valider',
            ])
            ->getForm();

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $qte = intval($data["quantite"]);
            $produitId = $produit->getId();

            return $this->redirectToRoute('panier_article',  ['qte' => $qte , 'produitId' => $produitId], Response::HTTP_SEE_OTHER);
        }

        return $this->render('produit/show.html.twig', [
            'produit' => $produit,
            'form' => $form->createView(),
            'isAdmin' => $isAdmin,
        ]);
    }

    /**
     * @Route("/produit/{id}/edit", name="produit_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Produit $produit,Securizer $securizer, TranslatorInterface $t): Response

    {
        $isAdmin = 0;
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $user->getNom();

        
        if($securizer->isGranted($user, 'ROLE_ADMIN')){
            $isAdmin = 1;
        }

        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', $t->trans('modification effectué'));
            
            return $this->redirectToRoute('produit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('produit/edit.html.twig', [
            'produit' => $produit,
            'form' => $form,
            'isAdmin' =>  $isAdmin,
        ]);
    }

    /**
     * @Route("/produit/{id}/delete", name="produit_delete", methods={"POST"})
     */
    public function delete(Request $request, Produit $produit, TranslatorInterface $t): Response
    {
        if ($this->isCsrfTokenValid('delete'.$produit->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($produit);
            $entityManager->flush();
        }

        $this->addFlash('warning', $t->trans('suppression effectué'));

        return $this->redirectToRoute('produit_index', [], Response::HTTP_SEE_OTHER);
    }
}
