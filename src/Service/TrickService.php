<?php

namespace App\Service;

use App\Constants\FlashMessages;
use App\Entity\Discussion;
use App\Entity\Media;
use App\Entity\Trick;
use App\Service\SlugifyService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Security;

class TrickService
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function getMediasForTrick(Trick $trick, EntityManagerInterface $entityManager): array
    {
        $images = $entityManager->getRepository(Media::class)->findBy(['trickId' => $trick->getId()]);
        $medias = [];

        foreach ($images as $image) {
            $medias[] = $this->formatMedia($image);
        }

        return $medias;
    }

    public function getTrickBanner(Trick $trick, EntityManagerInterface $entityManager): array
    {
        $media = $entityManager->getRepository(Media::class)->findBy(['trickId' => $trick, 'banner' => true]);
        $trickBanner[$trick->getId()] = $media;

        return $trickBanner;
    }

    public function formatMedia(Media $media): array
    {
        return [
            'source' => $media->getUrl(),
            'type' => $media->getType(),
            'banner' => $media->isBanner(),
            'id' => $media->getId()
        ];
    }

    public function prepareTrick(Trick $trick, SlugifyService $slugifyService)
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

    public function prepareComment(Discussion $discussion, Trick $trick)
    {
        $now = new \DateTime();
        $discussion->setCreationDate($now);
        $user = $this->security->getUser();
        $discussion->setUserId($user);
        $discussion->setTrickId($trick);

        return $discussion;
    }

    public function newLastUpdate(Trick $trick)
    {
        $now = new \DateTime();
        $trick->setLastUpdate($now);
        $user = $this->security->getUser();
        $trick->setUserId($user);

        return $trick;
    }

    public function createMedia($mediaUrl, Trick $trick)
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

    public function createImage($images, Trick $trick, EntityManagerInterface $manager, $uploadsDirectory)
    {
        foreach ($images as $uploadedImage) {
            $imageSize = $uploadedImage->getSize();

            if ($imageSize <= 1000000) {
                $fileName = md5(uniqid()) . '.' . $uploadedImage->guessExtension();
                $uploadedImage->move($uploadsDirectory, $fileName);
                $media = $this->createMedia($fileName, $trick);
                $media->setBanner(true);
                $manager->persist($media);
            } else {
                return ['error', FlashMessages::TOO_LARGE];
            }
        }
    }

    public function addImage($images, Trick $trick, EntityManagerInterface $manager, $uploadsDirectory)
    {
        foreach ($images as $uploadedImage) {
            $imageSize = $uploadedImage->getSize();

            if ($imageSize <= 1000000) {
                $fileName = md5(uniqid()) . '.' . $uploadedImage->guessExtension();
                $uploadedImage->move($uploadsDirectory, $fileName);
                $media = $this->createMedia($fileName, $trick);
                $media->setBanner(false);
                $manager->persist($media);
            } else {
                return ['error', FlashMessages::TOO_LARGE];
            }
        }
    }

    public function paginate(EntityManagerInterface $entityManager, Trick $trick, $request)
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
