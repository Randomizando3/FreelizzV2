CREATE TABLE IF NOT EXISTS disputes (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  project_id BIGINT UNSIGNED NOT NULL,
  opened_by BIGINT UNSIGNED NOT NULL,
  status ENUM('open','under_review','resolved','canceled') NOT NULL DEFAULT 'open',
  reason VARCHAR(120) NOT NULL,
  details TEXT NOT NULL,
  admin_notes TEXT NULL,
  resolved_at DATETIME NULL,
  resolved_by BIGINT UNSIGNED NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NULL,
  CONSTRAINT fk_disp_proj FOREIGN KEY (project_id) REFERENCES projects(id),
  CONSTRAINT fk_disp_opened FOREIGN KEY (opened_by) REFERENCES users(id),
  KEY ix_disp_status (status, created_at),
  KEY ix_disp_proj (project_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
