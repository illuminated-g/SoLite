<?php

namespace App\Service;

use \DateTime;

use Symfony\Bundle\SecurityBundle\Security;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Challenge;
use App\Entity\ChallengeRun;
use App\Entity\Submission;
use App\Entity\AutoScorer;

use App\Repository\AutoScorerRepository;

use Carbon\Carbon;

class AutoScorerService {

    private Security $security;
    private EntityManagerInterface $em;
    private AutoScorerRepository $scorerRepo;

    private string $submissionFolder;

    public function __construct(ManagerRegistry $doctrine,
        AutoScorerRepository $scorerRepo,
        string $submissionFolder)
    {
        $this->em = $doctrine->getManager();
        $this->scorerRepo = $scorerRepo;
        $this->submissionFolder = $submissionFolder;
    }
}