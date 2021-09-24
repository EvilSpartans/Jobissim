<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity(repositoryClass=MessageRepository::class)
 */
class Message
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="messages")
     * @ORM\JoinColumn(nullable=false)
     * @Serializer\Groups(groups="messages_list")
     */
    private $author;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="text")
     * @Serializer\Groups(groups="chat_list", groups="messages_list")
     */
    private $content;

    /**
     * @ORM\ManyToOne(targetEntity=Messaging::class, inversedBy="messages")
     * @ORM\JoinColumn(nullable=false)
     * @Serializer\Groups(groups="messages_list")
     */
    private $messaging;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="messagesReadBy")
     */
    private $readBy;

    public function __construct()
    {
        $this->createdAt = new \DateTime('now');
        $this->readBy = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getMessaging(): ?Messaging
    {
        return $this->messaging;
    }

    public function setMessaging(?Messaging $messaging): self
    {
        $this->messaging = $messaging;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getReadBy(): Collection
    {
        return $this->readBy;
    }

    public function addReadBy(User $readBy): self
    {
        if (!$this->readBy->contains($readBy)) {
            $this->readBy[] = $readBy;
        }

        return $this;
    }

    public function removeReadBy(User $readBy): self
    {
        $this->readBy->removeElement($readBy);

        return $this;
    }
}
