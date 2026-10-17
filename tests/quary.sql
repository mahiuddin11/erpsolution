SELECT 
    pd.id AS purchase_detail_id,
    pd.purchases_id,
    p.invoice_no,
    p.date,
    pd.product_id,
    pd.quantity,
    pd.unit_price,
    pd.total_price,
    pd.branch_id AS detail_branch_id,
    p.branch_id AS purchase_branch_id,
    pd.warehouse_id AS detail_warehouse_id,
    p.warehouse_id AS purchase_warehouse_id,
    p.supplier_id
FROM purchases_details pd
LEFT JOIN purchases p ON p.id = pd.purchases_id
WHERE pd.product_id = 1495
ORDER BY p.date DESC, pd.purchases_id DESC;


UPDATE purchases_details pd
LEFT JOIN purchases p ON p.id = pd.purchases_id
SET 
    pd.warehouse_id = 6,
    p.warehouse_id = 5
WHERE pd.product_id = 1495;



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
WHERE sd.product_id = 1495
ORDER BY s.date DESC, sd.sale_id DESC;



UPDATE sales__details sd
LEFT JOIN sales s ON s.id = sd.sale_id
SET sd.warehouse_id = 6,
    s.warehouse_id  = 5
WHERE sd.product_id = 1495;

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