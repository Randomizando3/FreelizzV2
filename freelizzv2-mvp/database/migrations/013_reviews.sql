CREATE TABLE IF NOT EXISTS reviews (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  project_id BIGINT UNSIGNED NOT NULL,
  reviewer_id BIGINT UNSIGNED NOT NULL,
  reviewee_id BIGINT UNSIGNED NOT NULL,
  rating TINYINT NOT NULL,
  comment VARCHAR(500) NULL,
  created_at DATETIME NOT NULL,
  CONSTRAINT fk_rev_proj FOREIGN KEY (project_id) REFERENCES projects(id),
  CONSTRAINT fk_rev_reviewer FOREIGN KEY (reviewer_id) REFERENCES users(id),
  CONSTRAINT fk_rev_reviewee FOREIGN KEY (reviewee_id) REFERENCES users(id),
  UNIQUE KEY uq_rev_once (project_id, reviewer_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
