UPDATE plans SET proposal_sort_weight=1, price_cents=0 WHERE code='free';
UPDATE plans SET proposal_sort_weight=2, price_cents=4900 WHERE code='plus';
UPDATE plans SET proposal_sort_weight=3, price_cents=9900 WHERE code='premium';
