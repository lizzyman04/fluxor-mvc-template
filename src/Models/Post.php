<?php

namespace Source\Models;

use DateTime;
use DateTimeInterface;
use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Table;
use Cycle\Annotated\Annotation\Table\Index;
use Cycle\Annotated\Annotation\Relation\BelongsTo;

#[Entity(table: "posts")]
#[Table(indexes: [
    new Index(columns: ["user_id"]),
    new Index(columns: ["created_at"])
])]
class Post
{
    #[Column(type: "bigPrimary")]
    private int $id;

    #[Column(type: "string", length: 255, nullable: false)]
    private string $title;

    #[Column(type: "text", nullable: false)]
    private string $content;

    #[Column(type: "bigInteger", name: "user_id", nullable: false)]
    private int $userId;

    #[Column(type: "datetime", name: "created_at")]
    private DateTimeInterface $createdAt;

    #[Column(type: "datetime", name: "updated_at")]
    private DateTimeInterface $updatedAt;

    #[BelongsTo(target: User::class, innerKey: 'user_id', outerKey: 'id', fkAction: 'CASCADE')]
    private $user;

    public function __construct(string $title = '', string $content = '', int $userId = 0)
    {
        $this->title = $title;
        $this->content = $content;
        $this->userId = $userId;
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
    }

    // Getters
    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    // Setters
    public function setTitle(string $title): self
    {
        $this->title = $title;
        $this->updatedAt = new DateTime();
        return $this;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;
        $this->updatedAt = new DateTime();
        return $this;
    }

    public function setUserId(int $userId): self
    {
        $this->userId = $userId;
        return $this;
    }

    public function updateTimestamps(): self
    {
        $this->updatedAt = new DateTime();
        return $this;
    }

    public function getExcerpt(int $length = 150): string
    {
        $text = strip_tags($this->content);
        if (strlen($text) <= $length) {
            return $text;
        }
        return substr($text, 0, $length) . '...';
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'excerpt' => $this->getExcerpt(),
            'user_id' => $this->userId,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt->format('Y-m-d H:i:s')
        ];
    }
}