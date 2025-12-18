CREATE TABLE IF NOT EXISTS freelancer_verifications (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NOT NULL,
  selfie_path VARCHAR(255) NOT NULL,
  document_path VARCHAR(255) NOT NULL,
  status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  reason VARCHAR(255) NULL,
  price_cents INT NOT NULL DEFAULT 0,
  paid_status ENUM('unpaid','paid','refunded') NOT NULL DEFAULT 'unpaid',
  created_at DATETIME NOT NULL,
  reviewed_at DATETIME NULL,
  reviewed_by BIGINT UNSIGNED NULL,
  CONSTRAINT fk_fv_user FOREIGN KEY (user_id) REFERENCES users(id),
  KEY ix_fv_status (status),
  KEY ix_fv_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
