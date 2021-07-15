<?php


namespace App\Controller;

use App\Data\SearchData;
use App\Entity\Campus;
use App\Entity\Lieu;
use App\Entity\Participant;
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
        $sortie->setDateHeureDebut(new \DateTime());
        $organisateur = $this->getUser()->getId();
        $sortie->setOrganisateur($this->entityManager->getRepository(Participant::class)->findOneById($organisateur));
        $campus = $this->getUser()->getCampus();
        $sortie->setCampus($this->entityManager->getRepository(Campus::class)->findOneById($campus));



        $sortieForm = $this->createForm(CreateSortieType::class, $sortie);

//        if ($sortieForm['lieu']) {
//            dd($sortieForm['lieu']);
//            $lieu = $sortie->getLieu();
//
//            $rue = $this->entityManager->getRepository(Lieu::class)->findOneById($lieu);
//            dd($rue);
//        }

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

    /**
     * @Route("/{id}", name="join")
     */
    public function joinSortie( Sortie $sortie,EntityManagerInterface $entityManager): Response
    {
        //raz message
        $message = null;


        $userconnecte = $this->getUser();
        //recup bien l utilisateurconnecte


        $sortierepo = $entityManager->getRepository(Sortie::class);
        $id_sortie = $sortie->getId();

        $sortie = $sortierepo->find($id_sortie);
        // sort bien l objet sortie cliquée av son id

        $sorties = $sortierepo->findAll();

        if ($sortie->getParticipants()->contains($userconnecte))
        {
            $message = "Vous etes déjà inscrit à cette sortie (". $sortie->getNom() . ").";
            $this->addFlash('dejainscrit', $message);

        }
        elseif ( $sortie->getNbInscriptionsMax() == $sortie->getParticipants()->count())
        {
            $message = "Nombre de participants max atteint pour cette sortie (". $sortie->getNom() .").";
            return  $this->redirectToRoute('sortie_list', [
                "message" => $message,
                "entities" => $sorties,
            ]);
        }


        elseif ($sortie->getEtatSortie() != 3 )
        {
            $message = "Inscription à cette sortie (". $sortie->getNom() .") clôturée !.";
            $this->addFlash('fermee', $message);
        }
        else
        {

            $sortie->addParticipant($userconnecte);
            $entityManager->persist($sortie);


            $entityManager->flush();


            $this->addFlash('joinsucces', "Vous avez réussi votre inscription à la sortie \" " .$sortie->getNom() . "\" ! ");

            //todo penser a retourner vers la sortie surlaquelle on viens de s'inscrire ??
            return $this->redirectToRoute('sortie_list', [
                'sorties' => $sorties]);

        }


    }



}