<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

use App\Entity\Challenge;
use App\Entity\ChallengeRun;
use App\Entity\Submission;
use App\Service\ChallengeService;
use App\Form\SubmissionType;

class ChallengesController extends AbstractController
{
    private $em;
    private $cs;

    public function __construct(ManagerRegistry $doctrine,
        ChallengeService $cs)
    {
        $this->em = $doctrine->getManager();
        $this->cs = $cs;
    }

    #[Route('/challenges', name: 'challenges')]
    public function index(): Response
    {
        /** Order for challenges:
         * 1. Active, soonest ending first
         * 2. Upcoming, soonest starting first
         * 3. Remaining, alphabetically
         */

        $challenges = $this->cs->groupedList();


        return $this->render('page/challenges/index.html.twig', [
            'challenges' => $challenges,
        ]);
    }

    #[Route('/challenge/{challenge}', name: 'challenge')]
    public function challenge(Challenge $challenge): Response
    {
        $leaderboard = $this->cs->leaderboard($challenge);
        return $this->render('page/challenges/challenge.html.twig', [
            'challenge' => $challenge,
            'leaderboard' => $leaderboard
        ]);
    }

    #[Route('/submit/{challenge}', name: 'submit')]
    public function submit(Challenge $challenge, Request $req, ChallengeService $cs): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            $this->addFlash('error', 'You must be logged in to submit an entry');
            $this->denyAccessUnlessGranted('ROLE_USER');
        }


        $form = $this->createForm(SubmissionType::class);

        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            $submission = $form->getData();
            $file = $form->get('file')->getData();

            $errs = $cs->createSubmission($submission, $challenge, $file);

            if (empty($errs)) {
                $this->addFlash('success', 'Submission uploaded.');
                return $this->redirectToRoute('challenge', [
                    'challenge' => $challenge->getId()
                ]);
            } else {
                foreach ($errs as $err) {
                    $this->addFlash('error', $err);
                }
            }
        } else {
            return $this->render('page/challenges/submit.html.twig', [
                'challenge' => $challenge,
                'form' => $form,
            ]);
        }
    }
}
