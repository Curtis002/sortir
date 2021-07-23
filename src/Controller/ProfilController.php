<?php

namespace App\Controller;

use App\Data\SearchDataAdmin;
use App\Data\SearchDataParticipant;
use App\Entity\Participant;
use App\Form\CreateParticipantType;
use App\Form\ProfilType;
use App\Form\RegistrationFormType;
use App\Form\SearchDataType;
use App\Form\UploadUserType;
use App\Repository\ParticipantRepository;
use App\Service\FileUploader;
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

    // Editer un profil
    /**
     * @Route("mon-profil/{id}/edit", name="participant_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Participant $participant, UserPasswordEncoderInterface $passwordEncoder, FileUploader $fileUploader): Response
    {

        $form = $this->createForm(ProfilType::class, $participant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
                $photoProfilFile = $form->get('photoProfil')->getData();


            if ($photoProfilFile) {
                $photoProfil = $fileUploader->upload($photoProfilFile);
                $participant->setPhotoProfil($photoProfil);

            }

            /*if ($photoProfilFile) {
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
            }*/
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

    // Afficher un profil
    /**
     * @Route("mon-profil/{id}", name="participant_show", methods={"GET"})
     */
    public function show(Participant $participant): Response
    {
        return $this->render('profil/profilDetails.html.twig', [
            'participant' => $participant,
        ]);
    }

    // Afficher la liste des participants
    /**
     * @Route("/admin/participants", name="participants_list")
     */
    public function list(ParticipantRepository $participantRepository,
                         Request $request,
                         EntityManagerInterface $entityManager,
                         UserPasswordEncoderInterface $passwordEncoder,
                         FileUploader $fileUploader
    ): Response

    {
        $participantManu = new Participant();
        $participantManu->setPhotoProfil("150.png");
        $partForm = $this->createForm(CreateParticipantType::class, $participantManu);

        $partForm->handleRequest($request);

        if($partForm->isSubmitted() && $partForm->isValid())
        {
            //dd($partForm->get('roles')->getData());
            if ($partForm->get('roles')->getData()[0] == "ROLE_USER"){
                $participantManu->setRoles(['ROLE_USER']);
            } else {
                $participantManu->setRoles(['ROLE_ADMIN']);
            }
            $participantManu->setMotPasse(
                $passwordEncoder->encodePassword(
                    $participantManu,
                    $partForm->get('motPasse')->getData()
                )
            );
            //dd($partForm->getData());
            $entityManager->persist($participantManu);
            $entityManager->flush();

            $this->addFlash('success', 'Un nouveau participant a été ajouté avec succès!');
            return $this->redirectToRoute("participants_list");
        }

        $data = new SearchDataAdmin();
        $form = $this->createForm(SearchDataType::class, $data);
        $form->handleRequest($request);
        $participants = $participantRepository->findSearch4($data);

        $uploadForm = $this->createForm(UploadUserType::class);
        $uploadForm->handleRequest($request);

        if ($uploadForm->isSubmitted() && $uploadForm->isValid())
        {
            $uploadPartFile = $uploadForm['uploadUserFile']->getData();
            if ($uploadPartFile)
            {
                $uploadPart = $fileUploader->uploadBis($uploadPartFile);
                if (null !== $uploadPart)
                {
                    $directory = $fileUploader->getUploadDirectory();
                    $fullpath = $directory.'/'.$uploadPart;
                }
            }
        }

        return $this->render('admin/participants.html.twig', [
            'partForm' => $partForm->createView(),
            'participants' => $participants,
            'form' => $form->createView(),
            'uploadForm' => $uploadForm->createView(),
        ]);
    }

    // Mettre à jour un participants
    /**
     * @Route("/admin/participant/update/{id}", name="participant_update")
     */
    public function update(Participant $id, Request $request):Response
    {

        $partForm = $this->createForm(CreateParticipantType::class, $id);
        $partForm->handleRequest($request);
        if($partForm->isSubmitted() && $partForm->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $this->addFlash('success', 'Les informations ont été modifié avec succès!');
            return $this->redirectToRoute("participants_list");
        }
        return $this->render('admin/updateParticipant.html.twig', [
            'partForm' => $partForm->createView()
        ]);
    }

    // Supprimer un participant
    /**
     * @Route("/admin/participant/delete/{id}", name="participant_delete")
     */
    public function delete(Participant $id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($id);
        $em->flush();

        return $this->redirectToRoute("participants_list");
    }

}
