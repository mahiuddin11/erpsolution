SELECT
    pd.id             AS detail_id,
    pd.purchases_id,
    p.invoice_no,
    p.custom_invoice,
    p.date,
    p.supplier_id,
    pd.product_id,
    pd.quantity,
    pd.unit_price,
    pd.branch_id      AS detail_branch_id,
    p.branch_id       AS purchase_branch_id,
    pd.warehouse_id   AS detail_warehouse_id,
    p.warehouse_id    AS purchase_warehouse_id,
    pd.backup_branch_id,
    (SELECT COUNT(*) FROM purchases_details x
      WHERE x.purchases_id = pd.purchases_id
        AND x.product_id <> pd.product_id
        AND x.deleted_at IS NULL) AS other_product_rows
FROM purchases_details pd
LEFT JOIN purchases p ON p.id = pd.purchases_id AND p.deleted_at IS NULL
WHERE pd.product_id = 1233
  AND pd.deleted_at IS NULL
ORDER BY p.date, pd.purchases_id;



UPDATE purchases_details pd
LEFT JOIN purchases p ON p.id = pd.purchases_id AND p.deleted_at IS NULL
LEFT JOIN (
    SELECT DISTINCT purchases_id
    FROM purchases_details
    WHERE product_id <> 4661 AND deleted_at IS NULL
) o ON o.purchases_id = pd.purchases_id
SET pd.branch_id = 4,
    p.branch_id  = IF(o.purchases_id IS NULL, 4, p.branch_id)
WHERE pd.product_id = 4661
  AND pd.deleted_at IS NULL;












SELECT 
    sd.id           AS sales_detail_id,
    sd.sale_id,
    s.invoice_no,
    s.date,
    sd.product_id,
    sd.qty,
    sd.rate,
    sd.price,
    sd.branch_id    AS detail_branch_id,
    s.branch_id     AS sale_branch_id,
    sd.warehouse_id AS detail_warehouse_id,
    s.warehouse_id  AS sale_warehouse_id,
    s.customer_id
FROM sales__details sd
LEFT JOIN sales s ON s.id = sd.sale_id
WHERE sd.product_id = 1608
ORDER BY s.date DESC, sd.sale_id DESC;



UPDATE sales__details sd
LEFT JOIN sales s ON s.id = sd.sale_id
SET sd.branch_id = 3,
    s.branch_id  = 3
WHERE sd.product_id = 1608;

SELECT 
    sd.id           AS sales_detail_id,
    sd.sale_id,
    s.invoice_no,
    s.date,
    sd.product_id,
    sd.qty,
    sd.rate,
    sd.price,
    sd.branch_id    AS detail_branch_id,
    s.branch_id     AS sale_branch_id,
    sd.warehouse_id AS detail_warehouse_id,
    s.warehouse_id  AS sale_warehouse_id,
    s.customer_id
FROM sales__details sd
LEFT JOIN sales s ON s.id = sd.sale_id
WHERE sd.product_id = 1608
ORDER BY s.date DESC, sd.sale_id DESC;