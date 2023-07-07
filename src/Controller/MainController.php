<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route(name: 'app_')]
class MainController extends AbstractController {

    #[Route('/', name:'main')]
    public function index(){
        return $this->render('main.html.twig');
    }

    #[Route('/signin', name:'signin')]
    public function signIn(){
        return $this->render('user/signin.html.twig');
    }

    #[Route('/signup', name:'signup')]
    public function signUp(){
        return $this->render('user/signup.html.twig');
    }

    #[Route('/forgot', name:'forgot')]
    public function forgot(){
        return $this->render('user/forgot.html.twig');
    }

    #[Route('/new-password', name:'new-password')]
    public function newPassword(){
        return $this->render('user/new-password.html.twig');
    }

}