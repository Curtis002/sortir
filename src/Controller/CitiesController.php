<?php


namespace App\Controller;

use App\Data\SearchDataAdmin;
use App\Entity\Ville;
use App\Form\CreateCityType;
use App\Form\SearchDataType;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CitiesController extends AbstractController
{
    private $em;

    // Afficher les villes
    /**
     * @Route("/admin/city", name="city_list")
     */
    public function list(VilleRepository $villeRepository,
                         Request $request,
                         EntityManagerInterface $entityManager): Response
    {
        $cities= $villeRepository->findAll();

        $city = new Ville();
        $cityForm = $this->createForm(CreateCityType::class, $city);

        $cityForm->handleRequest($request);

        if ($cityForm->isSubmitted() && $cityForm->isValid())
        {

            for ($i = 0; $i <= count($cities) - 1; $i++)
            {
                $citi = $cities[$i];
                if ( strtolower($citi->getNom()) == strtolower($cityForm->getData()->getNom()))
                {
                    $messageErrorVille = 'Votre ville existe déjà';
                    $this->addFlash('errorVille', $messageErrorVille );
                    return $this->redirectToRoute("city_list");
                }
            }
            $cityForm->getData()->setNom(ucfirst(strtolower($cityForm->getData()->getNom())));
            $entityManager->persist($city);
            $entityManager->flush();

            $this->addFlash('success', 'Votre ville a été ajouté avec succès!');
            return $this->redirectToRoute("city_list");
        }

        $data = new SearchDataAdmin();
        $form = $this->createForm(SearchDataType::class, $data);
        $form->handleRequest($request);
        $vil = $villeRepository->findSearch3($data);

        return $this->render('admin/cities.html.twig', [
                'cities' => $cities,
                'cityForm' => $cityForm->createView(),
                'vil' => $vil,
                'form' => $form->createView()
            ]);
    }

    // Mettre à jour une ville
    /**
     * @Route("/admin/city/update/{id}", name="city_update")
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

    // Supprimer une ville
    /**
     * @Route("/admin/city/delete/{id}", name="city_delete")
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