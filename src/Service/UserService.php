<?php

namespace App\Service;

use App\Constants\FlashMessages;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class UserService
{
    public function checkExistingUser(User $user, $entityManager)
    {
        $existingUser = $entityManager->getRepository(User::class)->findOneBy(['username' => $user->getUsername()]);

        if ($existingUser) {
            return ['error' => FlashMessages::ALREADY_NAME, 'isError' => true];
        }

        $existingUserByEmail = $entityManager->getRepository(User::class)->findOneBy(['email' => $user->getEmail()]);
        if ($existingUserByEmail) {
            return ['error' => FlashMessages::ALREADY_EMAIL, 'isError' => true];
        }

        return ['isError' => false];
    }

    public function createUser($user, UserPasswordHasherInterface $passwordHasher, ValidatorInterface $validator, EntityManagerInterface $entityManager, MailerService $mailerService, TokenGeneratorInterface $tokenGeneratorInterface) 
    {
        $errors = $validator->validate($user);
        $check = $this->checkExistingUser($user, $entityManager);

        if ($check['isError']) {
            return ['error' => $check['error']];
        }
        
        if ($errors->count() > 0) {
            $errorMessages = [];

            foreach ($errors as $violation) {
                $errorMessages[] = $violation->getMessage();
            }

            return ['errors' => $errorMessages];

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

            return ['success' => FlashMessages::SIGNUP_OK, 'user' => $user];
        }
    }

    public function changePassword($userRepository, $entityManager, $tokenGenerator, $mailerService, $email)
    {
        $user = $userRepository->findOneBy(['email' => $email]);

        if ($user) {
            $token = $tokenGenerator->generateToken();
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
            return ['success' => FlashMessages::RECOVERY_OK, 'isError' => false];
        } else {
            return ['error' => FlashMessages::RECOVERY_KO, 'isError' => true];
        }
    }

}
