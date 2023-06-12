<?php

namespace App\Service;

use Symfony\Bundle\SecurityBundle\Security;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Challenge;
use App\Entity\ChallengeRun;

use Carbon\Carbon;

class ChallengeService {

    private Security $security;
    private EntityManagerInterface $em;
    private ChallengeRepository $chalRepo;
    private ChallengeRunRepository $runRepo;

    public function __construct( Security $security, ManagerRegistry $doctrine, ChallengeRepository $chalRepo, ChallengeRunRepository $runRepo)
    {
        $this->security = $security;
        $this->em = $doctrine->getManager();
        $this->chalRepo = $chalRepo;
        $this->runRepo = $runRepo;
    }

    public function groupedList(): array
    {
        $grouped = [];
        $grouped['active'] = [];
        $grouped['next'] = [];
        $grouped['inactive'] = [];

        //first query all available challenges
        $available = $this->chalRepo->findBy([
            'available' => true
        ]);

        //query challenge runs that haven't ended yet

        $runs = $this->runRepo->findBy([

        ]);

        return $grouped;
    }
}