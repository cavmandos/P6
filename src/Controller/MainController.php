<?php

namespace App\Controller;

use App\Entity\Media;
use App\Entity\Trick;
use App\Entity\User;
use App\Form\ForgotType;
use App\Form\NewPasswordType;
use App\Form\UserType;
use App\Service\MailerService;
use App\Constants\FlashMessages;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use App\Repository\UserRepository;

#[Route(name: 'app_')]
class MainController extends AbstractController {

    #[Route('/', name:'main')]
    public function index(EntityManagerInterface $entityManager){

        $tricks = $entityManager->getRepository(Trick::class)->findAll();

        $trickMedias = [];
        foreach ($tricks as $trick) {
            $media = $entityManager->getRepository(Media::class)->findBy(['trickId' => $trick, 'banner' => true]);
            $trickMedias[$trick->getId()] = $media;
        }

        return $this->render('main.html.twig', [
            'tricks' => $tricks,
            'medias' => $trickMedias
        ]);
    }

    #[Route('/signup', name:'signup')]
    public function signUp(Request $request, UserPasswordHasherInterface $passwordHasher, ValidatorInterface $validator, EntityManagerInterface $entityManager, MailerService $mailerService, TokenGeneratorInterface $tokenGeneratorInterface) {
        
        $user = $this->createUser($request, $passwordHasher, $validator, $entityManager, $mailerService, $tokenGeneratorInterface);

        if ($user !== null) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('user/signup.html.twig', [
            'form' => $this->createForm(UserType::class, $user)->createView()
        ]);
    }

    #[Route('/forgot', name:'forgot')]
    public function forgot(Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager, TokenGeneratorInterface $tokenGeneratorInterface, MailerService $mailerService)
    {
        $form = $this->createForm(ForgotType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $this->changePassword($userRepository, $entityManager, $tokenGeneratorInterface, $mailerService, $email);
        }

        return $this->render('user/forgot.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/password/{token}/{id<\d+>}', name:'password', methods: ['GET', 'POST'])]
    public function newPassword(string $token, int $id, UserRepository $userRepository, Request $request, UserPasswordHasherInterface $passwordHasher)
    {
        
        $form = $this->createForm(NewPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $userRepository->find($id);
            if (!$user) {
                throw new AccessDeniedException();
            }
    
            $password = $form->get('password')->getData();
            $userRepository->changePassword($token, $user, $password, $passwordHasher);
            $this->addFlash('success', FlashMessages::PASSWORD_OK);
            return $this->redirectToRoute('app_main');
        }

        return $this->render('user/new-password.html.twig', [
             'form' => $form->createView()
        ]);
    }

    #[Route('/verify/{token}/{id<\d+>}', name: 'verify', methods: ['GET'])]
    public function verify(string $token, int $id, UserRepository $userRepository): Response
    {
        $user = $userRepository->find($id);

        if (!$user) {
            throw new AccessDeniedException();
        }

        $userRepository->verifyUser($token, $user);

        $this->addFlash('success', FlashMessages::ACTIVATION_OK);
        return $this->redirectToRoute('app_login');
    }

    private function checkExistingUser(User $user, EntityManagerInterface $entityManager): bool 
    {
        $existingUser = $entityManager->getRepository(User::class)->findOneBy(['username' => $user->getUsername()]);
        if ($existingUser) {
            $this->addFlash('error', FlashMessages::ALREADY_NAME);
            return true;
        }

        $existingUserByEmail = $entityManager->getRepository(User::class)->findOneBy(['email' => $user->getEmail()]);
        if ($existingUserByEmail) {
            $this->addFlash('error', FlashMessages::ALREADY_EMAIL);
            return true;
        }

        return false;
    }

    private function createUser(Request $request, UserPasswordHasherInterface $passwordHasher, ValidatorInterface $validator, EntityManagerInterface $entityManager, MailerService $mailerService, TokenGeneratorInterface $tokenGeneratorInterface): ?User 
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        
        if (!$form->isSubmitted()) {
            return null;
        }
        
        $errors = $validator->validate($user);
        if ($this->checkExistingUser($user, $entityManager)) {
                return null;
        }
        
        if ($errors->count() > 0) {
            foreach ($errors as $violation) {
                $this->addFlash('error', $violation->getMessage());
            }
            return null;
        } else {
            $user->setPassword($passwordHasher->hashPassword($user, $user->getPassword()));

            $token = $tokenGeneratorInterface->generateToken();
            $user->setToken($token);

            $entityManager->persist($user);
            $entityManager->flush();

            $mailerService->send(
                $user->getEmail(),
                'Confirmation du compte',
                'registration.html.twig',
                [
                    'user' => $user,
                    'token' => $token
                ]
            );

            $this->addFlash('success', FlashMessages::SIGNUP_OK);
            return $user;
        }
        
    }

    private function changePassword($userRepository, $entityManager, $tokenGeneratorInterface, $mailerService, $email)
    {
        $user = $userRepository->findOneBy(['email' => $email]);

        if ($user) {
            $token = $tokenGeneratorInterface->generateToken();
            $user->setToken($token);
            $entityManager->persist($user);
            $entityManager->flush();
    
            $mailerService->send(
                $user->getEmail(),
                'Réinitialisation du mot de passe',
                'forgot.html.twig',
                [
                    'user' => $user,
                    'token' => $token
                ]
            );
            $this->addFlash('success', FlashMessages::RECOVERY_OK);
            return $this->redirectToRoute('app_forgot');
                
        } else {
            $this->addFlash('error', FlashMessages::RECOVERY_KO);
            return $this->redirectToRoute('app_forgot');
        }
    }
    

}