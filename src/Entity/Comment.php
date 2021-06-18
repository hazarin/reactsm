<?php

namespace App\Entity;

use App\Mapping\EntityTimeStampBase;
use App\Repository\CommentRepository;
use Doctrine\ORM\Mapping as ORM;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=CommentRepository::class)
 * @ORM\HasLifecycleCallbacks
 *
 * @OA\Schema(required={"text"})
 */
class Comment extends EntityTimeStampBase
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="comments")
     *
     * @OA\Property(ref=@Model(type=User::class))
     * @Groups({"get", "set"})
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Article", inversedBy="comments")
     *
     * @OA\Property(ref=@Model(type=Article::class))
     * @Groups({"get", "set"})
     */
    private $article;

    /**
     * @ORM\Column(type="text")
     *
     * @OA\Property(type="text")
     * @Groups({"get", "set"})
     */
    private string $text;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user): void
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * @param mixed $article
     */
    public function setArticle($article): void
    {
        $this->article = $article;
    }

    /**
     * @return string|null
     */
    public function getText(): ?string
    {
        return $this->text;
    }

    /**
     * @param string $text
     * @return $this
     */
    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }
}
