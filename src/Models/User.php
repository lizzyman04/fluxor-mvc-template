<?php

namespace Source\Models;

use DateTime;
use DateTimeInterface;
use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Table;
use Cycle\Annotated\Annotation\Table\Index;
use Cycle\Annotated\Annotation\Relation\HasMany;

#[Entity(table: "users")]
#[Table(indexes: [
    new Index(columns: ["email"], unique: true),
    new Index(columns: ["role"])
])]
class User
{
    #[Column(type: "bigPrimary")]
    private int $id;

    #[Column(type: "string", length: 100, nullable: false)]
    private string $name;

    #[Column(type: "string", length: 255, nullable: false)]
    private string $email;

    #[Column(type: "string", length: 255, nullable: false)]
    private string $password;

    #[Column(type: "string", length: 20, nullable: false)]
    private string $role = 'user';

    #[Column(type: "datetime", name: "created_at")]
    private DateTimeInterface $createdAt;

    #[Column(type: "datetime", name: "updated_at")]
    private DateTimeInterface $updatedAt;

    #[HasMany(target: Post::class, innerKey: 'id', outerKey: 'user_id', fkAction: 'CASCADE')]
    private $posts;

    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
    }

    // Getters
    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function getPosts()
    {
        return $this->posts;
    }

    // Setters
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;
        return $this;
    }

    public function updateTimestamps(): self
    {
        $this->updatedAt = new DateTime();
        return $this;
    }

    // Helpers
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt->format('Y-m-d H:i:s')
        ];
    }
}