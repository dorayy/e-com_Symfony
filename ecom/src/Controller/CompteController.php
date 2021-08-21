<?php

namespace App\Controller;

use App\Form\UserType;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Securizer;
use App\Repository\UserRepository;

/**
 * @Route("{_locale}/compte")
 */
class CompteController extends AbstractController
{

    /**
     * @Route("/", name="compte_accueil")
     */
    public function index(Securizer $securizer): Response
    {
        // Récupération du user
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $isSuperAdmin  = 0;

        // Vérification si l'user fait partie du role super admin
        if($securizer->isGranted($user, 'ROLE_SUPER_ADMIN')){
            $isSuperAdmin  = 1;
        }

        return $this->render('compte/index.html.twig', [ 
            'nom' => $user,
            'isSuperAdmin' => $isSuperAdmin
        ]);
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

    /**
     * @Route("/user", name="compte_user")
     */
    public function listeUser(UserRepository $user): Response
    {
        return $this->render('compte/user.html.twig', [ 
            'user' =>$user->findUserInscrit()
        ]);
    }

}
