<?php


namespace App\Controller;

use App\Entity\Ville;
use App\Form\CreateCityType;
use ContainerKxmheqw\getCreateCityTypeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CitiesController extends AbstractController
{
    /**
     * @Route ("/city", name="city_create")
     */
    public function create(
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
    }
}