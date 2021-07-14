<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ProfilType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProfilController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

//    /**
//     * @Route("/mon-profil", name="profil")
//     */
//    public function modifier(Request $request, UserPasswordEncoderInterface $encoder): Response
//    {
//        $participant = $this->getUser();
//        $form = $this->createForm(ProfilType::class, $participant);
//
//        $form->handleRequest($request);
//        if ($form->isSubmitted() && $form->isValid()) {
//            $old_pwd = $form->get('oldPassword')->getData();
//            if ($encoder->isPasswordValid($participant, $old_pwd)) {
//                $new_pwd = $form->get('newPassword')->getData();
//                $password = $encoder->encodePassword($participant, $new_pwd);
//
//                $participant->setPassword($password);
//                $this->entityManager->flush();
//
//            }
//        }
//
//        return $this->render('profil/profil.html.twig', [
//            'profilForm' => $form->createView()
//                ]
//        );
//    }

    /**
     * @Route("mon-profil/{id}/edit", name="participant_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Participant $participant, UserPasswordEncoderInterface $passwordEncoder, SluggerInterface $slugger): Response
    {

        $form = $this->createForm(ProfilType::class, $participant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
                $photoProfilFile = $form->get('photoProfil')->getData();

            if ($photoProfilFile) {
                $originalFilename = pathinfo($photoProfilFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $photoProfilFile->guessExtension();
                // Move the file to the directory where avatars are stored
                try {
                    $photoProfilFile->move(
                        $this->getParameter('photo_profil_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $participant->setPhotoProfil($newFilename);
            } else {
                $existingFile = $participant->getPhotoProfil();
                $participant->setPhotoProfil($existingFile);
            }
            // encode the plain password
            $participant->setMotPasse(
                $passwordEncoder->encodePassword(
                    $participant,
                    $form->get('motPasse')->getData()
                )
            );

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('participant_show', ['id' => $participant->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('profil/profil.html.twig', [
            'participant' => $participant,
            'profilForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("mon-profil/{id}", name="participant_show", methods={"GET"})
     */
    public function show(Participant $participant): Response
    {
        return $this->render('profil/profilDetails.html.twig', [
            'participant' => $participant,
        ]);
    }
}
