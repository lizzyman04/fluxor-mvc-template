<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class DefaultUsersSeeder extends AbstractSeed
{
    public function run(): void
    {
        $users = $this->table('users');

        $data = [
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                // password: admin123
                'password' => '$2a$12$vc2KRrwyOQrgbfc7c5cYkO1FAqeQzVyobjnIDnZRpEdcR4LOg1vn.',
                'role' => 'admin',
            ],
            [
                'name' => 'Demo User',
                'email' => 'demo@example.com',
                // password: demo123
                'password' => '$2a$12$bIH6BN9af4q.T.zSqjUyVeqMjZd30fYP8/feM9HKYd0kfBkovbS86',
                'role' => 'user',
            ],
        ];

        foreach ($data as $row) {
            $exists = $this->fetchRow(
                "SELECT id FROM users WHERE email = '{$row['email']}' LIMIT 1"
            );

            if (!$exists) {
                $users->insert($row)->saveData();
            }
        }
    }
}
