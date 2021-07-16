<?php


namespace App\Controller;

use App\Entity\Campus;
use App\Form\CreateCampusType;
use App\Repository\CampusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CampusController extends AbstractController
{
    /**
     * @Route("/campus", name="campus_list")
     */
    public function list(CampusRepository $campusRepository,
                         Request $request,
                         EntityManagerInterface $entityManager): Response
    {
        $allCampus= $campusRepository->findAll();

        $campus = new Campus();
        $campForm = $this->createForm(CreateCampusType::class, $campus);

        $campForm->handleRequest($request);

        if($campForm->isSubmitted() && $campForm->isValid())
        {
            $entityManager->persist($campus);
            $entityManager->flush();

            $this->addFlash('success', 'Votre campus a été ajouté avec succès!');
        }

        return $this->render('admin/campus.html.twig'
            , [
                'allCampus' => $allCampus,
                'campForm' => $campForm->createView()
            ]);
    }

    /**
     * @Route("/campus/update/{id}", name="campus_update")
     */
    public function update(Campus $id, Request $request):Response
    {

        $campusForm = $this->createForm(CreateCampusType::class, $id);
        $campusForm->handleRequest($request);
        if($campusForm->isSubmitted() && $campusForm->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirectToRoute("campus_list");
        }
        return $this->render('admin/updateCampus.html.twig', [
            'campusForm' => $campusForm->createView()
        ]);
    }

    /**
     * @Route("/campus/delete/{id}", name="campus_delete")
     */
    public function delete(Campus $id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($id);
        $em->flush();

        return $this->redirectToRoute("campus_list");
    }
}

