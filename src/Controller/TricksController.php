<?php

namespace App\Controller;

use App\Entity\Media;
use App\Entity\Trick;
use App\Form\TrickType;
use App\Repository\MediaRepository;
use App\Repository\TrickRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use PhpParser\Node\Stmt\TryCatch;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[Route(name: 'app_')]
class TricksController extends AbstractController {

    #[Route('/create', name:'create')]
    public function create(Request $request, EntityManagerInterface $manager,){

        $trick = new Trick();
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        try {
            if ($form->isSubmitted() && $form->isValid()) {
                $trick = $this->prepareTrick($trick);
                $mediaUrl = $form->get('medias')->getData();
                $images = $form->get('images')->getData();
                
                if (!empty($mediaUrl)) {
                    $media = $this->createMedia($mediaUrl, $trick);
                    $manager->persist($media);
                }

                if (!empty($images)) {
                    $this->createImage($images, $trick, $manager);
                }
    
                $manager->persist($trick);
                $manager->flush();
                $this->addFlash('success', "Félicitations, vous avez créé le trick : " . $trick->getName());
                return $this->redirectToRoute('app_main');
            }
        } catch (Exception $e) {
            $this->addFlash('error', 'Oups, il semble y avoir un soucis');
            $this->redirectToRoute('app_create');
        }

        return $this->render('tricks/create.html.twig', [
            'form' => $form->createView()
        ]);

    }

    #[Route('/tricks/{name}', name:'trick_show')]
    public function show($name, EntityManagerInterface $entityManager){

        $trick = $entityManager->getRepository(Trick::class)->findOneBy(['name' => $name]);
        $medias = $this->getMediasForTrick($trick, $entityManager);
        $trickBanner = $this->getTrickBanner($trick, $entityManager);

        return $this->render('tricks/trick.html.twig', [
            'trick' => $trick,
            'medias' => $medias,
            'name' => $name,
            'trickbanner' => $trickBanner
        ]);
    }

    #[Route('/update-{name}', name:'trick_update')]
    public function update($name, EntityManagerInterface $entityManager, Request $request, ){

        $trick = $entityManager->getRepository(Trick::class)->findOneBy(['name' => $name]);
        $medias = $this->getMediasForTrick($trick, $entityManager);
        $trickBanner = $this->getTrickBanner($trick, $entityManager);
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        try {
            if ($form->isSubmitted()) {

                $mediaUrl = $form->get('medias')->getData();
                $images = $form->get('images')->getData();

                if (!empty($mediaUrl)) {
                    $media = $this->createMedia($mediaUrl, $trick);
                    $entityManager->persist($media);
                }

                if (!empty($images)) {
                    $this->addImage($images, $trick, $entityManager);
                }

                $trick = $this->newLastUpdate($trick);
                $entityManager->persist($trick);
                $entityManager->flush();
                $this->addFlash('success', "Félicitations, vous avez modifié le trick : " . $trick->getName());
                return $this->redirectToRoute('app_trick_show', ['name' => $trick->getName()]);
            }
        } catch (Exception $e) {
            $this->addFlash('error', 'Oups, il semble y avoir un soucis');
            $this->redirectToRoute('app_trick_update', ['name' => $trick->getName()]);
        }

        return $this->render('tricks/update.html.twig', [
            'trick' => $trick,
            'medias' => $medias,
            'name' => $name,
            'trickbanner' => $trickBanner,
            'form' => $form->createView()
        ]);
    }

    #[Route('/update/banner/{id}', name:'trick_banner')]
    public function updateBanner($id, EntityManagerInterface $entityManager){

        $newBanner = $entityManager->getRepository(Media::class)->findOneBy(['id' => $id]);
        $newBanner->setBanner(true);
        $trick = $entityManager->getRepository(Trick::class)->findOneBy(['id' => $newBanner->getTrickId()]);
        $otherBanners = $entityManager->getRepository(Media::class)->findBy(['trickId' => $trick->getId()]);
        
        foreach ($otherBanners as $otherBanner) {
            if ($otherBanner !== $newBanner) {
                $otherBanner->setBanner(false);
            }
        };
        
        $entityManager->flush();
        $this->addFlash('success', "Héhé, ça c'est un bon choix de bannière :-) ");
        return $this->redirectToRoute('app_trick_update', ['name' => $trick->getName()]);
    }

