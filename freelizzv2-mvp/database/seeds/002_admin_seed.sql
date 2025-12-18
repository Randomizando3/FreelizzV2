INSERT INTO settings(`key`,`value`,updated_by,updated_at) VALUES
('support_email','',NULL,NOW()),
('support_whatsapp','',NULL,NOW()),
('contact_to_email','',NULL,NOW()),
('verification_price_cents','0',NULL,NOW()),
('mp_access_token','',NULL,NOW()),
('mp_webhook_secret','',NULL,NOW())
ON DUPLICATE KEY UPDATE `value`=VALUES(`value`), updated_at=NOW();

INSERT INTO plans(code,name,proposals_per_day,featured_public,take_rate_pct,withdraw_min_cents,withdraw_window,withdraw_speed,can_view_avg,support_priority,created_at,updated_at)
VALUES
('free','Free',2,0,20.00,10000,'15 a 20 (mensal)','15 a 20 (mensal)',0,1,NOW(),NOW()),
('plus','Plus',10,0,10.00,10000,'a cada 7 dias','a cada 7 dias',1,2,NOW(),NOW()),
('premium','Premium',0,1,1.00,10000,'24-48h','24-48h',1,3,NOW(),NOW())
ON DUPLICATE KEY UPDATE
name=VALUES(name),
proposals_per_day=VALUES(proposals_per_day),
featured_public=VALUES(featured_public),
take_rate_pct=VALUES(take_rate_pct),
withdraw_min_cents=VALUES(withdraw_min_cents),
withdraw_window=VALUES(withdraw_window),
withdraw_speed=VALUES(withdraw_speed),
can_view_avg=VALUES(can_view_avg),
support_priority=VALUES(support_priority),
updated_at=NOW();
