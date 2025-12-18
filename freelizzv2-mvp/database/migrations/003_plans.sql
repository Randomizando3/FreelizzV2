CREATE TABLE IF NOT EXISTS plans (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  code VARCHAR(16) NOT NULL,
  name VARCHAR(60) NOT NULL,
  proposals_per_day INT NOT NULL DEFAULT 0,        -- 0 = ilimitado
  featured_public TINYINT(1) NOT NULL DEFAULT 0,   -- premium destaque público
  take_rate_pct DECIMAL(5,2) NOT NULL DEFAULT 0,   -- taxa %
  withdraw_min_cents INT NOT NULL DEFAULT 0,
  withdraw_window VARCHAR(60) NOT NULL DEFAULT '',
  withdraw_speed VARCHAR(60) NOT NULL DEFAULT '',
  can_view_avg TINYINT(1) NOT NULL DEFAULT 0,
  support_priority INT NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NULL,
  UNIQUE KEY uq_plans_code (code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
