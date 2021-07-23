<?php


namespace App\Controller;

use App\Data\SearchData;
use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Entity\Ville;
use App\Form\CancelSortieType;
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
        $statutchecker->statutSetteurStatut($sorties, $entityManager);
        /////----test statutchecker-----////////


        return $this->render('sortie/list.html.twig', [
            'sorties'=>$sorties,
            'sortiesForm'=>$sortieForm->createView()
        ]);
    }

    // Afficher le détail d'une sortie
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

    // Créer une sortie
    /**
     * @Route ("/create", name="create")
     */

    public function create(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        $sortie = new Sortie();

        // Récupération de l'id de l'utilisateur en cours
        $organisateur = $this->getUser()->getId();
        $sortie->setOrganisateur($this->entityManager->getRepository(Participant::class)->findOneById($organisateur));

        // Récupération du campus de l'utilisateur
        $campus = $this->getUser()->getCampus();
        $sortie->setCampus($this->entityManager->getRepository(Campus::class)->findOneById($campus));

        $sortieForm = $this->createForm(CreateSortieType::class, $sortie);
        $sortieForm->handleRequest($request);

        // Récupération du lieu du formulaire imbriqué
        $lieuInsere = $sortieForm->get('lieux')->getData();

        if($sortieForm->isSubmitted() && $sortieForm->isValid())
        {
            // Si on est dans le cas d'une insertion d'un nouveau lieu
            if ($lieuInsere != null ) {
                $this->entityManager->persist($lieuInsere);
                $sortie->setLieu($lieuInsere);
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

            $entityManager->persist($sortie);
            $entityManager->flush();

            return $this->redirectToRoute('sortie_list');
        }

        return $this->render('sortie/create.html.twig'
            , [
                'sortieForm' => $sortieForm->createView(),

            ]);
    }

    // Modifier une sortie
    /**
     * @Route("/modifier-sortie/{id}", name="modifier_sortie")
     */
    public function modify(int $id, Request $request): Response
    {
        // Récupération de la sortie
        $sortie = $this->entityManager->getRepository(Sortie::class)->findOneById($id);

        $sortieForm = $this->createForm(CreateSortieType::class, $sortie);
        $sortieForm->handleRequest($request);

        // Récupération du lieu du formulaire imbriqué
        $lieuInsere = $sortieForm->get('lieux')->getData();

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {

            // Si on est dans le cas d'une insertion d'un nouveau lieu
            if ($lieuInsere != null ) {
                $this->entityManager->persist($lieuInsere);
                $sortie->setLieu($lieuInsere);
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

    // Supprimer une sortie
    /**
     * @Route("/supprimer-sortie/{id}", name="delete")
     */
    public function delete($id): Response
    {
        $sortie = $this->entityManager->getRepository(Sortie::class)->findOneById($id);
        dump($sortie);
        $this->entityManager->remove($sortie);
        $this->entityManager->flush();

        return $this->redirectToRoute('sortie_list');

    }

    // Annuler une sortie
    /**
     * @Route("/annuler-sortie/{id}", name="cancel")
     */
    public function cancel($id, Request $request): Response
    {
        $sortie = $this->entityManager->getRepository(Sortie::class)->findOneById($id);

        $cancelForm = $this->createForm(CancelSortieType::class, $sortie);
        $cancelForm->handleRequest($request);

        if ($cancelForm->isSubmitted() && $cancelForm->isValid()) {
            $sortie->setEtatSortie($this->entityManager->getRepository(Etat::class)->findOneById(6));
            $this->entityManager->persist($sortie);
            $this->entityManager->flush();

            return $this->redirectToRoute('sortie_list');
        }

        return $this->render('sortie/cancel.html.twig', [
            'sortie' => $sortie,
            "cancelForm" => $cancelForm->createView()
        ]);

    }

    // S'inscrire à une sortie
    /**
     * @Route("/join/{id}", name="join")
     */
    public function joinSortie( Sortie $sortie,EntityManagerInterface $entityManager): Response
    {
        //raz message
        $message = null;

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

        return $this->redirectToRoute('sortie_list', [
            'sorties' => $sorties]);

    }

    // Se désister d'une sortie
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