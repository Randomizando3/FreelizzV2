CREATE TABLE IF NOT EXISTS settings (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  `key` VARCHAR(120) NOT NULL,
  `value` TEXT NOT NULL,
  updated_by BIGINT UNSIGNED NULL,
  updated_at DATETIME NULL,
  UNIQUE KEY uq_settings_key (`key`),
  KEY ix_settings_updated_by (updated_by)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
