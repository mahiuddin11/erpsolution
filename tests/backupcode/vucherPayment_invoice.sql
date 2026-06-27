--update transaction table paymant_invoice cullum , defulte payment_invoice culums null set. 

UPDATE account_transactions
SET payment_invoice = NULL
WHERE payment_invoice = invoice
  AND type IN ('purchase', 'sale');

UPDATE account_transactions
SET payment_invoice = NULL
WHERE payment_invoice = '';

UPDATE dabit_voucher_details
SET payment_invoice = NULL
WHERE payment_invoice = '';


UPDATE credit_voucher_details
SET payment_invoice = NULL
WHERE payment_invoice = '';

UPDATE chart_of_accounts coa
JOIN suppliers s ON coa.accountable_id = s.id
SET coa.accountCode = s.supplierCode
WHERE coa.accountable_type = 'App\\Models\\Supplier';

UPDATE chart_of_accounts coa
JOIN customers c ON coa.accountable_id = c.id
SET coa.accountCode = c.customerCode
WHERE coa.accountable_type = 'App\\Models\\Customer';


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




-- stock priview opening to stock --
SELECT
    posd.date,
    posd.project_id,
    posd.product_opening_stock_id AS general_id,
    posd.branch_id,
    posd.product_id,
    posd.unit_price,
    posd.total_price,
    posd.quantity,
    'Opening' AS status,
    posd.updated_by,
    posd.created_by,
    posd.deleted_by,
    posd.deleted_at,
    posd.created_at,
    posd.updated_at,
    pos.invoice_no
FROM product_opening_stock_details posd
JOIN product_opening_stocks pos ON pos.id = posd.product_opening_stock_id
WHERE posd.product_id = 1235
AND NOT EXISTS (
    SELECT 1
    FROM stocks s
    WHERE s.general_id   = posd.product_opening_stock_id
      AND s.product_id   = posd.product_id
      AND s.branch_id    = posd.branch_id
      AND s.project_id   = posd.project_id
      AND s.date         = posd.date
      AND s.quantity      = posd.quantity
);

-- final update opening to stock  --
INSERT INTO stocks (
    date,
    project_id,
    general_id,
    branch_id,
    product_id,
    unit_price,
    total_price,
    quantity,
    status,
    updated_by,
    created_by,
    deleted_by,
    deleted_at,
    created_at,
    updated_at,
    invoice_no
)
SELECT
    posd.date,
    posd.project_id,
    posd.product_opening_stock_id AS general_id,
    posd.branch_id,
    posd.product_id,
    posd.unit_price,
    posd.total_price,
    posd.quantity,
    'Opening' AS status,
    posd.updated_by,
    posd.created_by,
    posd.deleted_by,
    posd.deleted_at,
    posd.created_at,
    posd.updated_at,
    pos.invoice_no
FROM product_opening_stock_details posd
JOIN product_opening_stocks pos ON pos.id = posd.product_opening_stock_id
WHERE posd.product_id = 1233
AND NOT EXISTS (
    SELECT 1
    FROM stocks s
    WHERE s.general_id   = posd.product_opening_stock_id
      AND s.product_id   = posd.product_id
      AND s.branch_id    = posd.branch_id
      AND s.project_id   = posd.project_id
      AND s.date         = posd.date
      AND s.quantity      = posd.quantity
);



-- purchase table to stock table check 
SELECT
    pd.date,
    pd.project_id,
    pd.purchases_id AS general_id,
    pd.branch_id,
    pd.product_id,
    pd.unit_price,
    pd.total_price,
    pd.quantity,
    CASE
        WHEN p.purchase_type = 'Direct' THEN 'Purchase'
        WHEN p.purchase_type = 'Manual' THEN 'Manual Purchase'
        ELSE 'Purchase'
    END AS status,
    pd.updated_by,
    pd.created_by,
    pd.deleted_by,
    pd.deleted_at,
    pd.created_at,
    pd.updated_at,
    p.invoice_no
FROM purchases_details pd
JOIN purchases p ON p.id = pd.purchases_id
WHERE pd.status = 'Active'
AND NOT EXISTS (
    SELECT 1
    FROM stocks s
    WHERE s.general_id = pd.purchases_id
      AND s.product_id = pd.product_id
      AND s.branch_id  = pd.branch_id
      AND s.project_id = pd.project_id
      AND s.date       = pd.date
      AND s.quantity    = pd.quantity
);

-- terget product purchase to stok check 
SELECT
    pd.date,
    pd.project_id,
    pd.purchases_id AS general_id,
    pd.branch_id,
    pd.product_id,
    pd.unit_price,
    pd.total_price,
    pd.quantity,
    CASE
        WHEN p.purchase_type = 'Direct' THEN 'Purchase'
        WHEN p.purchase_type = 'Manual' THEN 'Manual Purchase'
        ELSE 'Purchase'
    END AS status,
    pd.updated_by,
    pd.created_by,
    pd.deleted_by,
    pd.deleted_at,
    pd.created_at,
    pd.updated_at,
    p.invoice_no
FROM purchases_details pd
JOIN purchases p ON p.id = pd.purchases_id
WHERE pd.status = 'Active'
  AND pd.product_id = 1237
