<?php

namespace App\Entity;

use App\Repository\ArticleImageRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArticleImageRepository::class)]
class ArticleImage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'articleImages')]
    private ?article $article = null;

    #[ORM\Column(length: 255)]
    private ?string $image_url = null;

    #[ORM\Column]
    private ?\DateTime $created_date = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $updated_date = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getArticle(): ?article
    {
        return $this->article;
    }

    public function setArticle(?article $article): static
    {
        $this->article = $article;

        return $this;
    }

    public function getImageUrl(): ?string
    {
        return $this->image_url;
    }

    public function setImageUrl(string $image_url): static
    {
        $this->image_url = $image_url;

        return $this;
    }

    public function getCreatedDate(): ?\DateTime
    {
        return $this->created_date;
    }

    public function setCreatedDate(\DateTime $created_date): static
    {
        $this->created_date = $created_date;

        return $this;
    }

    public function getUpdatedDate(): ?\DateTime
    {
        return $this->updated_date;
    }

    public function setUpdatedDate(?\DateTime $updated_date): static
    {
        $this->updated_date = $updated_date;

        return $this;
    }

    public function setArticleImage(null $null)
    {
        return $this;
    }
}
