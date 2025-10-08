<?php

namespace Source\Models;

use DateTimeImmutable;
use DateTimeInterface;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Relation\HasMany;

#[Entity(table: 'users')]
class User
{
    #[Column(type: 'primary')]
    public int $id;

    #[Column(type: 'string')]
    public string $name;

    #[Column(type: 'string')]
    public string $email;

    #[Column(type: 'string')]
    public string $password;

    #[Column(type: 'string')]
    public string $role;

    #[Column(type: 'datetime', name: 'created_at')]
    public DateTimeInterface $createdAt;

    #[Column(type: 'datetime', name: 'updated_at')]
    public DateTimeInterface $updatedAt;

    #[HasMany(target: Post::class)]
    public array $posts = [];

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
        $this->role = 'user';
    }
}