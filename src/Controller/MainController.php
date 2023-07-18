<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route(name: 'app_')]
class MainController extends AbstractController {

    #[Route('/', name:'main')]
    public function index(){
        return $this->render('main.html.twig', [
            'test' => 'Un texte de démo'
        ]);
    }

    #[Route('/signup', name:'signup')]
    public function signUp(Request $request, EntityManagerInterface $manager){

        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        return $this->render('user/signup.html.twig', [
            'form' => $form->createView()
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

}