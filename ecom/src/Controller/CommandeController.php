<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommandeController extends AbstractController
{
    /**
     * @Route("/commande/detail/{id}", name="commande_detail")
     */
    public function Detailcommande($id): Response
    {
        return $this->redirectToRoute('commande_show', ['id' => $id], Response::HTTP_SEE_OTHER);
    }
}
