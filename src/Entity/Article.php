<?php

namespace App\Entity;

use App\Mapping\EntityTimeStampBase;
use App\Repository\ArticleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Annotations as OA;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;

/**
 * @ORM\Entity(repositoryClass=ArticleRepository::class)
 * @ORM\HasLifecycleCallbacks
 *
 * @OA\Schema(required={"title", "content"})
 */
class Article extends EntityTimeStampBase
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     * @OA\Property(description="Article id")
     *
     * @Groups({"get", "list", "article"})
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @OA\Property(type="string", maxLength=255)
     *
     * @Groups({"get", "set", "list", "article"})
     */
    private string $title;

    /**
     * @ORM\Column(type="text")
     *
     * @OA\Property(type="text")
     *
     * @Groups({"get", "set", "article"})
     */
    private string $content;


    /**
     * @MaxDepth(1)
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="article")
     *
     * @Groups({"get", "set", "article"})
     */
    private Collection $comments;

    public function __construct() {
        $this->comments = new ArrayCollection();
    }

    /**
     * @return Collection
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    /**
     * @param ArrayCollection $comments
     */
    public function setComments(Collection $comments): void
    {
        $this->comments = $comments;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @param string $content
     * @return $this
     */
    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }
}
