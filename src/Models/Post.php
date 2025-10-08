<?php

namespace Source\Models;

use DateTimeImmutable;
use DateTimeInterface;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Relation\BelongsTo;

#[Entity(table: 'posts')]
class Post
{
    #[Column(type: 'primary')]
    public int $id;

    #[Column(type: 'string')]
    public string $title;

    #[Column(type: 'text')]
    public string $content;

    #[BelongsTo(target: User::class, innerKey: 'user_id', fkAction: 'CASCADE')]
    public User $user;

    #[Column(type: 'datetime', name: 'created_at')]
    public DateTimeInterface $createdAt;

    #[Column(type: 'datetime', name: 'updated_at')]
    public DateTimeInterface $updatedAt;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }

    // Método para acessar o user_id diretamente
    public function getUserId(): int
    {
        return $this->user->id;
    }
}