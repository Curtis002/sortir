<?php

namespace App\Controller;

use App\Form\ProfilType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfilController extends AbstractController
{
    /**
     * @Route("/mon-profil", name="profil")
     */
    public function index(): Response
    {
        $participant = $this->getUser();
        //$form = $this->createForm(ProfilType::class, $participant);

        return $this->render('profil/profil.html.twig');
//        return $this->render('profil/profil.html.twig', [
//            'form' => $form->createView()
//        ]);
    }
}
