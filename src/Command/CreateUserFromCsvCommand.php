<?php

namespace App\Command;

use App\Entity\Participant;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\YamlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class CreateUserFromCsvCommand extends Command
{
    private EntityManagerInterface $entityManager;

    private string $dataDirectory;

    private SymfonyStyle $io;

    private ParticipantRepository $participantRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        string $dataDirectory,
        ParticipantRepository $participantRepository)
    {
        parent::__construct();
        $this->dataDirectory = $dataDirectory;
        $this->entityManager = $entityManager;
        $this->participantRepository = $participantRepository;
    }

    protected static $defaultName = 'app:create-users-from-file';
    protected static $defaultDescription = 'Importer des données en provenance d\'un fichier CSV';

    protected function configure(): void
    {
        $this->setDescription(self::$defaultDescription);
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->createUsers();

        return Command::SUCCESS;
    }

    private function getDataFromFile(): array
    {
        $file = $this->dataDirectory . 'particpant2.csv';

        $fileExtension = pathinfo($file, PATHINFO_EXTENSION);

        $normalizers = [new ObjectNormalizer()];

        $encoders = [
            new CsvEncoder(),
            new XmlEncoder(),
            new YamlEncoder()
        ];

        $serializer = new Serializer($normalizers, $encoders);

        /** @var string $fileString */
        $fileString = file_get_contents($file);

        $data = $serializer->decode($fileString, $fileExtension);

        if(array_key_exists('results', $data)){
            return $data['results'];
        }
        return $data;
    }

    private function createUsers(): void
    {
        $this->io->section('CREATION DES UTILISATEURS A PARTIR DU FICHIER').

        $userCreated = 0;

        foreach ($this->getDataFromFile() as $row){
            //on verifie la cle email et si ce n est pas vide
            if (array_key_exists('email', $row) && !empty($row['email'])) {
                $user = $this->participantRepository->findOneBy([
                    'email' => $row['email']
                ]);

                //si le user n existe pas deeja alors on le cree
                if(!$user){
                    $user = new Participant();

                    $user->setMail($row['email'])
                        ->setPassword('blablabla');

                    $this->entityManager->persist($user);

                    $userCreated++;
                }
            }
        }
        $this->entityManager->flush();

        //on verifie le nombre de participant cree
        if($userCreated > 1){
            $string = "{$userCreated} UTILISATEURS CREES EN BASE DE DONNEEES.";
        }elseif ($userCreated === 1){
            $string = '1 UTILISATEUR CREES EN BASE DE DONNEEES.';
        }else {
            $string = 'AUCUN UTILISATEUR N\'A ETE CREE EN BASE DE DONNEES.';
        }

        $this->io->success($string);

    }
}
