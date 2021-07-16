<?php


namespace App\Controller;

use App\Data\SearchData;
use App\Entity\Campus;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Entity\Ville;
use App\Form\CreateSortieType;
use App\Form\LieuType;
use App\Form\SearchType;
use App\Repository\SortieRepository;
use Doctrine\Common\Collections\ArrayCollection;
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
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/", name="list")
     */
    public function list()
    {
        $sorties = $this->entityManager->getRepository(Sortie::class)->findAll();

        return $this->render('sortie/list.html.twig', [
            'sorties' => $sorties
        ]);

//        $data = new SearchData();
//        $data->page = $request->get('page', 1);
//
//        $sortieForm = $this->createForm(SearchType::class, $data);
//
//        $sortieForm->handleRequest($request);
//
//        $sorties = $sortieRepository->findSearch($data);
//
//        return $this->render('sortie/list.html.twig', [
//            'sorties'=>$sorties,
//            'sortiesForm'=>$sortieForm->createView()
//        ]);
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

        $lieu1 = new Lieu();
        $sortie->getLieux()->add($lieu1);

        $sortie->setDateHeureDebut(new \DateTime());
        $organisateur = $this->getUser()->getId();
        $sortie->setOrganisateur($this->entityManager->getRepository(Participant::class)->findOneById($organisateur));
        $campus = $this->getUser()->getCampus();
        $sortie->setCampus($this->entityManager->getRepository(Campus::class)->findOneById($campus));

        $sortieForm = $this->createForm(CreateSortieType::class, $sortie);
        $sortieForm->handleRequest($request);

        if($sortieForm->isSubmitted() && $sortieForm->isValid())
        {
            // Si lieu existant
            if ($request->request->get('create_sortie')['lieuSortie'] !== "") {
                $idLieu = (int)$request->request->get('create_sortie')['lieuSortie'];
                $lieu1 = $this->entityManager->getRepository(Lieu::class)->findOneById($idLieu);
            } else {
                $lieu1->setNom($request->request->get('create_sortie')['lieux'][0]['nom']);
                $lieu1->setRue($request->request->get('create_sortie')['lieux'][0]['rue']);
                $idVille = (int)($request->request->get('create_sortie')['ville']);
                $lieu1->setVille($this->entityManager->getRepository(Ville::class)->findOneById($idVille));
            }
            $entityManager->persist($lieu1);
            $sortie->setLieu($lieu1);
            dump($sortie);
            $entityManager->persist($sortie);
            $entityManager->flush();
        }

        return $this->render('sortie/create.html.twig'
        , [
                'sortieForm' => $sortieForm->createView(),

        ]);
    }

}