    #[Route('/tricks/delete/{id}', name: 'trick_delete')]
    public function deleteTrick($id, EntityManagerInterface $entityManager, TrickRepository $trickRepository)
    {
        $trick = $entityManager->getRepository(Trick::class)->findOneBy(['id' => $id]);
        $medias = $entityManager->getRepository(Media::class)->findBy(['trickId' => $id]);

        foreach ($medias as $media) {
            $fileName = $media->getUrl();
            $uploadDirectory = $this->getParameter('uploads');
            $fileToDelete = $uploadDirectory . '/' . $fileName;
            if (file_exists($fileToDelete)) {
                unlink($fileToDelete);
            }
        };

        $trickRepository->remove($trick, true);
        $this->addFlash('success', "So long ! Vous avez supprimé le trick : ".$trick->getName());
        return $this->redirectToRoute('app_main');
    }

    #[Route('/media/delete/{id}', name: 'media_delete')]
    public function deleteMedia($id, EntityManagerInterface $entityManager, MediaRepository $mediaRepository)
    {
        $media = $entityManager->getRepository(Media::class)->findOneBy(['id' => $id]);
        $trick = $entityManager->getRepository(Trick::class)->findOneBy(['id' => $media->getTrickId()]);
        $mediaRepository->remove($media, true);
        $fileName = $media->getUrl();
        $uploadDirectory = $this->getParameter('uploads');
        $fileToDelete = $uploadDirectory . '/' . $fileName;

        if (file_exists($fileToDelete)) {
            unlink($fileToDelete);
        }

        $this->addFlash('success', "Le media a bien été supprimé");
        return $this->redirectToRoute('app_trick_update', ['name' => $trick->getName()]);
    }

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    private function getMediasForTrick(Trick $trick, EntityManagerInterface $entityManager): array
    {
        $images = $entityManager->getRepository(Media::class)->findBy(['trickId' => $trick->getId()]);
        $medias = [];

        foreach ($images as $image) {
            $medias[] = $this->formatMedia($image);
        }

        return $medias;
    }

    private function getTrickBanner(Trick $trick, EntityManagerInterface $entityManager): array
    {
        $media = $entityManager->getRepository(Media::class)->findBy(['trickId' => $trick, 'banner' => true]);
        $trickBanner[$trick->getId()] = $media;

        return $trickBanner;
    }

    private function formatMedia(Media $media): array
    {
        return [
            'source' => $media->getUrl(),
            'type' => $media->getType(),
            'banner' => $media->isBanner(),
            'id' => $media->getId()
        ];
    }

    private function prepareTrick(Trick $trick)
    {
        $now = new \DateTime();
        $trick->setPublished($now);
        $trick->setLastUpdate($now);
        $user = $this->security->getUser();
        $trick->setUserId($user);

        return $trick;
    }

    private function newLastUpdate(Trick $trick)
    {
        $now = new \DateTime();
        $trick->setLastUpdate($now);
        $user = $this->security->getUser();
        $trick->setUserId($user);

        return $trick;
    }

    private function createMedia($mediaUrl, Trick $trick)
    {
        $media = new Media();
        $media->setTrickId($trick);

        $fileExtension = pathinfo($mediaUrl, PATHINFO_EXTENSION);
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (in_array(strtolower($fileExtension), $imageExtensions)) {
            $media->setType('image');
            $media->setBanner(true);
            $media->setUrl(strip_tags($mediaUrl));
        } else {
            $media->setType('video');
            $media->setBanner(false);
            $cleanedIframeCode = strip_tags($mediaUrl, '<iframe><div><style><p>');
            $media->setUrl($cleanedIframeCode);
        }

        return $media;
    }

    private function createImage($images, Trick $trick, EntityManagerInterface $manager)
    {
        foreach ($images as $uploadedImage) {
            $imageSize = $uploadedImage->getSize();

            if ($imageSize <= 1000000) {
                $fileName = md5(uniqid()) . '.' . $uploadedImage->guessExtension();
                $uploadedImage->move($this->getParameter('uploads'), $fileName);
                $media = $this->createMedia($fileName, $trick);
                $media->setBanner(true);
                $manager->persist($media);
            } else {
                $this->addFlash('error', 'Le fichier fourni est trop volumineux (maxi 1mo)');
                return $this->redirectToRoute('app_create');
            }
        }
    }

    private function addImage($images, Trick $trick, EntityManagerInterface $manager)
    {
        foreach ($images as $uploadedImage) {
            $imageSize = $uploadedImage->getSize();

            if ($imageSize <= 1000000) {
                $fileName = md5(uniqid()) . '.' . $uploadedImage->guessExtension();
                $uploadedImage->move($this->getParameter('uploads'), $fileName);
                $media = $this->createMedia($fileName, $trick);
                $media->setBanner(false);
                $manager->persist($media);
            } else {
                $this->addFlash('error', 'Le fichier fourni est trop volumineux (maxi 1mo)');
                return $this->redirectToRoute('app_create');
            }
        }
    }

}