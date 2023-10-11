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
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use PhpParser\Node\Stmt\TryCatch;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\QueryBuilder;

#[Route(name: 'app_')]
class TricksController extends AbstractController {

    #[Route('/create', name:'create')]
    public function create(Request $request, EntityManagerInterface $manager, SlugifyService $slugifyService){

        $trick = new Trick();
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        try {
            if ($form->isSubmitted() && $form->isValid()) {
                $trick = $this->prepareTrick($trick, $slugifyService);
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
    public function show($slug, EntityManagerInterface $entityManager, Request $request){

        $trick = $entityManager->getRepository(Trick::class)->findOneBy(['slug' => $slug]);
        $medias = $this->getMediasForTrick($trick, $entityManager);
        $trickBanner = $this->getTrickBanner($trick, $entityManager);
        $paginator = $this->paginate($entityManager, $trick, $request);

        $discussion = new Discussion();
        $form = $this->createForm(DiscussionType::class, $discussion);
        $form->handleRequest($request);

        try {
            if ($form->isSubmitted() && $form->isValid()) {
                $discussion = $this->prepareComment($discussion, $trick);
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
        return $this->redirectToRoute('app_trick_update', ['slug' => $trick->getSlug()]);
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

    private function prepareTrick(Trick $trick, SlugifyService $slugifyService)
    {
        $now = new \DateTime();
        $trick->setPublished($now);
        $trick->setLastUpdate($now);
        $user = $this->security->getUser();
        $trick->setUserId($user);
        $text = $trick->getName();
        $text = $slugifyService->slugify($text);
        $trick->setSlug($text);

        return $trick;
    }

    private function prepareComment(Discussion $discussion, Trick $trick)
    {
        $now = new \DateTime();
        $discussion->setCreationDate($now);
        $user = $this->security->getUser();
        $discussion->setUserId($user);
        $discussion->setTrickId($trick);

        return $discussion;
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
                $this->addFlash('error', FlashMessages::TOO_LARGE);
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
                $this->addFlash('error', FlashMessages::TOO_LARGE);
                return $this->redirectToRoute('app_create');
            }
        }
    }

    private function paginate(EntityManagerInterface $entityManager, Trick $trick, Request $request)
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $entityManager->getRepository(Discussion::class)->createQueryBuilder('d');
        $queryBuilder
            ->where('d.trickId = :trickId')
            ->setParameter('trickId', $trick->getId())
            ->orderBy('d.creationDate', 'DESC');

        $commentsQuery = $queryBuilder->getQuery();

        $paginator = new Paginator($commentsQuery);
        $paginator->getQuery()
            ->setFirstResult($request->query->getInt('page', 1) * 10 - 10)
            ->setMaxResults(10);

        return $paginator;
    }

}