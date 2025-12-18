ALTER TABLE jobs
  ADD COLUMN budget_min_cents INT NOT NULL DEFAULT 0,
  ADD COLUMN budget_max_cents INT NOT NULL DEFAULT 0,
  ADD COLUMN hourly_rate_cents INT NOT NULL DEFAULT 0,
  ADD COLUMN city VARCHAR(80) NULL,
  ADD COLUMN state VARCHAR(2) NULL,
  ADD COLUMN updated_at DATETIME NULL;

CREATE INDEX ix_jobs_status_published ON jobs(status, published_at);
