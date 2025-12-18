CREATE TABLE IF NOT EXISTS subscriptions (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NOT NULL,
  plan_code VARCHAR(16) NOT NULL,
  status ENUM('pending','active','expired','canceled') NOT NULL DEFAULT 'pending',
  started_at DATETIME NULL,
  ends_at DATETIME NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NULL,
  CONSTRAINT fk_sub_user FOREIGN KEY (user_id) REFERENCES users(id),
  KEY ix_sub_user_status (user_id, status),
  KEY ix_sub_plan (plan_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
