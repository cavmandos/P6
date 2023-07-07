<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route(name: 'app_')]
class TricksController extends AbstractController {

    #[Route('/create', name:'create')]
    public function create(){
        return $this->render('tricks/create.html.twig');
    }

    #[Route('/tricks/{name}', name:'trick_show')]
    public function show($name){
        return $this->render('tricks/trick.html.twig', [
            'name' => $name
        ]);
    }

    #[Route('/update-{name}', name:'trick_update')]
    public function update($name){
        return $this->render('tricks/update.html.twig', [
            'name' => $name
        ]);
    }

}