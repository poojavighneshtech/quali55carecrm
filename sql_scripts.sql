-- [Date:18-08-2023] - store customer first pref source online/offline ...
ALTER TABLE `leads` ADD `customer_source` VARCHAR(10) NULL DEFAULT NULL AFTER `referredby`;

-- [Date:18-08-2023] - update customer_source to resp lead_source category..
UPDATE leads SET customer_source = "Online" WHERE lead_source IN ("Google Ads","Web Chat","Web Popup","Web Order","Web - Call","Web - WhatsApp");


UPDATE leads SET customer_source = "Offline" WHERE lead_source IN ("Wellness Forever","Reference","Ref","Agent","Corporate Booking","Returning Cust","Other","Just Dial");

-- UPDATE leads having source - Returning Cust and thats customers first lead having source  in ("Google Ads","Web Chat","Web Popup","Web Order","Web - Call","Web - WhatsApp")

-- [Working Script]
CREATE TEMPORARY TABLE temp_customers AS
SELECT DISTINCT customer_id
FROM leads
WHERE lead_source IN ("Google Ads", "Web Chat", "Web Popup", "Web Order", "Web - Call", "Web - WhatsApp");

UPDATE leads
SET customer_source = "Online"
WHERE lead_source = "Returning Cust"
AND customer_id IN (SELECT customer_id FROM temp_customers);

DROP TEMPORARY TABLE IF EXISTS temp_customers;

ALTER TABLE `vendor_inventory_mgmt` ADD `is_verified` VARCHAR(3) NOT NULL DEFAULT 'no' AFTER `comment`;
ALTER TABLE `vendor_details` ADD `notify_flag` VARCHAR(10) NOT NULL DEFAULT 'yes' AFTER `vendor_type`;
UPDATE `vendor_details` SET notify_flag = 'no' WHERE id IN (30,40,49,50,52,59,62,72,73,74,75,79,80,84,91,101,102,115,118);