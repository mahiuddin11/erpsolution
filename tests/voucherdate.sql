SELECT type, COUNT(*) AS cnt FROM account_transactions GROUP BY type ORDER BY cnt DESC;
SELECT COUNT(*) FROM account_transactions WHERE voucher_date IS NULL;

UPDATE account_transactions t
JOIN dabit_vouchers v ON v.id = t.table_id
SET t.voucher_date = v.date
WHERE t.type = 'debit_voucher';

UPDATE account_transactions t
JOIN credit_vouchers v ON v.id = t.table_id
SET t.voucher_date = v.date
WHERE t.type = 'credit_voucher';

UPDATE account_transactions t
JOIN contra_vouchers v ON v.id = t.table_id
SET t.voucher_date = v.date
WHERE t.type = 'contra_voucher';

UPDATE account_transactions t
JOIN journal_vouchers v ON v.id = t.table_id   
SET t.voucher_date = v.date
WHERE t.type = 'journal_voucher';

UPDATE account_transactions
SET voucher_date = DATE(created_at)
WHERE voucher_date IS NULL;

SELECT COUNT(*) FROM account_transactions WHERE voucher_date IS NULL;

SELECT type, COUNT(*) AS cnt FROM account_transactions WHERE voucher_date IS NULL GROUP BY type ORDER BY cnt DESC;
