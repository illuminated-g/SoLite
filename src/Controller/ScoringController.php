<?php

/**
 * This controller handles the API for autoscoring tooling running on another
 * system.
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use App\Service\AutoScorerService;
use App\Service\ChallengeService;

class ScoringController extends AbstractController
{
    private $scorers;
    private $challenges;

    function __construct(AutoScorerService $scorers, ChallengeService $challenges) {
        $this->scorers = $scorers;
        $this->challenges = $challenges;
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


        return $this->render('scoring/index.html.twig', [
            'controller_name' => 'ScoringController',
        ]);
    }
}
