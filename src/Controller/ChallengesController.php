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

    #[Route('/submit/{challenge}', name: 'submit')]
    public function submit(Challenge $challenge, Request $req, ChallengeService $cs): Response
    {
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
