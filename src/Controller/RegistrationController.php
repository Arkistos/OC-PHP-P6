<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\SnowTricksAuthenticator;
use App\Service\JWTService;
use App\Service\PictureService;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        UserAuthenticatorInterface $userAuthenticator,
        SnowTricksAuthenticator $authenticator,
        EntityManagerInterface $entityManager,
        SendMailService $mail,
        JWTService $jwt,
        PictureService $pictureService
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            //Enregistrement de la photo de profil
            $pictureService->add($form->get('profile_pic')->getData(), $user->getUsername(), '/profile_pics', 300, 300);


            $header = [
                'typ' => 'JWT',
                'alg' => 'HS256'
            ];

            $payload = [
                'user_id' => $user->getId()
            ];

            $token = $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));


            $mail->send(
                'no-reply@snowtricks.fr',
                $user->getEmail(),
                'Activation de votre compte SnowTricks',
                'register',
                compact('user', 'token')
            );

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }


    #[Route('/checkToken/{token}', name: 'check_user')]
    public function checkUser(string $token, JWTService $jwt, UserRepository $userRepository, EntityManagerInterface $em): Response
    {
        if($jwt->isValid($token) && !$jwt->isExpired($token) && $jwt->check($token, $this->getParameter('app.jwtsecret'))) {
            $payload = $jwt->getPayload($token);

            $user = $userRepository->find($payload['user_id']);

            if($user && !$user->isIsActivated()) {
                $user->setIsActivated(true);
                $em->flush($user);
                return $this->redirectToRoute('app_homepage');
            }
            return $this->redirectToRoute('app_login');
        }

        /***** Message annonçant le token invalide *****/
    }

    #[Route('/resendcheck', name:'resend_check')]
    public function resendCheck(JWTService $jwt, SendMailService $mailer, UserRepository $userRepository): Response
    {
        $user = $this->getUser();
        if(!$user) {
            return $this->redirectToRoute('app_login');
        }

        if($user->isIsActivated()) {
            return $this->redirectToRoute('app_homepage');
        }

        $header = [
            'typ' => 'JWT',
            'alg' => 'HS256'
        ];

        $payload = [
            'user_id' => $user->getId()
        ];

        $token = $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));


        $mailer->send(
            'no-reply@snowtricks.fr',
            $user->getEmail(),
            'Activation de votre compte SnowTricks',
            'register',
            compact('user', 'token')
        );
        return $this->redirectToRoute('app_homepage');
    }
}
