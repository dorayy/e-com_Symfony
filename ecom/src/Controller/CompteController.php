<?php

namespace App\Controller;

use App\Form\UserType;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/compte")
 */
class CompteController extends AbstractController
{

    /**
     * @Route("/", name="compte_accueil")
     */
    public function index(): Response
    {
        $userId = $this->get('security.token_storage')->getToken()->getUser();
        $user = $userId->getNom();

        return $this->render('compte/index.html.twig', [ 'nom' => $user ]);
    }
    /**
     * @Route("/edit", name="compte_edit")
     */
    public function compte(): Response
    {
        $userId = $this->get('security.token_storage')->getToken()->getUser();
        $user = $userId->getId();

        return $this->redirectToRoute('user_edit', ['id' => $user], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/commande", name="compte_commande")
     */
    public function commande(): Response
    {
        $userId = $this->get('security.token_storage')->getToken()->getUser();
        $user = $userId->getId();
        
        return $this->redirectToRoute('contenu_show', ['id' => $user], Response::HTTP_SEE_OTHER);
    }

}
