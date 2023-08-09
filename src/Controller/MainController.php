<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route(name: 'app_')]
class MainController extends AbstractController {

    #[Route('/', name:'main')]
    public function index(){
        return $this->render('main.html.twig', [
            'test' => 'Un texte de démo'
        ]);
    }

    #[Route('/signup', name:'signup')]
    public function signUp(Request $request, UserPasswordHasherInterface $passwordHasher, ValidatorInterface $validator, EntityManagerInterface $entityManager) {
        
        $user = $this->createUser($request, $passwordHasher, $validator, $entityManager);

        if ($user !== null) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('user/signup.html.twig', [
            'form' => $this->createForm(UserType::class, $user)->createView()
        ]);
    }

    #[Route('/forgot', name:'forgot')]
    public function forgot(Request $request){

        if ($request->isMethod('POST')) {
            dd("Envoi email :-)");
        }

        return $this->render('user/forgot.html.twig');
    }

    #[Route('/new-password', name:'new-password')]
    public function newPassword(Request $request){

        if ($request->isMethod('POST')) {
            dd("Mot de passe changé :-)");
        }
        return $this->render('user/new-password.html.twig');
    }

    private function checkExistingUser(User $user, EntityManagerInterface $entityManager): bool {
        $existingUser = $entityManager->getRepository(User::class)->findOneBy(['username' => $user->getUsername()]);
        if ($existingUser) {
            $this->addFlash('error', 'Un utilisateur avec le même nom existe déjà.');
            return true;
        }

        $existingUserByEmail = $entityManager->getRepository(User::class)->findOneBy(['email' => $user->getEmail()]);
        if ($existingUserByEmail) {
            $this->addFlash('error', 'Un utilisateur avec le même email existe déjà.');
            return true;
        }

        return false;
    }

        private function createUser(Request $request, UserPasswordHasherInterface $passwordHasher, ValidatorInterface $validator, EntityManagerInterface $entityManager): ?User {
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
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'Félicitation Rider ! Vous allez recevoir un email pour valider votre inscription.');
            return $user;
        }
        
    }
    

}