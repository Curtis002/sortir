<?php


namespace App\Controller;

use App\Entity\Sortie;
use App\Form\CreateSortieType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends AbstractController
{
    /**
     * @Route ("/sortie/create", name="sortie_create")
     */

    public function create(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        $sortie = new Sortie();
        $sortie->setDateHeureDebut(new \DateTime());

        $sortieForm = $this->createForm(CreateSortieType::class, $sortie);
        $sortieForm->handleRequest($request);

        if($sortieForm->isSubmitted() && $sortieForm->isValid())
        {
            $entityManager->persist($sortie);
            $entityManager->flush();

            $this->addFlash('success', 'Félicitation, votre sortie a été créée !!');
        }

        return $this->render('sortie/create.html.twig'
        , [
                  'sortieForm' => $sortieForm->createView()
        ]);
    }

}