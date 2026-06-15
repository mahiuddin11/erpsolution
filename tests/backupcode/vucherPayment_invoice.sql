--update transaction table paymant_invoice cullum , defulte payment_invoice culums null set. 

UPDATE account_transactions
SET payment_invoice = NULL
WHERE payment_invoice = invoice
  AND type IN ('purchase', 'sale');


SELECT 
    at.id                          as at_id,
    at.invoice                     as at_invoice,
    at.type                        as at_type,
    at.account_id                  as at_account_id,
    at.debit                       as at_debit,
    at.credit                      as at_credit,
    at.payment_invoice             as at_wrong_payment_invoice,
    at.created_at                  as at_created_at,

    -- Dabit Voucher Details match
    dvd.id                         as dvd_id,
    dvd.payment_invoice            as dvd_payment_invoice,
    dv.voucher_no                  as dv_voucher_no,

    -- Credit Voucher Details match
    cvd.id                         as cvd_id,
    cvd.payment_invoice            as cvd_payment_invoice,
    cv.voucher_no                  as cv_voucher_no,

    -- Journal Voucher Details match
    jvd.id                         as jvd_id,
    jvd.payment_invoice            as jvd_payment_invoice,
    jv.voucher_no                  as jv_voucher_no

FROM account_transactions at

-- Dabit Voucher Details
LEFT JOIN dabit_voucher_details dvd 
    ON dvd.payment_invoice = at.payment_invoice
    AND dvd.account_id = at.account_id
LEFT JOIN dabit_vouchers dv 
    ON dv.id = dvd.dabit_voucher_id

-- Credit Voucher Details
LEFT JOIN credit_voucher_details cvd 
    ON cvd.payment_invoice = at.payment_invoice
    AND cvd.account_id = at.account_id
LEFT JOIN credit_vouchers cv 
    ON cv.id = cvd.credit_voucher_id

-- Journal Voucher Details
LEFT JOIN journal_voucher_details jvd 
    ON jvd.payment_invoice = at.payment_invoice
    AND jvd.account_id = at.account_id
LEFT JOIN journal_vouchers jv 
    ON jv.id = jvd.journal_voucher_id

WHERE at.payment_invoice REGEXP '^[0-9]+$'

ORDER BY at.id DESC;




-- original payment_invoice finder 
SELECT 
    at.id,
    at.invoice,
    at.payment_invoice as current_wrong_value,
    ref.invoice as correct_value
FROM account_transactions at
JOIN account_transactions ref ON ref.id = at.payment_invoice
WHERE at.payment_invoice REGEXP '^[0-9]+$';


SELECT 
    at.id                           as at_id,
    at.invoice                      as at_invoice,
    at.payment_invoice              as at_wrong_payment_invoice,
    ref.invoice                     as correct_invoice,

    dvd.payment_invoice             as dvd_pay_invoice,
    cvd.payment_invoice             as cvd_pay_invoice,
    jvd.payment_invoice             as jvd_pay_invoice,

    CASE 
        WHEN dvd.payment_invoice = at.payment_invoice THEN 'DVD ✅'
        WHEN cvd.payment_invoice = at.payment_invoice THEN 'CVD ✅'
        WHEN jvd.payment_invoice = at.payment_invoice THEN 'JVD ✅'
        ELSE 'NO MATCH ❌'
    END                             as matched

FROM account_transactions at
JOIN account_transactions ref ON ref.id = at.payment_invoice
LEFT JOIN dabit_voucher_details dvd 
    ON dvd.payment_invoice = at.payment_invoice 
    AND dvd.account_id = at.account_id
LEFT JOIN credit_voucher_details cvd 
    ON cvd.payment_invoice = at.payment_invoice 
    AND cvd.account_id = at.account_id
LEFT JOIN journal_voucher_details jvd 
    ON jvd.payment_invoice = at.payment_invoice 
    AND jvd.account_id = at.account_id

WHERE at.payment_invoice REGEXP '^[0-9]+$'

ORDER BY at.id;


-- update and matching voucher details table quary 


UPDATE account_transactions at
JOIN account_transactions ref ON ref.id = at.payment_invoice
SET at.payment_invoice = ref.invoice
WHERE at.payment_invoice REGEXP '^[0-9]+$';

-- Step 2: dabit_voucher_details update (account_transactions এর সাথে match করে)
UPDATE dabit_voucher_details dvd
JOIN account_transactions ref ON ref.id = dvd.payment_invoice
SET dvd.payment_invoice = ref.invoice
WHERE dvd.payment_invoice REGEXP '^[0-9]+$';

-- Step 3: credit_voucher_details update
UPDATE credit_voucher_details cvd
JOIN account_transactions ref ON ref.id = cvd.payment_invoice
SET cvd.payment_invoice = ref.invoice
WHERE cvd.payment_invoice REGEXP '^[0-9]+$';

-- Step 4: journal_voucher_details update
UPDATE journal_voucher_details jvd
JOIN account_transactions ref ON ref.id = jvd.payment_invoice
SET jvd.payment_invoice = ref.invoice
WHERE jvd.payment_invoice REGEXP '^[0-9]+$';

-- Step 5: verify সব ঠিক হয়েছে কিনা
SELECT 'account_transactions'  as tbl, COUNT(*) as remaining FROM account_transactions  WHERE payment_invoice REGEXP '^[0-9]+$'
UNION ALL
SELECT 'dabit_voucher_details',        COUNT(*) FROM dabit_voucher_details        WHERE payment_invoice REGEXP '^[0-9]+$'
UNION ALL
SELECT 'credit_voucher_details',       COUNT(*) FROM credit_voucher_details       WHERE payment_invoice REGEXP '^[0-9]+$'
UNION ALL
SELECT 'journal_voucher_details',      COUNT(*) FROM journal_voucher_details      WHERE payment_invoice REGEXP '^[0-9]+$';