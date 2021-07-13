<?php


namespace App\Controller;

use App\Data\SearchData;
use App\Entity\Sortie;
use App\Form\CreateSortieType;
use App\Form\SearchType;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/sorties", name="sortie_")
 */
class SortieController extends AbstractController
{

    /**
     * @Route("/", name="list")
     */
    public function list(SortieRepository $sortieRepository, Request $request): Response
    {
        $data = new SearchData();
        $data->page = $request->get('page', 1);

        $sortieForm = $this->createForm(SearchType::class, $data);

        $sortieForm->handleRequest($request);

        $sorties = $sortieRepository->findSearch($data);

        return $this->render('sortie/list.html.twig', [
            'sorties'=>$sorties,
            'form'=>$sortieForm->createView()
        ]);
    }

    /**
     * @Route("/detail/{id}", name="detail")
     */
    public function detail(int $id, SortieRepository $sortieRepository): Response
    {
        $sorties = $sortieRepository->find($id);

        return $this->render('sortie/detail.html.twig', [
            "sorties"=>$sorties
        ]);
    }

    /**
     * @Route ("/create", name="create")
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