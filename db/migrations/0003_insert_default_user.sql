-- Insert default admin user (password: admin123)
INSERT IGNORE INTO users (name, email, password, role) VALUES (
    'Admin User',
    'admin@example.com',
    '$2a$12$vc2KRrwyOQrgbfc7c5cYkO1FAqeQzVyobjnIDnZRpEdcR4LOg1vn.',
    'admin'
);

-- Insert demo user (password: demo123)
INSERT IGNORE INTO users (name, email, password, role) VALUES (
    'Demo User',
    'demo@example.com',
    '$2a$12$bIH6BN9af4q.T.zSqjUyVeqMjZd30fYP8/feM9HKYd0kfBkovbS86',
    'user'
);
