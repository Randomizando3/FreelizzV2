CREATE TABLE IF NOT EXISTS projects (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  job_id BIGINT UNSIGNED NOT NULL,
  client_id BIGINT UNSIGNED NOT NULL,
  freelancer_id BIGINT UNSIGNED NOT NULL,
  proposal_id BIGINT UNSIGNED NOT NULL,
  status ENUM('active','completed','canceled','disputed') NOT NULL DEFAULT 'active',
  created_at DATETIME NOT NULL,
  completed_at DATETIME NULL,
  updated_at DATETIME NULL,
  CONSTRAINT fk_proj_job FOREIGN KEY (job_id) REFERENCES jobs(id),
  CONSTRAINT fk_proj_client FOREIGN KEY (client_id) REFERENCES users(id),
  CONSTRAINT fk_proj_freela FOREIGN KEY (freelancer_id) REFERENCES users(id),
  CONSTRAINT fk_proj_prop FOREIGN KEY (proposal_id) REFERENCES proposals(id),
  UNIQUE KEY uq_proj_job (job_id),
  KEY ix_proj_client (client_id, status),
  KEY ix_proj_freela (freelancer_id, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
