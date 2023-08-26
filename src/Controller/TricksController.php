<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Form\TrickType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route(name: 'app_')]
class TricksController extends AbstractController {

    #[Route('/create', name:'create')]
    public function create(Request $request, EntityManagerInterface $manager){

        $trick = new Trick();
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        return $this->render('tricks/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/tricks/{name}', name:'trick_show')]
    public function show($name, EntityManagerInterface $entityManager){

        $trick = $entityManager->getRepository(Trick::class)->findOneBy([
            'name' => $name
        ]);

        $images = [
            "https://oppq.qc.ca/wp-content/uploads/Snowboard-programme-pour-vous-echauffer.jpg",
            "https://img.olympicchannel.com/images/image/private/t_social_share_thumb/f_auto/primary/vqys54onceefbza1qu9p"
        ];

        $videos = [
            "https://www.youtube.com/embed/EzGPmg4fFL8",
        ];

        $medias = array_merge($images, $videos);

        return $this->render('tricks/trick.html.twig', [
            'trick' => $trick,
            'medias' => $medias,
            'name' => $name
        ]);
    }

    #[Route('/update-{name}', name:'trick_update')]
    public function update($name){

        $images = [
            "https://oppq.qc.ca/wp-content/uploads/Snowboard-programme-pour-vous-echauffer.jpg",
            "https://img.olympicchannel.com/images/image/private/t_social_share_thumb/f_auto/primary/vqys54onceefbza1qu9p"
        ];

        $videos = [
            "https://www.youtube.com/embed/EzGPmg4fFL8",
        ];

        $medias = array_merge($images, $videos);

        return $this->render('tricks/update.html.twig', [
            'medias' => $medias,
            'name' => $name
        ]);
    }

}