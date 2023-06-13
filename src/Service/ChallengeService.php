<?php

namespace App\Service;

use \DateTime;

use Symfony\Bundle\SecurityBundle\Security;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Challenge;
use App\Entity\ChallengeRun;
use App\Entity\Submission;

use App\Repository\ChallengeRepository;
use App\Repository\ChallengeRunRepository;

use Carbon\Carbon;

class ChallengeService {

    private Security $security;
    private EntityManagerInterface $em;
    private ChallengeRepository $chalRepo;
    private ChallengeRunRepository $runRepo;

    private string $submissionFolder;

    public function __construct(Security $security, ManagerRegistry $doctrine, ChallengeRepository $chalRepo, ChallengeRunRepository $runRepo, string $submissionFolder)
    {
        $this->security = $security;
        $this->em = $doctrine->getManager();
        $this->chalRepo = $chalRepo;
        $this->runRepo = $runRepo;
        $this->submissionFolder = $submissionFolder;
    }

    public function groupedList(): array
    {
        $grouped = [];
        $grouped['active'] = [];
        $grouped['next'] = [];
        $grouped['inactive'] = [];

        //first query all available challenges
        /*$available = $this->chalRepo->findBy([
            'available' => true
        ]);*/

        //query challenge runs that haven't ended yet

        $available = $this->lookupBy([
            'available' => true
        ], true);

        $grouped['active'] = $available;

        return $grouped;
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

        $submission->setStatus('uploaded');
        $submission->setSubmitted(new DateTime());

        $this->em->persist($submission);
        $this->em->persist($user);
        $this->em->flush();

        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        // this is needed to safely include the file name as part of the URL
        $newFilename = $challenge->getId() . '-' . $submission->getId() . '.zip';

        // Move the file to the directory where brochures are stored
        try {
            $file->move(
                $this->submissionFolder,
                $newFilename
            );
        } catch (FileException $e) {
            return [$e.getMessageKey()];
        }

        return [];
    }
}