<?php

namespace App\Service;

use \DateTime;

use Doctrine\ORM\Query\Expr\GroupBy;
use Symfony\Bundle\SecurityBundle\Security;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\User\UserInterface;

use App\Entity\Challenge;
use App\Entity\ChallengeRun;
use App\Entity\Submission;

use App\Repository\ChallengeRepository;
use App\Repository\ChallengeRunRepository;

use Psr\Log\LoggerInterface;

use Carbon\Carbon;

class ChallengeService {

    private Security $security;
    private EntityManagerInterface $em;
    private ChallengeRepository $chalRepo;
    private ChallengeRunRepository $runRepo;

    private string $submissionFolder;

    private LoggerInterface $logger;

    public function __construct(Security $security, ManagerRegistry $doctrine,
        ChallengeRepository $chalRepo, ChallengeRunRepository $runRepo,
        string $submissionFolder, LoggerInterface $logger)
    {
        $this->security = $security;
        $this->em = $doctrine->getManager();
        $this->chalRepo = $chalRepo;
        $this->runRepo = $runRepo;
        $this->submissionFolder = $submissionFolder;
        $this->logger = $logger;
    }

    public function groupedList(): array
    {
        $grouped = [];
        $grouped['active'] = [];
        $grouped['upcoming'] = [];
        $grouped['previous'] = [];

        //first query all available challenges
        /*$available = $this->chalRepo->findBy([
            'available' => true
        ]);*/

        //query challenge runs that haven't ended yet

        // $available = $this->lookupBy([
        //     'available' => true
        // ], true);

        // $grouped['active'] = $available;

        $grouped['active'] = $this->activeChallenges();
        $grouped['upcoming'] = $this->upcomingChallenges();
        $grouped['previous'] = $this->previousChallenges();

        return $grouped;
    }

    public function leaderboard(Challenge $challenge): array
    {
        $leaderboard = [];

        if ($challenge->isLeaderboard()) {
            $run = $challenge->getActiveRun();

            if (!is_null($run)) {
                $qb = $this->em->createQueryBuilder();

                $order = 'DESC';
                $selectScore = 'MAX(s.score)';

                if ($challenge->isLowerScoreBetter()) {
                    $order = 'ASC';
                    $selectScore = 'MIN(s.score)';
                }

                $qb ->select(['u.username AS username', $selectScore . ' AS score'])
                    ->from(Submission::class,'s')
                    ->leftJoin('s.participant','u')
                    ->where("s.status = 'complete'")
                    ->andWhere('s.run = :run')
                    ->groupBy('s.participant')
                    ->orderBy($selectScore, $order)
                    ->setParameter('run', $run);
                
                $query = $qb->getQuery();

                $leaderboard = $query->getResult();
            }
        }

        return $leaderboard;
    }

    private function activeChallenges(): array
    {
        $qb = $this->em->createQueryBuilder();

        $now = new \DateTime();

        $qb ->select(['c', 'r'])
            ->from(Challenge::class, 'c')
            ->leftJoin('c.runs', 'r')
            ->where(':now BETWEEN r.start AND r.finish')
            ->andWhere('c.available = TRUE')
            ->orderBy('r.finish', 'ASC')
            ->setParameter('now', $now);
        
        $query = $qb->getQuery();
        return $query->getResult();
    }

    private function upcomingChallenges(): array {
        $qb = $this->em->createQueryBuilder();
        $now = new \DateTime();

        $qb ->select(['c','r'])
            ->from(Challenge::class,'c')
            ->leftJoin('c.runs','r')
            ->where(':now < r.start')
            ->andWhere('c.available = TRUE')
            ->orderBy('r.start', 'ASC')
            ->setParameter('now', $now);
        
        $query = $qb->getQuery();
        return $query->getResult();
    }

    private function previousChallenges(): array {
        $qb = $this->em->createQueryBuilder();
        $now = new \DateTime();

        $qb ->select(['c','r'])
            ->from(Challenge::class,'c')
            ->leftJoin('c.runs','r')
            ->where(':now > r.finish')
            ->andWhere('c.available = TRUE')
            ->orderBy('r.finish', 'DESC')
            ->setParameter('now', $now);
        
        $query = $qb->getQuery();
        return $query->getResult();
    }

    public function lookupBy($params, $exact = false) {
        if (empty($params)) {
            return [];
        }

        $f = $exact ? '' : '%';

        $qb = $this->em->createQueryBuilder();

        $qb ->select(['c', 'r'])
            ->from(Challenge::class, 'c')
            ->leftJoin('c.runs', 'r');
        
        foreach($params as $param => $value) {
            if (!empty($value)) {
                switch ($param) {
                    case 'name':
                        $qb->andWhere($qb->expr()->like('c.name', ':name'))
                            ->setParameter('name', $f . $value . $f);
                        break;

                    case 'available':
                        $qb->andWhere('c.available = TRUE');
                        break;
                    
                    default:
                        $qb->andWhere($qb->expr()->like('c.' . $param, ':' . $param))
                            ->setParameter($param, $f . $value . $f);
                        break;
                }
            }
        }

        $query = $qb->getQuery();
        return $query->getResult();
    }

    public function createSubmission(Submission $submission, Challenge $challenge, $file): array
    {
        $user = $this->security->getUser();
        $user->addSubmission($submission);

        $activeRun = $challenge->getActiveRun();

        if (is_null($activeRun)) {
            return [
                'Cannot accept submission for an inactive challenge.'
            ];
        }

        $submission->setStatus('uploaded');
        $submission->setSubmitted(new DateTime());
        $submission->setRun($activeRun);

        $this->em->persist($submission);
        $this->em->persist($user);
        $this->em->flush();

        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        // this is needed to safely include the file name as part of the URL
        $newFilename = $submission->getId() . '.zip';

        $this->logger->info($this->submissionFolder . ' - ' . $newFilename);

        // Move the file to the directory where brochures are stored
        try {
            $file->move(
                $this->submissionFolder,
                $newFilename
            );
        } catch (FileException $e) {
            return ['Unable to relocate upload (' . $e->getCode() . ')'];
        }

        return [];
    }

    public function submissions(?UserInterface $user, ?ChallengeRun $run): array
    {
        if (is_null($user) || is_null($run)) {
            return [];
        }

        $qb = $this->em->createQueryBuilder();

        $qb ->select('s')
            ->from(Submission::class,'s')
            ->where('s.participant = :participant')
            ->andWhere('s.run = :run')
            ->orderBy('s.submitted', 'DESC')
            ->setParameter('participant', $user)
            ->setParameter('run', $run);
        
        $query = $qb->getQuery();
        return $query->getResult();
    }
}