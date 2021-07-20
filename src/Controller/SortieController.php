<?php


namespace App\Controller;

use App\Data\SearchData;
use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Entity\Ville;
use App\Form\CreateSortieType;
use App\Repository\ParticipantRepository;
use App\Form\SearchType;
use App\Repository\SortieRepository;
use App\Service\Statutchecker;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;


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
    public function list(SortieRepository $sortieRepository,EntityManagerInterface $entityManager, Request $request, Statutchecker $statutchecker)
    {
        $current = $this->getUser();
        $participant = $this->entityManager->getRepository(Participant::class)->find($current);
        //dump($participant);

        $etat = $this->entityManager->getRepository(Etat::class)->find(5);

        $data = new SearchData();
        $sortieForm = $this->createForm(SearchType::class, $data);
        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            $sorties = $sortieRepository->findSearch($data, $participant,$etat);

        } else {
            $sorties = $sortieRepository->findAll();
        }

        /////----test statutchecker-----////////
        $statutchecker->statutClotureeSortie($sorties, $entityManager);
        /////----test statutchecker-----////////


        return $this->render('sortie/list.html.twig', [
            'sorties'=>$sorties,
            'sortiesForm'=>$sortieForm->createView()
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
     * @Route("/modifier-sortie/{id}", name="modifier_sortie")
     */
    public function modify(int $id, Request $request): Response
    {
        $sortie = $this->entityManager->getRepository(Sortie::class)->findOneById($id);

        $lieu1 = new Lieu();
        $lieux = new ArrayCollection();
        $lieux->add($lieu1);

        $sortie->setLieux($lieux);

        $sortieForm = $this->createForm(CreateSortieType::class, $sortie);
        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            //dd($request);
            // Si lieu existant
            if ($request->request->get('create_sortie')['lieux'][0]["nom"] !== "") {
                $lieu1->setNom($request->request->get('create_sortie')['lieux'][0]['nom']);
                $lieu1->setRue($request->request->get('create_sortie')['lieux'][0]['rue']);
                $idVille = (int)($request->request->get('create_sortie')['ville']);
                $lieu1->setVille($this->entityManager->getRepository(Ville::class)->findOneById($idVille));
                $this->entityManager->persist($lieu1);
                $sortie->setLieu($lieu1);
            }

            // Savoir si on enregistre ou publie la sortie
            $clicked = $request->request->get('clicked');

            if ($clicked == 'enregistrer') {
                $sortie->setEtatSortie($this->entityManager->getRepository(Etat::class)->findOneById(1));
            } else {
                $sortie->setEtatSortie($this->entityManager->getRepository(Etat::class)->findOneById(2));
            }

            $this->entityManager->persist($sortie);
            $this->entityManager->flush();


            $message = "La sortie a bien été mise à jour";
            $this->addFlash('maj', $message);

            return $this->redirectToRoute('sortie_list');
        }

        return $this->render('sortie/create.html.twig', [
            'sortie' => $sortie,
            "sortieForm" => $sortieForm->createView()
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
        //dd($request);
        if($sortieForm->isSubmitted() && $sortieForm->isValid())
        {
            // Si lieu existant
            if ($request->request->get('create_sortie')['lieu'] !== "") {
                $idLieu = (int)$request->request->get('create_sortie')['lieu'];
                $lieu1 = $this->entityManager->getRepository(Lieu::class)->findOneById($idLieu);
            } else {
                $lieu1->setNom($request->request->get('create_sortie')['lieux'][0]['nom']);
                $lieu1->setRue($request->request->get('create_sortie')['lieux'][0]['rue']);
                $idVille = (int)($request->request->get('create_sortie')['ville']);
                $lieu1->setVille($this->entityManager->getRepository(Ville::class)->findOneById($idVille));
            }

            // Savoir si on enregistre ou publie la sortie
            $clicked = $request->request->get('clicked');

            if ($clicked == 'enregistrer') {
                $sortie->setEtatSortie($this->entityManager->getRepository(Etat::class)->findOneById(1));
                $message = "Votre sortie a bien été enregistrée";
                $this->addFlash('enregistree', $message);
            } else {
                $sortie->setEtatSortie($this->entityManager->getRepository(Etat::class)->findOneById(2));
                $message = "Votre sortie a bien été publiée";
                $this->addFlash('publiee', $message);
            }

            $entityManager->persist($lieu1);
            $sortie->setLieu($lieu1);
            dump($sortie);
            $entityManager->persist($sortie);
            $entityManager->flush();

            return $this->redirectToRoute('sortie_list');
        }

        return $this->render('sortie/create.html.twig'
        , [
                'sortieForm' => $sortieForm->createView(),

        ]);
    }

    /**
     * @Route("/supprimer-sortie/{id}", name="delete")
     */
    public function delete($id): Response
    {
        echo "passe ici";
        $sortie = $this->entityManager->getRepository(Sortie::class)->findOneById($id);
        dump($sortie);
        $this->entityManager->remove($sortie);
        $this->entityManager->flush();

        return $this->redirectToRoute('sortie_list');

    }



    /**
     * @Route("/join/{id}", name="join")
     */
    public function joinSortie( Sortie $sortie,EntityManagerInterface $entityManager): Response
    {
        //raz message
        $message = null;
        /*1 En création
        2 Ouverte
        3 Cloturée
        4 Activité en cours
        5 Passée
        6 Annulée*/

        $userconnecte = $this->getUser();
        //recup bien l utilisateurconnecte


        $sortierepo = $entityManager->getRepository(Sortie::class);
        $id_sortie = $sortie->getId();
        $etatrepo = $entityManager->getRepository(Etat::class);
        $etat = $etatrepo->find(3);

        $sortie = $sortierepo->find($id_sortie);
        // sort bien l objet sortie cliquée av son id

        $sorties = $sortierepo->findAll();

        // voir id dans bdd pour cloturée = 3
        if ($sortie->getEtatSortie()->getId() == 3 )
        {
            $message = "Inscription à cette sortie (". $sortie->getNom() .") clôturée !.";
            $this->addFlash('cloturee', $message);
        }

        elseif ( $sortie->getNbInscriptionsMax() == $sortie->getParticipants()->count()) {
            $message = "Nombre de participants max atteint pour cette sortie (" . $sortie->getNom() . ").";
            $this->addFlash('maxatteint', $message);
        }
        elseif ($sortie->getParticipants()->contains($userconnecte))
        {
            $message = "Vous etes déjà inscrit à cette sortie (". $sortie->getNom() . ").";
            $this->addFlash('dejainscrit', $message);
        }
        elseif ($sortie->getEtatSortie()->getId() == 2 and  $sortie->getNbInscriptionsMax()-1 == $sortie->getParticipants()->count() )
        {
            $sortie->addParticipant($userconnecte);
            $sortie->setEtatSortie($etat);

            $entityManager->persist($sortie);
            $entityManager->flush();
            $this->addFlash('joinsucces', "Vous avez réussi votre inscription à la sortie \" " .$sortie->getNom() . "\" ! ");
        }
    elseif ($sortie->getEtatSortie()->getId() == 2 )
        {
        $sortie->addParticipant($userconnecte);

        $entityManager->persist($sortie);
        $entityManager->flush();
        $this->addFlash('joinsucces', "Vous avez réussi votre inscription à la sortie \" " .$sortie->getNom() . "\" ! ");
    }

        //todo penser a retourner vers la sortie surlaquelle on viens de s'inscrire ??
        return $this->redirectToRoute('sortie_list', [
            'sorties' => $sorties]);




    }

    /**
     * @Route("/escape/{id}", name="escape")
     */
    public function escapeSortie( Sortie $sortie,EntityManagerInterface $entityManager): Response
    {
        //raz message
        $message = null;


        $userconnecte = $this->getUser();
        //recup bien l utilisateurconnecte

        $sortierepo = $entityManager->getRepository(Sortie::class);
        $id_sortie = $sortie->getId();

        $etatrepo = $entityManager->getRepository(Etat::class);
        $etat = $etatrepo->find(2);


        $sortie = $sortierepo->find($id_sortie);
        // sort bien l objet sortie cliquée av son id

        $sorties = $sortierepo->findAll();

        if ( $sortie->getParticipants()->contains($userconnecte) and $sortie->getEtatSortie()->getId() == 3 and date("now") < $sortie->getDateLimiteInscription())
        {

            $sortie->removeParticipant($userconnecte);

            $sortie->setEtatSortie($etat);

            $entityManager->persist($sortie);
            $entityManager->flush();

            $message = "Vous vous etes bien desinscrit a la sortie (". $sortie->getNom() . ").";
            $this->addFlash('deinscrit', $message);
        }elseif ($sortie->getParticipants()->contains($userconnecte) and $sortie->getEtatSortie()->getId() == 2)
        {

            $sortie->removeParticipant($userconnecte);

            $entityManager->refresh($sortie);
            $entityManager->flush();

            $message = "Vous vous etes bien desinscrit a la sortie (". $sortie->getNom() . ").";
            $this->addFlash('deinscrit', $message);

        }

        return $this->redirectToRoute('sortie_list', [
            "message" => $message,
            "entities" => $sorties,
        ]);

    }



}