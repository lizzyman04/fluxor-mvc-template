-- Insert default admin user (password: admin123)
INSERT IGNORE INTO users (name, email, password, role) VALUES (
    'Admin User',
    'admin@example.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'admin'
);

-- Insert demo user (password: demo123)
INSERT IGNORE INTO users (name, email, password, role) VALUES (
    'Demo User',
    'demo@example.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'user'
);
