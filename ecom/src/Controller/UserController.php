<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Securizer;


/**
 * @Route("{_locale}/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="user_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository, Securizer $securizer): Response
    {
        $isAdmin = 0;
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $user->getNom();

        if($securizer->isGranted($user, 'ROLE_ADMIN')){
            $isAdmin = 1;
        }

        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
            'isAdmin' => $isAdmin,
        ]);
    }
    /**
     * @Route("/{id}", name="user_show", methods={"GET"})
     */
    public function show(User $user, Securizer $securizer): Response
    {
        $isAdmin = 0;
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $user->getNom();

        if($securizer->isGranted($user, 'ROLE_ADMIN')){
            $isAdmin = 1;
        }

        return $this->render('user/show.html.twig', [
            'user' => $user,
            'isAdmin' => $isAdmin,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user, Securizer $securizer): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        $isAdmin = 0;
        $username = $this->get('security.token_storage')->getToken()->getUser();
        $username->getNom();

        if($securizer->isGranted($username, 'ROLE_ADMIN')){
            $isAdmin = 1;
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('compte_accueil', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'isAdmin' => $isAdmin,
        ]);
    }

}
