<?php

namespace App\Controller;

use App\Entity\Discussion;
use App\Entity\Media;
use App\Entity\Trick;
use App\Form\DiscussionType;
use App\Form\TrickType;
use App\Constants\FlashMessages;
use App\Repository\DiscussionRepository;
use App\Repository\MediaRepository;
use App\Repository\TrickRepository;
use App\Service\SlugifyService;
use App\Service\TrickService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route(name: 'app_')]
class TricksController extends AbstractController {

    #[Route('/create', name:'create')]
    public function create(Request $request, EntityManagerInterface $manager, SlugifyService $slugifyService, TrickService $trickService){

        $trick = new Trick();
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        try {
            if ($form->isSubmitted() && $form->isValid()) {
                $trick = $trickService->prepareTrick($trick, $slugifyService);
                $mediaUrl = $form->get('medias')->getData();
                $images = $form->get('images')->getData();
                
                if (!empty($mediaUrl)) {
                    $media = $trickService->createMedia($mediaUrl, $trick);
                    $manager->persist($media);
                }

                if (!empty($images)) {
                    $uploadsDirectory = $this->getParameter('uploads');
                    $addImage = $trickService->createImage($images, $trick, $manager, $uploadsDirectory);

                    if (isset($addImage['error'])) {
                        $this->addFlash('error', $addImage['error']);
                        return $this->redirectToRoute('app_create');
                    }
                }
    
                $manager->persist($trick);
                $manager->flush();
                $this->addFlash('success', FlashMessages::TRICK_OK . $trick->getName());
                return $this->redirectToRoute('app_main');
            }
        } catch (Exception $e) {
            $this->addFlash('error', FlashMessages::PROBLEM);
            $this->redirectToRoute('app_create');
        }

        return $this->render('tricks/create.html.twig', [
            'form' => $form->createView()
        ]);

    }

    #[Route('/tricks/{slug}', name:'trick_show')]
    public function show($slug, EntityManagerInterface $entityManager, Request $request, TrickService $trickService){

        $trick = $entityManager->getRepository(Trick::class)->findOneBy(['slug' => $slug]);
        $medias = $trickService->getMediasForTrick($trick, $entityManager);
        $trickBanner = $trickService->getTrickBanner($trick, $entityManager);
        $paginator = $trickService->paginate($entityManager, $trick, $request);

        $discussion = new Discussion();
        $form = $this->createForm(DiscussionType::class, $discussion);
        $form->handleRequest($request);

        try {
            if ($form->isSubmitted() && $form->isValid()) {
                $discussion = $trickService->prepareComment($discussion, $trick);
                $entityManager->persist($discussion);
                $entityManager->flush();
                $this->addFlash('success', FlashMessages::MESSAGE_OK);
                return $this->redirectToRoute('app_trick_show', ['slug' => $trick->getSlug()]);
            }
        } catch (Exception $e) {
            $this->addFlash('error', FlashMessages::PROBLEM);
            $this->redirectToRoute('app_trick_show', ['slug' => $trick->getSlug()]);
        }

        $currentPage = $request->query->getInt('page', 1);

        return $this->render('tricks/trick.html.twig', [
            'form' => $form->createView(),
            'trick' => $trick,
            'medias' => $medias,
            'slug' => $slug,
            'trickbanner' => $trickBanner,
            'discussion' => $paginator,
            'current_page' => $currentPage,
        ]);
    }

    #[Route('/update-{name}', name:'trick_update')]
    public function update($name, EntityManagerInterface $entityManager, Request $request, TrickService $trickService){

        $trick = $entityManager->getRepository(Trick::class)->findOneBy(['name' => $name]);
        $medias = $trickService->getMediasForTrick($trick, $entityManager);
        $trickBanner = $trickService->getTrickBanner($trick, $entityManager);
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        try {
            if ($form->isSubmitted()) {

                $mediaUrl = $form->get('medias')->getData();
                $images = $form->get('images')->getData();

                if (!empty($mediaUrl)) {
                    $media = $trickService->createMedia($mediaUrl, $trick);
                    $entityManager->persist($media);
                }

                if (!empty($images)) {
                    $uploadsDirectory = $this->getParameter('uploads');
                    $addImage = $trickService->addImage($images, $trick, $entityManager, $uploadsDirectory);

                    if (isset($addImage['error'])) {
                        $this->addFlash('error', $addImage['error']);
                        return $this->redirectToRoute('app_trick_update', ['name' => $trick->getName()]);
                    }
                }

                $trick = $trickService->newLastUpdate($trick);
                $entityManager->persist($trick);
                $entityManager->flush();
                $this->addFlash('success', FlashMessages::TRICK_UPDATE . $trick->getName());
                return $this->redirectToRoute('app_trick_show', ['slug' => $trick->getSlug()]);
            }
        } catch (Exception $e) {
            $this->addFlash('error', FlashMessages::PROBLEM);
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
        $this->addFlash('success', FlashMessages::BANNER_CHANGE);
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
        $this->addFlash('success', FlashMessages::TRICK_DELETE.$trick->getName());
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

        $this->addFlash('success', FlashMessages::MEDIA_DELETE);
        return $this->redirectToRoute('app_trick_update', ['name' => $trick->getName()]);
    }

    #[Route('/discussion/delete/{id}', name: 'discussion_delete')]
    public function deleteComment($id, EntityManagerInterface $entityManager, DiscussionRepository $discussionRepository)
    {
        try {
            $discussion = $entityManager->getRepository(Discussion::class)->findOneBy(['id' => $id]);
            $trick = $entityManager->getRepository(Trick::class)->findOneBy(['id' => $discussion->getTrickId()]);
            $discussionRepository->remove($discussion, true);

            $this->addFlash('success', FlashMessages::MESSAGE_DELETE);
            return $this->redirectToRoute('app_trick_show', ['slug' => $trick->getSlug()]);
        } catch (Exception $e) {
            $this->addFlash('error', FlashMessages::PROBLEM);
            $this->redirectToRoute('app_trick_show', ['slug' => $trick->getSlug()]);
        }
        
    }

}