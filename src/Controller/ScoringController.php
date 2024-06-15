<?php

/**
 * This controller handles the API for autoscoring tooling running on another
 * system.
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

use App\Service\AutoScorerService;
use App\Service\ChallengeService;

use App\Entity\AutoScorer;
use App\Entity\Submission;

class ScoringController extends AbstractController
{
    private $scorers;
    private $challenges;
    private EntityManagerInterface $em;

    private string $submissionFolder;

    function __construct(AutoScorerService $scorers, ChallengeService $challenges,
        ManagerRegistry $doctrine, string $submissionFolder)
    {
        $this->scorers = $scorers;
        $this->challenges = $challenges;
        $this->em = $doctrine->getManager();
        $this->submissionFolder = $submissionFolder;
    }

    #[Route('/scoring/next', name: 'scoring_next')]
    public function scoring_next(Request $request): Response
    {
        /**
         * Expected schema:
         * 
         *  {
         *      scorer: "<scorer name>",
         *      key: "<api_key>",
         *      challenges: [
         *          <challenge_ids of supported challenges>
         *      ]
         *  }
         */

        $scorerInfo = json_decode($request->getContent(), true);
        $scorer = $this->validateScorer($scorerInfo);

        if (is_null($scorer)) {
            return new JsonResponse([
                'code' => 401,
                'message' => 'Invalid credentials'
            ], 401);
        }

        if (empty($scorerInfo['challenges'])) {
            return new JsonResponse([
                'code' => 400,
                'message' => 'Missing request field(s)'
            ]);
        }

        $ids = implode(',', $scorerInfo['challenges']);

        $qb = $this->em->createQueryBuilder();
        $qb ->select('s', 'r')
            ->from(Submission::class, 's')
            ->leftJoin('s.run', 'r')
            ->where('IDENTITY(r.challenge) IN (:ids)')
            ->andWhere('s.status = :status')
            ->orderBy('s.submitted', 'ASC')
            ->setParameter('ids', $ids)
            ->setParameter('status', 'uploaded');

        $query = $qb->getQuery();
        $submissions = $query->getResult();

        if (empty($submissions)) {
            return new JsonResponse([
                'code' => 406,
                'message' => 'No submissions available',
                'submission' => -1
            ]);
        }

        $submission = $submissions[0];

        $submission->setStatus('pending');
        $submission->setScorer($scorer);
        $submission->setScoringStarted(new \DateTime());
        $this->em->persist($submission);
        $this->em->flush();

        return new JsonResponse([
            'code' => 200,
            'submission' => $submission->getId()
        ]);
    }

    #[Route('/scoring/submission/{id}', name: 'scoring_submission')]
    public function scoring_submission(Request $request, Submission $submission): Response
    {
        $scorerInfo = json_decode($request->getContent(), true);
        $scorer = $this->validateScorer($scorerInfo);

        if (is_null($scorer)) {
            return new JsonResponse([
                'code' => 401,
                'message' => 'Invalid credentials'
            ], 401);
        }

        $zipName = $submission->getId() . '.zip';
        
        return new BinaryFileResponse($this->submissionFolder . '/' . $zipName, 200);
    }

    #[Route('/scoring/result/{id}', name: 'scoring_result')]
    public function scoring_result(Request $request, Submission $submission): Response
    {
        $info = json_decode($request->getContent(), true);
        $scorer = $this->validateScorer($info);

        if (is_null($scorer)) {
            return new JsonResponse([
                'code' => 401,
                'message' => 'Invalid credentials'
            ], 401);
        }

        $submission->setStatus($info['status']);
        $submission->setScore($info['score']);
        $submission->setScored(true);

        $this->em->persist($submission);
        $this->em->flush();

        return new JsonResponse([
            'code' => 200,
            'message' => 'Score updated'
        ]);
    }

    private function validateScorer($scorerInfo)
    {
        if (empty($scorerInfo) || empty($scorerInfo['scorer']) || empty($scorerInfo['key'])) {
            return null;
        }

        $qb = $this->em->createQueryBuilder();

        $qb ->select('s')
            ->from(AutoScorer::class, 's')
            ->where('s.name = :name')
            ->andWhere('s.api_key = :key')
            ->setParameter('name', $scorerInfo['scorer'])
            ->setParameter('key', $scorerInfo['key']);
        
        $query = $qb->getQuery();
        return $query->getOneOrNullResult();
    }
}
