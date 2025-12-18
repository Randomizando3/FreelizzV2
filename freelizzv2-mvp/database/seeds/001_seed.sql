INSERT INTO job_categories(name,slug) VALUES
('Design','design'),
('Desenvolvimento','dev'),
('Marketing','marketing')
ON DUPLICATE KEY UPDATE name=VALUES(name);
