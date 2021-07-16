<?php


namespace App\Controller;

use App\Entity\Ville;
use App\Form\CreateCityType;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CitiesController extends AbstractController
{
    private $em;

    /**
     * @Route("/city", name="city_list")
     */
    public function list(VilleRepository $villeRepository,
                         Request $request,
                         EntityManagerInterface $entityManager): Response
    {
        $cities= $villeRepository->findAll();

        $city = new Ville();
        $cityForm = $this->createForm(CreateCityType::class, $city);

        $cityForm->handleRequest($request);

        if($cityForm->isSubmitted() && $cityForm->isValid())
        {
            $entityManager->persist($city);
            $entityManager->flush();

            $this->addFlash('success', 'Votre ville a été ajouté avec succès!');
            return $this->redirectToRoute("city_list");
        }

        return $this->render('admin/cities.html.twig', [
                'cities' => $cities,
                'cityForm' => $cityForm->createView()
            ]);
    }

    /**
     * @Route("/city/update/{id}", name="city_update")
     */
    public function update(Ville $id, Request $request):Response
    {
        $cityForm = $this->createForm(CreateCityType::class, $id);
        $cityForm->handleRequest($request);
        if($cityForm->isSubmitted() && $cityForm->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $this->addFlash('success', 'Votre ville a été modifié avec succès!');
            return $this->redirectToRoute("city_list");
        }
        return $this->render('admin/updateCity.html.twig', [
            'cityForm' => $cityForm->createView()
        ]);
    }

    /**
     * @Route("/city/delete/{id}", name="city_delete")
     */
    public function delete(Ville $id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($id);
        $em->flush();

        return $this->redirectToRoute("city_list");
    }

  /*  public function create(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        $city = new Ville();
        $cityForm = $this->createForm(CreateCityType::class, $city);

        $cityForm->handleRequest($request);

        if($cityForm->isSubmitted() && $cityForm->isValid())
        {
            $entityManager->persist($city);
            $entityManager->flush();

            $this->addFlash('success', 'Votre ville a été ajouté avec succès!');
        }

        return $this->render('admin/cities.html.twig'
            , [
                'cityForm' => $cityForm->createView()
            ]);
    }*/
}