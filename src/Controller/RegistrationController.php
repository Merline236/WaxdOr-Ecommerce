<?php

namespace App\Controller;

use App\Entity\Users;
use App\Service\JWTService;
use App\Service\SendMailService;
use App\Form\RegistrationFormType;
use App\Repository\UsersRepository;
use App\Security\UsersAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher,
     UserAuthenticatorInterface $userAuthenticator, UsersAuthenticator $authenticator,
     EntityManagerInterface $entityManager, SendMailService $mail, JWTService $jwt,
     ): Response
    {
        $user = new Users();
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
            // faites tout ce dont vous avez besoin ici, comme envoyer un e-mail

            // on génère le jwt de l'utilisateur
            //on crée le header
            $header = [
                'typ' => 'JWT',
                'alg' => 'HS256',
            ];

            // on crée le payload
            $payload = [
                'user_id' => $user->getId()
            ];

            //on génère le token
            $token = $jwt->generate($header, $payload,
            $this->getParameter('app.jwtsecret'));

            



            //on envoie un mail
            $mail->send(
                'no-reply@monsite.net',
                $user->getEmail(),
                'Activation de votre compte sur le site WaxDor',
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

    #[Route('/verif/{token}', name: 'verify_user')]
    public function verifyUser($token, JWTService $jwt, UsersRepository $usersRepository, EntityManagerInterface $em): Response
    {
    //dd($jwt->check($token, $this->getparameter('app.jwtsecret')));

    //on vérifie si le token est valide, n'a pas expiré et n'a pas été mofidié
    if($jwt->isValid($token) && !$jwt->isExpired($token) && 
    $jwt->check($token, $this->getParameter('app.jwtsecret'))){
        //on récupére le payload
        $payload = $jwt->getPayload($token);

        //on récupere le user du token
        $user = $usersRepository->find($payload['user_id']);

        //on véréfie que l'utilisateur n'a pas encore activé son son compte
        if($user && !$user->getIsVerified()){
            $user->setIsVerified(true);
            $em->flush($user);
            $this->addFlash('success', 'Utilisateur activé');
            return $this->redirectToroute('profile_index');
        }
    }
    //Ici un probléme se pose dans le token
    $this->addFlash('danger', 'Le token est invalide ou a expiré');
    return $this->redirectToroute('app_login');
    }
    #[Route('renvoiverif', name: 'resend_verif')]
    public function resendVerif(JWTService $jwt, SendMailService $mail, UsersRepository $usersRepository): Response
    {
       $user =$this->getUser();
       
       if(!$user){
        $this->addFlash('danger', 'Vous devez vous connecter pour accéder à cette page');
            return $this->redirectToroute('app_login');
        }

        if($user->getIsVerified()){
            $this->addFlash('warning', 'Ce compte utilisateur est déjà activé');
            return $this->redirectToroute('profile_index');
        }

        // on génère le jwt de l'utilisateur
            //on crée le header
        $header = [
            'typ' => 'JWT',
            'alg' => 'HS256',
        ];

        // on crée le payload
        $payload = [
            'user_id' => $user->getId()
        ];

        //on génère le token
        $token = $jwt->generate($header, $payload,
        $this->getParameter('app.jwtsecret'));


        //on envoie un mail
        $mail->send(
            'no-reply@monsite.net',
            $user->getEmail(),
            'Activation de votre compte sur le site WaxDor',
            'register',
            compact('user', 'token')
        );

            $this->addFlash('success', 'Email de vérification envoyé');
                return $this->redirectToroute('profile_index');
    }
}