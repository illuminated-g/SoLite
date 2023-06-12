<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

use App\Entity\Challenge;
use App\Entity\ChallengeRun;
use App\Service\ChallengeService;

class ChallengesController extends AbstractController
{
    private $em;

    public function __construct(ManagerRegistry $doctrine) {
        $this->em = $doctrine->getManager();
    }

    #[Route('/challenges', name: 'challenges')]
    public function index(ChallengeService $cs): Response
    {
        /** Order for challenges:
         * 1. Active, soonest ending first
         * 2. Upcoming, soonest starting first
         * 3. Remaining, alphabetically
         */

        $challenges = $cs->groupedList();


        return $this->render('page/challenges/index.html.twig', [
            'challenges' => $challenges,
        ]);
    }

    #[Route('/challenge/{challenge}', name: 'challenge')]
    public function challenge(Challenge $challenge): Response
    {
        return $this->render('page/challenges/challenge.html.twig', [
            'challenge' => $challenge
        ]);
    }
}
