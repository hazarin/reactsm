<?php

namespace App\Mapping;

use Doctrine\ORM\Mapping as ORM;
use DateTime;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Class EntityTimeStampBase
 *
 * @ORM\HasLifecycleCallbacks
 */
class EntityTimeStampBase implements EntityTimeStampInterface
{
    /**
     * @ORM\Column(name="created_at", type="datetime")
     * @Groups({"get"})
     */
    protected $createdAt;

    /**
     * @ORM\Column(name="updated_at", type="datetime")
     * @Groups({"get"})
     */
    protected $updatedAt;

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updateTimestamps(): void
    {
        $now = new DateTime('now');

        $this->setUpdatedAt($now);

        if ($this->getCreatedAt() === null) {
            $this->setCreatedAt($now);
        }
    }

    /**
     * @inheritDoc
     */
    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    /**
     * @inheritDoc
     */
    public function setCreatedAt(DateTime $createdAt): EntityTimeStampInterface
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @inheritDoc
     */
    public function setUpdatedAt(DateTime $updatedAt): EntityTimeStampInterface
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