AND NOT EXISTS (
    SELECT 1
    FROM stocks s
    WHERE s.general_id = pd.purchases_id
      AND s.product_id = pd.product_id
      AND s.branch_id  = pd.branch_id
      AND s.project_id = pd.project_id
      AND s.date       = pd.date
      AND s.quantity    = pd.quantity
);



-- update purchase to stock
INSERT INTO stocks (
    date,
    project_id,
    general_id,
    branch_id,
    product_id,
    unit_price,
    total_price,
    quantity,
    status,
    updated_by,
    created_by,
    deleted_by,
    deleted_at,
    created_at,
    updated_at,
    invoice_no
)
SELECT
    pd.date,
    pd.project_id,
    pd.purchases_id AS general_id,
    pd.branch_id,
    pd.product_id,
    pd.unit_price,
    pd.total_price,
    pd.quantity,
    CASE
        WHEN p.purchase_type = 'Direct' THEN 'Purchase'
        WHEN p.purchase_type = 'Manual' THEN 'Manual Purchase'
        ELSE 'Purchase'
    END AS status,
    pd.updated_by,
    pd.created_by,
    pd.deleted_by,
    pd.deleted_at,
    pd.created_at,
    pd.updated_at,
    p.invoice_no
FROM purchases_details pd
JOIN purchases p ON p.id = pd.purchases_id
WHERE pd.product_id = 1237
AND NOT EXISTS (
    SELECT 1
    FROM stocks s
    WHERE s.general_id  = pd.purchases_id
      AND s.product_id  = pd.product_id
      AND s.branch_id   = pd.branch_id
      AND s.project_id <=> pd.project_id
      AND s.date        = pd.date
      AND s.quantity     = pd.quantity
);


--Ladger Marge --

UPDATE `account_transactions` SET account_id = 759 WHERE account_id = 1306;
UPDATE `journal_voucher_details` SET account_id = 759 WHERE account_id = 1306;
UPDATE `dabit_voucher_details` SET account_id = 759 WHERE account_id = 1306;
UPDATE `purchases` SET ledger_id = 759 WHERE ledger_id = 1306;
UPDATE `purchases_details` SET ledger_id = 759 WHERE ledger_id = 1306;
UPDATE `credit_vouchers` SET account_id = 759 WHERE account_id = 1306;
UPDATE `credit_vouchers` SET account_id = 759 WHERE account_id = 1306;
UPDATE `credit_voucher_details` SET account_id = 759 WHERE account_id = 1306;
UPDATE `projects` SET ledger_id = 759 WHERE ledger_id = 1306;
UPDATE `supplier_select_prices` SET account_id = 759 WHERE account_id = 1306;


--check chear of account supplyer id or customer id -- 
SELECT id, accountable_type, accountable_id, account_name, accountCode
FROM chart_of_accounts
WHERE id = 1306;


-- purchase id chececk --
SELECT 'chart_of_accounts' AS source_table, 'accountable_id' AS matched_column, id AS matched_id FROM chart_of_accounts WHERE accountable_id = (SELECT accountable_id FROM chart_of_accounts WHERE id = 1306)
UNION ALL
SELECT 'suppliers', 'id', id FROM suppliers WHERE id = (SELECT accountable_id FROM chart_of_accounts WHERE id = 1306)
UNION ALL
SELECT 'customers', 'id', id FROM customers WHERE id = (SELECT accountable_id FROM chart_of_accounts WHERE id = 1306)
UNION ALL
SELECT 'purchases', 'supplier_id', id FROM purchases WHERE supplier_id = (SELECT accountable_id FROM chart_of_accounts WHERE id = 1306)
UNION ALL
SELECT 'purchases', 'ledger_id', id FROM purchases WHERE ledger_id = (SELECT accountable_id FROM chart_of_accounts WHERE id = 1306)
UNION ALL
SELECT 'purchases_details', 'supplier_id', id FROM purchases_details WHERE supplier_id = (SELECT accountable_id FROM chart_of_accounts WHERE id = 1306)
UNION ALL
SELECT 'purchases_details', 'ledger_id', id FROM purchases_details WHERE ledger_id = (SELECT accountable_id FROM chart_of_accounts WHERE id = 1306)
UNION ALL
SELECT 'purchase_orders', 'supplier_id', id FROM purchase_orders WHERE supplier_id = (SELECT accountable_id FROM chart_of_accounts WHERE id = 1306)
UNION ALL
SELECT 'purchase_orders', 'account_id', id FROM purchase_orders WHERE account_id = (SELECT accountable_id FROM chart_of_accounts WHERE id = 1306)
UNION ALL
SELECT 'purchase_order_details', 'supplier_ledger_id', id FROM purchase_order_details WHERE supplier_ledger_id = (SELECT accountable_id FROM chart_of_accounts WHERE id = 1306)
UNION ALL
SELECT 'supplier_select_prices', 'supplier_id', id FROM supplier_select_prices WHERE supplier_id = (SELECT accountable_id FROM chart_of_accounts WHERE id = 1306)
UNION ALL
SELECT 'supplier_select_prices', 'account_id', id FROM supplier_select_prices WHERE account_id = (SELECT accountable_id FROM chart_of_accounts WHERE id = 1306);