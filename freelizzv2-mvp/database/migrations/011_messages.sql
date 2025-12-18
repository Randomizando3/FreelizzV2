CREATE TABLE IF NOT EXISTS project_messages (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  project_id BIGINT UNSIGNED NOT NULL,
  sender_id BIGINT UNSIGNED NOT NULL,
  body TEXT NOT NULL,
  created_at DATETIME NOT NULL,
  CONSTRAINT fk_msg_proj FOREIGN KEY (project_id) REFERENCES projects(id),
  CONSTRAINT fk_msg_sender FOREIGN KEY (sender_id) REFERENCES users(id),
  KEY ix_msg_proj_created (project_id, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
