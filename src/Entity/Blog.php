<?php

namespace App\Entity;

use App\Repository\BlogRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BlogRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Blog
{

    use TimestampableEntity;


    #[Groups(['select_box'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['select_box'])]
    #[Assert\NotBlank]
    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[Groups(['select_box'])]
    #[Assert\NotBlank(message: 'Заголовок обязательный')]
    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[Groups(['select_box'])]
    #[Assert\NotBlank]
    #[ORM\Column(type: Types::TEXT)]
    private ?string $text = null;


    #[ORM\ManyToOne(targetEntity: Category::class)]
    #[ORM\JoinColumn(name: 'category_id', referencedColumnName: 'id')]
    private Category|null $category = null;


    #[Ignore]
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    private User|null $user = null;

    #[Groups(['select_box'])]
    #[ORM\JoinTable(name: 'tags_to_blog')]
    #[ORM\JoinColumn(name: 'blog_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'tag_id', referencedColumnName: 'id', unique: true)]
    #[ORM\ManyToMany(targetEntity: 'Tag', cascade: ['persist'])]
    private Collection|PersistentCollection $tags;


    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $percent = null;


    #[ORM\Column(type: Types::STRING)]
    private ?string $status = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?DateTime $blockedAt;

    /**
     * @var Collection<int, Comment>
     */
    #[Ignore]
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'blog', orphanRemoval: true, cascade: ['persist'])]
    #[ORM\OrderBy(['id' => 'DESC'])]
    private Collection $comments;

    /**
     * @return DateTime|null
     */
    public function getBlockedAt(): ?DateTime
    {
        return $this->blockedAt;
    }

    /**
     * @param DateTime|null $blockedAt
     */
    public function setBlockedAt(?DateTime $blockedAt): static
    {
        $this->blockedAt = $blockedAt;

        return $this;
    }


    /**
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * @param string|null $status
     */
    public function setStatus(?string $status): static
    {
        $this->status = $status;

        return $this;
    }


    /**
     * @return string|null
     */
    public function getPercent(): ?string
    {
        return $this->percent;
    }

    /**
     * @param string|null $percent
     */
    public function setPercent(?string $percent): static
    {
        $this->percent = $percent;

        return $this;
    }


    public function __construct(UserInterface|User $user)
    {
        $this->status   = 'pending';
        $this->user     = $user;
        $this->comments = new ArrayCollection();
    }


    #[ORM\PreUpdate]
    public function setBlockedAtValue(): void
    {
        if ($this->status == 'blocked' && !$this->blockedAt) {
            $this->blockedAt = new \DateTime();
        }
    }


    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User|null $user
     */
    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): static
    {
        $this->text = $text;

        return $this;
    }


    public function getDescription(): ?string
    {
        return $this->description;
    }


    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }


    public function getCategory(): ?Category
    {
        return $this->category;
    }


    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }


    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function setTags(ArrayCollection $tags): static
    {
        $this->tags = $tags;

        return $this;
    }

    public function addTag(Tag $tag): void
    {
        $this->tags[] = $tag;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setBlog($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getBlog() === $this) {
                $comment->setBlog(null);
            }
        }

        return $this;
    }
/*
    public function getUserId(): int
    {
        return $this->getUser()->getId();
    }
*/

}
