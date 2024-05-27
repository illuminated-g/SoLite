<?php

namespace App\Controller;

use \DateTime;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Persistence\ManagerRegistry;

use App\Form\RegistrationType;
use App\Form\ChangePassType;

use App\Entity\User;

use App\Service\Mailer;
use App\Service\SpamService;

class SecurityController extends AbstractController
{
    #[Route('/register', name: 'register')]
    public function register(Request $req, UserPasswordHasherInterface $hasher,
        Mailer $mailer, ManagerRegistry $doctrine,
        SpamService $spamService): Response
    {
        $user = new User();
        
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            $password = bin2hex(random_bytes(16));
            $user->setPassword($hasher->hashPassword($user, $password));

            $user->setRegisteredOn(new DateTime());
            $user->setRegistrationIp($req->getClientIp());

            if ($spamService->checkIsSpamUser($user)) {
                $this->addFlash('error', "Registration cannot be completed due to illegal values in input fields. Ensure special characters are not being used in username or ");
                return $this->redirectToRoute("register");
            }

            $mail = $mailer->createMailer();

            $from = $_ENV['MAIL_EMAIL'];
            $site = $req->getSchemeAndHttpHost();

            $mail->setFrom($from);
            $mail->addAddress($user->getEmail(), $user->getFullName());
            $mail->Subject = $req->getHost() . ' User Registration';

            $msg = $this->renderView('email/registration.html.twig', [
                'newuser' => $user,
                'newpass' => $password,
                'site' => $site
            ]);

            $mail->msgHTML($msg);

            if (!$mail->send()) {
                $error = 'There was an error sending your user registration email: ' . $mail->ErrorInfo . '<br>';
                $error .= 'Email us at <a href="mailto:' . $from . '">' . $from . '</a> if you need assistance.';

                return $this->render('user/register.html.twig', [
                    'form' => $form->createView(),
                    'error' => $error
                ]);
            }

            $em = $doctrine->getManager();
            try {
                $em->persist($user);
            
                $em->flush();
    
                $this->addFlash('success', 'Registration successful. Check your email for your one-time use password to log in.');
    
                return $this->redirectToRoute("home");
            } catch (\Exception $e) {
                $this->addFlash('error', 'Username and email must be unique: ' . $e->getMessage());
            }
        }

        return $this->render('user/register.html.twig', [
            'form' => $form->createView(),
            'error' => null,
        ]);
    }

    #[Route('/changepass', name: 'change_pass')]
    public function changePass(Request $req, UserPasswordHasherInterface $hasher, Mailer $mailer, ManagerRegistry $doctrine): Response {
        $form = $this->createForm(ChangePassType::class);

        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $user->setPassword($hasher->hashPassword($user, $form->getData()->getPassword()));

            $user->setChangePass(false);

            $entityManager = $doctrine->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Password changed.');

            $mail = $mailer->createMailer();

            $mail->setFrom($_ENV['MAIL_EMAIL']);
            $mail->addAddress($user->getEmail(), $user->getFullName());
            $mail->Subject = $req->getHost() . ' User Password Changed';

            $msg = $this->renderView('email/changepass.html.twig');

            $mail->msgHTML($msg);

            try {
                $mail->send();
            } catch (\Exception $e) {
                $this->addFlash('warning', 'Unable to send notification email.');
            }

            return $this->redirectToRoute('home');
        }
        
        return $this->render('security/changepass.html.twig', [
            'form' => $form->createView(),
            'error' => null,
        ]);
    }

    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
