<?php

namespace App\Entity;

use App\Repository\MediaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MediaRepository::class)]
class Media
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Trick::class, inversedBy: 'medias')]
    private ?Trick $trickId = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column(type: 'text', length: 65535)]
    private ?string $url = null;

    #[ORM\Column]
    private ?bool $banner = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTrickId(): ?int
    {
        return $this->trickId;
    }

    public function setTrickId($trickId): static
    {
        $this->trickId = $trickId;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function isBanner(): ?bool
    {
        return $this->banner;
    }

    public function setBanner(bool $banner): static
    {
        $this->banner = $banner;

        return $this;
    }
}
