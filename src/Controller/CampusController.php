<?php


namespace App\Controller;

use App\Data\SearchData;
use App\Data\SearchDataAdmin;
use App\Entity\Campus;
use App\Form\CreateCampusType;
use App\Form\SearchDataType;
use App\Form\SearchType;
use App\Repository\CampusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CampusController extends AbstractController
{

    /**
     * @Route("/admin/campus", name="campus_list")
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
            for ($i = 0; $i <= count($allCampus) - 1; $i++)
            {
                $camp = $allCampus[$i];
                if ( $camp->getNom() == $campForm->getData()->getNom())
                {
                    $messageErrorCampus = 'Votre campus existe déjà';
                    $this->addFlash('errorCampus', $messageErrorCampus );
                    return $this->redirectToRoute("campus_list");
                }
            }

            $entityManager->persist($campus);
            $entityManager->flush();

            $this->addFlash('success', 'Votre campus a été ajouté avec succès!');
            return $this->redirectToRoute("campus_list");
        }

        $data = new SearchDataAdmin();
        $form = $this->createForm(SearchDataType::class, $data);
        $form->handleRequest($request);
        $camps = $campusRepository->findSearch2($data);

        return $this->render('admin/campus.html.twig', [
                'allCampus' => $allCampus,
                'campForm' => $campForm->createView(),
                'camps' => $camps,
                'form' => $form->createView()
            ]);
    }

    /**
     * @Route("/admin/campus/update/{id}", name="campus_update")
     */
    public function update(Campus $id, Request $request):Response
    {

        $campusForm = $this->createForm(CreateCampusType::class, $id);
        $campusForm->handleRequest($request);
        if($campusForm->isSubmitted() && $campusForm->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $this->addFlash('success', 'Votre campus a été modifié avec succès!');
            return $this->redirectToRoute("campus_list");
        }
        return $this->render('admin/updateCampus.html.twig', [
            'campusForm' => $campusForm->createView()
        ]);
    }

    /**
     * @Route("/admin/campus/delete/{id}", name="campus_delete")
     */
    public function delete(Campus $id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($id);
        $em->flush();

        return $this->redirectToRoute("campus_list");
    }

//    /**
//     * @Route("/campus", name="campus_list")
//     */
//   public function index(CampusRepository $campusRepository): Response
//    {
//        $data = new SearchData();
//        $form = $this->createForm(SearchType::class, $data);
//        $camps = $campusRepository->findSearch();
//        return $this->render('admin/campus.html.twig', [
//            'camps' => $camps,
//            'form' => $form->createView()
//        ]);
//    }

}

