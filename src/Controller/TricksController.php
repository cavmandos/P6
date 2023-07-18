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

        $images = [
            "https://oppq.qc.ca/wp-content/uploads/Snowboard-programme-pour-vous-echauffer.jpg",
            "https://img.olympicchannel.com/images/image/private/t_social_share_thumb/f_auto/primary/vqys54onceefbza1qu9p"
        ];

        $videos = [
            "https://www.youtube.com/embed/EzGPmg4fFL8",
        ];

        $medias = array_merge($images, $videos);

        return $this->render('tricks/trick.html.twig', [
            'medias' => $medias,
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