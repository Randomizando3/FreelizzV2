CREATE TABLE IF NOT EXISTS freelancer_accounts (
  user_id BIGINT UNSIGNED PRIMARY KEY,
  plan_code VARCHAR(16) NOT NULL DEFAULT 'free',
  avatar_path VARCHAR(255) NULL,
  area VARCHAR(120) NULL,
  bio_html MEDIUMTEXT NULL,
  portfolio_url VARCHAR(255) NULL,
  portfolio_html MEDIUMTEXT NULL,
  payout_method ENUM('pix','bank') NULL,
  payout_details TEXT NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NULL,
  CONSTRAINT fk_fa_user FOREIGN KEY (user_id) REFERENCES users(id),
  KEY ix_fa_plan (plan_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
