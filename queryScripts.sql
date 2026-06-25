-- To Store Reference Id for online payment of order if payment image is not available.
-- ALTER TABLE `del_orders` ADD `reference_id` VARCHAR(250) NULL AFTER `payment_image`;

-- To store Labour Charges Seperately for daily expenses.
-- ALTER TABLE `daily_expenses` ADD `labour` INT(5) NULL AFTER `expenses`;

-- CREATE TABLE `web_exception_log` (
--   `id` int(20) NOT NULL,
--   `function` varchar(250) NOT NULL,
--   `controller` varchar(250) NOT NULL,
--   `exception` varchar(500) NOT NULL,
--   `exception_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
--   `user` varchar(20) NOT NULL
-- ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
-- COMMIT;

-- ALTER TABLE `web_exception_log`
--   ADD PRIMARY KEY (`id`);

-- ALTER TABLE `web_exception_log`
--   MODIFY `id` int(20) NOT NULL AUTO_INCREMENT;
-- COMMIT;


-- CREATE TABLE `google_campain_report` (
--   `id` int(11) NOT NULL,
--   `campaign` varchar(250) NOT NULL,
--   `date` date NOT NULL,
--   `campaign_state` varchar(40) NOT NULL,
--   `campaign_type` varchar(40) NOT NULL,
--   `budget` int(11) NOT NULL,
--   `currency_code` varchar(20) NOT NULL,
--   `clicks` int(11) NOT NULL,
--   `impr` varchar(20) NOT NULL,
--   `ctr` varchar(10) NOT NULL,
--   `avg_cpc` varchar(20) NOT NULL,
--   `cost` varchar(20) NOT NULL,
--   `conversions` varchar(20) NOT NULL,
--   `view_through_conv` varchar(20) NOT NULL,
--   `cost_conv` varchar(10) NOT NULL,
--   `conv_rate` varchar(20) NOT NULL,
--   `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
--   `created_by` varchar(20) NOT NULL,
--   `updated_at` timestamp NULL DEFAULT NULL
-- ) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ALTER TABLE `google_campain_report`
--   ADD PRIMARY KEY (`id`);

-- ALTER TABLE `google_campain_report`
--   MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8966;
-- COMMIT;


-- CREATE TABLE `q5c_dev_intraapp`.`virtual_wh_inventory_mgmt` ( 
--   `id` INT(20) NOT NULL AUTO_INCREMENT , 
--   `vdr_prod_details_id` INT(20) NOT NULL , 
--   `prod_id` INT(20) NOT NULL , 
--   `vdr_id` INT(20) NOT NULL , 
--   `vdr_wh_id` INT(20) NOT NULL , 
--   `vir_wh_id` INT(20) NOT NULL , 
--   `inventory_id` INT(20) NOT NULL , 
--   `prod_qty` INT(20) NOT NULL , 
--   `status` INT(2) NOT NULL , 
--   `in_time` TIMESTAMP NOT NULL , 
--   `out_time` TIMESTAMP NULL DEFAULT NULL , 
--   `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , 
--   `created_by` VARCHAR(50) NOT NULL , 
--   `updated_at` TIMESTAMP NULL DEFAULT NULL ,
--   `updated_by` VARCHAR(50) NULL DEFAULT NULL , 
--   PRIMARY KEY (`id`)) ENGINE = InnoDB;

--   ALTER TABLE `virtual_wh_inventory_mgmt` ADD `order_details_id` INT(20) NOT NULL AFTER `id`;

--   ALTER TABLE `virtual_wh_inventory_mgmt` ADD `del_boy` INT(20) NULL AFTER `out_time`;

--   ALTER TABLE `virtual_wh_inventory_mgmt` ADD `drop_wh_id` INT(20) NULL AFTER `vir_wh_id`;

--   UPDATE del_orders SET status = 'Closed',comment="Backend Closed as status is pending from generated" WHERE status = 'Pending';


--   ALTER TABLE `leads_log` ADD `log_order_lead_date` TIMESTAMP NULL AFTER `log_order_type`;

  CREATE TABLE `q5c_dev_intraapp`.`monthly_records` ( `id` INT(20) NOT NULL AUTO_INCREMENT , `total_rent_collected` INT(20) NULL , `total_unit_rented` INT(20) NULL , `total_customer_served` INT(20) NULL , `new_rent_collected` INT(20) NULL , `new_unit_rented` INT(20) NULL , `new_customer` INT(20) NULL , `renewal_rent_collected` INT(20) NULL , `renewal_count_of_equipment` INT(20) NULL , `vdr_payment` INT(20) NULL , `sales_value` INT(20) NULL , `sales_customer` INT(20) NULL , `sales_transport` INT(20) NULL , `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , `created_by` VARCHAR(50) NOT NULL , `updated_at` TIMESTAMP on update CURRENT_TIMESTAMP NULL , `updated_by` VARCHAR(50) NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;



CREATE TRIGGER `monthly_renewal` AFTER INSERT ON `renewals`
 FOR EACH ROW BEGIN
DECLARE month,yr VARCHAR(50);
SELECT MONTH(renewals.pickup_date) INTO Month FROM order_details;
SELECT YEAR(renewals.pickup_date) INTO yr FROM order_details;

UPDATE monthly_records SET monthly_records.total_rent_collected = (monthly_records.total_rent_collected + NEW.cash_amount), monthly_records.total_rent_collected = (monthly_records.total_rent_collected + NEW.online_amount), monthly_records.renewal_rent_collected = (monthly_records.renewal_rent_collected + NEW.cash_amount), monthly_records.renewal_rent_collected = (monthly_records.renewal_rent_collected + NEW.online_amount), monthly_records.renewal_count_of_equipment = (monthly_records.renewal_count_of_equipment + 1), monthly_records.total_unit_rented = (monthly_records.total_unit_rented + 1) WHERE monthly_records.month = Month AND monthly_records.year = yr;
END

CREATE TRIGGER `rent_depo_sale_trans` AFTER INSERT ON `order_details`
 FOR EACH ROW IF(NEW.sale_rental = "Rental") THEN
UPDATE fy_report SET total_depo_collected = (total_depo_collected + NEW.product_deposite),total_rental = (total_rental + NEW.product_rent),transport = (transport + NEW.transport) WHERE fy = '2022-2023';
ELSEIF(NEW.sale_rental = "Sale") THEN
UPDATE fy_report SET sale = (sale + NEW.product_rent),sale_transport = (sale_transport + NEW.transport) WHERE fy = '2022-2023';
END IF

CREATE TRIGGER `depo_refund` AFTER INSERT ON `pickups`
 FOR EACH ROW UPDATE fy_report SET depo_returned = (depo_returned+NEW.cash_amount) WHERE fy='2022-2023'

CREATE TRIGGER `vdr_amount_month` AFTER INSERT ON `order_details`
 FOR EACH ROW BEGIN
DECLARE month,yr,P1 VARCHAR(50);
SELECT MONTH(order_details.creation_date) INTO Month FROM order_details;
SELECT YEAR(order_details.creation_date) INTO yr FROM order_details;

IF (NEW.sale_rental = 'Rental') THEN
SELECT product_rent_approved INTO P1 FROM vendor_products WHERE id = NEW.vendor_product_id;
UPDATE monthly_records SET vdr_payment = (vdr_payment + P1) WHERE month = Month AND year=yr;
END IF;
END

CREATE TRIGGER `sales_customer` AFTER INSERT ON `order_details`
 FOR EACH ROW BEGIN
DECLARE P1 VARCHAR(50);
SELECT count(DISTINCT customer_id) INTO P1 FROM order_details WHERE sale_rental = 'Sale' AND creation_date BETWEEN '2022-05-01' AND '2022-05-30';
UPDATE monthly_records SET total_customer_served_rental = P1 WHERE month = '05' AND year = '2022';
END

CREATE TRIGGER `order_details monthly records` AFTER INSERT ON `order_details`
 FOR EACH ROW BEGIN
DECLARE month,yr VARCHAR(50);
SELECT MONTH(order_details.creation_date) INTO Month FROM order_details;
SELECT YEAR(order_details.creation_date) INTO yr FROM order_details;
IF (NEW.sale_rental = 'Rental')THEN
	UPDATE monthly_records SET total_rent_collected = (total_rent_collected + NEW.product_rent), total_unit_rented = (total_unit_rented + NEW.product_qty), new_rent_collected = (new_rent_collected + NEW.product_rent),new_unit_rented = (new_unit_rented + NEW.product_qty),transportation = (transportation + NEW.transport)WHERE month = Month AND year = yr;
ELSEIF (NEW.sale_rental = 'Sale')THEN
	UPDATE monthly_records SET sales_value = (sales_value + NEW.product_rent),sales_transport = (sales_transport + NEW.transport)WHERE month = Month AND year = yr;
END IF;
END

CREATE TRIGGER `total_customer_count` AFTER INSERT ON `order_details`
 FOR EACH ROW BEGIN
DECLARE P1 VARCHAR(50);
SELECT count(DISTINCT customer_id) INTO P1 FROM order_details WHERE creation_date BETWEEN '2022-05-01' AND '2022-05-30';
UPDATE monthly_records SET total_customer_served_rental = P1 WHERE month = '05' AND year = '2022';
END

CREATE TRIGGER `vdr_month_without_q5c` AFTER INSERT ON `order_details`
 FOR EACH ROW BEGIN
DECLARE month,yr,P1 VARCHAR(50);
SELECT MONTH(order_details.creation_date) INTO Month FROM order_details;
SELECT YEAR(order_details.creation_date) INTO yr FROM order_details;

IF (NEW.sale_rental = 'Rental' AND NEW.vendor_id != 17) THEN
SELECT product_rent_approved INTO P1 FROM vendor_products WHERE id = NEW.vendor_product_id;
UPDATE monthly_records SET vdr_payment = (vdr_payment + P1) WHERE month = Month AND year=yr;
END IF;
END

CREATE TRIGGER `vdr_amount` AFTER INSERT ON `order_details`
 FOR EACH ROW BEGIN
DECLARE P1 VARCHAR(50);
IF (NEW.sale_rental = 'Rental') THEN
SELECT product_rent_approved INTO P1 FROM vendor_products WHERE id = NEW.vendor_product_id;
UPDATE fy_report SET vdr_payment = (vdr_payment + P1) WHERE fy = '2022-2023';
END IF;
END

CREATE TRIGGER `vdr_payment_renewal` AFTER INSERT ON `renewals`
 FOR EACH ROW BEGIN
DECLARE month,yr,P1,P0 VARCHAR(50);
SELECT MONTH(renewals.pickup_date) INTO Month FROM renewals;
SELECT YEAR(renewals.pickup_date) INTO yr FROM renewals;
SELECT order_details.vendor_product_id INTO P0 FROM order_details WHERE order_details.id = NEW.order_details_id;
SELECT product_rent_approved INTO P1 FROM vendor_products WHERE id = P0;
UPDATE monthly_records SET vdr_payment = (vdr_payment + P1) WHERE month = Month AND year=yr;
END

CREATE TRIGGER `vdr_payment_renewal_without_q5c` AFTER INSERT ON `renewals`
 FOR EACH ROW BEGIN
DECLARE month,yr,P1,P0,V1 VARCHAR(50);
SELECT MONTH(renewals.pickup_date) INTO Month FROM renewals;
SELECT YEAR(renewals.pickup_date) INTO yr FROM renewals;
SELECT order_details.vendor_product_id INTO P0 FROM order_details WHERE order_details.id = NEW.order_details_id;
SELECT order_details.vendor_id INTO V1 FROM order_details WHERE order_details.id = NEW.order_details_id;
IF(V1 != 17)THEN
SELECT product_rent_approved INTO P1 FROM vendor_products WHERE id = P0;
UPDATE monthly_records SET vdr_payment = (vdr_payment + P1) WHERE month = Month AND year=yr;
END IF;
END





ALTER TABLE `del_orders` ADD `floor_wise_labour_charges` INT NULL AFTER `order_owner`, ADD `floor_no` INT NULL AFTER `floor_wise_labour_charges`, ADD `labour_charges` INT NULL AFTER `floor_no`;



SELECT sum(cost) FROM google_campain_report WHERE date BETWEEN '2022-07-01' AND '2022-07-31' AND city='Mumbai';
SELECT sum(impr) FROM google_campain_report WHERE date BETWEEN '2022-07-01' AND '2022-07-31' AND city='Mumbai';
SELECT sum(clicks) FROM google_campain_report WHERE date BETWEEN '2022-07-01' AND '2022-07-31' AND city='Mumbai';
SELECT sum(cost) FROM google_campain_report WHERE date BETWEEN '2022-07-01' AND '2022-07-31' AND city='Pune';
SELECT sum(impr) FROM google_campain_report WHERE date BETWEEN '2022-07-01' AND '2022-07-31' AND city='Pune';
SELECT sum(clicks) FROM google_campain_report WHERE date BETWEEN '2022-07-01' AND '2022-07-31' AND city='Pune';


//sarikaa and sheetal said 
UPDATE customer_details SET primary_contact_no = '9820454372' WHERE customer_details.primary_contact_no = '9820425989'
UPDATE del_orders SET mobileno = '9820454372' WHERE del_orders.mobileno = '9820425989'

UPDATE customer_details SET primary_contact_no = '9820149439' WHERE customer_details.primary_contact_no = '9819512724'
UPDATE del_orders SET mobileno = '9820149439' WHERE del_orders.mobileno = '9819512724'

UPDATE customer_details SET customer_name = 'Max Healthcare' WHERE customer_details.customer_name = 'Dr Balabhai Nanavati Hospital';

UPDATE del_orders SET shipping_first_name = 'Max Healthcare' WHERE del_orders.shipping_first_name = 'Dr Balabhai Nanavati Hospital';



SELECT sum(cost) FROM google_campain_report WHERE date BETWEEN '2022-10-01' AND '2022-10-31' AND city='Mumbai';
SELECT sum(impr) FROM google_campain_report WHERE date BETWEEN '2022-10-01' AND '2022-10-31' AND city='Mumbai';
SELECT sum(clicks) FROM google_campain_report WHERE date BETWEEN '2022-10-01' AND '2022-10-31' AND city='Mumbai';

SELECT sum(cost) FROM google_campain_report WHERE date BETWEEN '2022-10-01' AND '2022-10-31' AND city='Pune';
SELECT sum(impr) FROM google_campain_report WHERE date BETWEEN '2022-10-01' AND '2022-10-31' AND city='Pune';
SELECT sum(clicks) FROM google_campain_report WHERE date BETWEEN '2022-10-01' AND '2022-10-31' AND city='Pune';



SELECT sum(cost) FROM google_campain_report WHERE date BETWEEN '2023-05-01' AND '2023-05-31' AND city='Mumbai';
SELECT sum(impr) FROM google_campain_report WHERE date BETWEEN '2023-05-01' AND '2023-05-31' AND city='Mumbai';
SELECT sum(clicks) FROM google_campain_report WHERE date BETWEEN '2023-05-01' AND '2023-05-31' AND city='Mumbai';


-- opening closing balances
CREATE TABLE `q5c_prod_intraapp`.`opening_closing_balances` ( `id` INT NOT NULL , `date` DATE NOT NULL , `opening_balance_ptcash` INT NOT NULL , `closing_balance_ptcash` INT NOT NULL , `opening_balance_cust_cash` INT NOT NULL , `closing_balance_cust_cash` INT NOT NULL , `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , `created_by` VARCHAR(50) NOT NULL , `updated_at` TIMESTAMP on update CURRENT_TIMESTAMP NULL , `updated_by` VARCHAR NULL ) ENGINE = InnoDB;





-- IN Devweb....
CREATE TABLE `q5c_dev_intraapp`.`hospitalleads` ( `id` INT(20) NOT NULL AUTO_INCREMENT , `admissiondate` DATE NOT NULL , `dischargedate` DATE NOT NULL , `patientname` VARCHAR(100) NOT NULL , `age` INT(5) NOT NULL , `gender` VARCHAR(10) NOT NULL , `city` VARCHAR(50) NOT NULL , `doctorname` VARCHAR(50) NOT NULL , `contactnumber` BIGINT(10) NOT NULL , `wardname` VARCHAR(50) NOT NULL , `admissionstatus` VARCHAR(150) NOT NULL , `dischargestatus` VARCHAR(150) NOT NULL , `leadtaken` INT(20) NULL DEFAULT NULL , `remark` VARCHAR(250) NULL DEFAULT NULL , `leadstatus` VARCHAR(50) NOT NULL DEFAULT 'Pending' , `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , `created_by` VARCHAR(50) NOT NULL , `updated_at` TIMESTAMP on update CURRENT_TIMESTAMP NULL DEFAULT NULL , `updated_by` VARCHAR(50) NULL DEFAULT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;
CREATE TABLE `q5c_dev_intraapp`.`doctormaster` ( `id` INT(20) NOT NULL AUTO_INCREMENT , `doctorname` VARCHAR(100) NOT NULL , `speciality` VARCHAR(100) NOT NULL , `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , `created_by` VARCHAR(50) NOT NULL , `updated_at` TIMESTAMP on update CURRENT_TIMESTAMP NULL DEFAULT NULL , `updated_by` VARCHAR(50) NULL DEFAULT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;

CREATE TABLE `q5c_dev_intraapp`.`vdr_inv_mgmt` ( `id` INT NOT NULL , `vendor_id` INT NOT NULL , `product_id` INT NOT NULL , `order_details_id` INT NOT NULL , `rented_on` INT NOT NULL , `picked_up_on` INT NOT NULL , `rent` INT NOT NULL , `created_at` INT NOT NULL , `created_by` INT NOT NULL , `updated_at` INT NOT NULL , `updated_by` INT NOT NULL , `order_id` INT NOT NULL ) ENGINE = InnoDB;

-- Vendor Inventory Auto
CREATE TABLE `q5c_dev_intraapp`.`vendor_inventory_auto` ( `id` INT NOT NULL AUTO_INCREMENT , `order_details_id` INT NOT NULL , `order_type` ENUM('delivery','renewal') NOT NULL , `inventory_no` VARCHAR(50) NULL DEFAULT NULL , `invoice_no` VARCHAR(50) NULL DEFAULT NULL , `invoice_status` ENUM('pending','verified') NOT NULL , `verified_at` TIMESTAMP NULL DEFAULT NULL , `verified_by` VARCHAR(50) NOT NULL , `payment_state` ENUM('pending','partial paid','paid') NOT NULL , `payment_image` VARCHAR(100) NOT NULL , `flag` ENUM('active','inactive') NOT NULL , `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , `created_by` VARCHAR(50) NOT NULL , `updated_at` TIMESTAMP on update CURRENT_TIMESTAMP NULL DEFAULT NULL , `updated_by` VARCHAR(50) NULL DEFAULT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;


SELECT sum(cost) FROM google_campain_report WHERE date BETWEEN '2023-07-01' AND '2023-07-31' AND city='Mumbai';
SELECT sum(impr) FROM google_campain_report WHERE date BETWEEN '2023-07-01' AND '2023-07-31' AND city='Mumbai';
SELECT sum(clicks) FROM google_campain_report WHERE date BETWEEN '2023-07-01' AND '2023-07-31' AND city='Mumbai';

SELECT sum(cost) FROM google_campain_report WHERE date BETWEEN '2023-07-01' AND '2023-07-31' AND city='Pune';
SELECT sum(impr) FROM google_campain_report WHERE date BETWEEN '2023-07-01' AND '2023-07-31' AND city='Pune';
SELECT sum(clicks) FROM google_campain_report WHERE date BETWEEN '2023-07-01' AND '2023-07-31' AND city='Pune';





-- Corporate Renewals
CREATE TABLE `q5c_dev_intraapp`.`corporate_renewal` ( `id` INT NOT NULL AUTO_INCREMENT , `order_details_id` INT NOT NULL , `start_date` DATE NOT NULL , `end_date` DATE NOT NULL , `amount` INT NULL , `invoice_no` VARCHAR NULL , `invoice_date` DATE NULL , `flag` VARCHAR NOT NULL DEFAULT 'Active' , `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , `created_by` VARCHAR NOT NULL , `updated_at` TIMESTAMP on update CURRENT_TIMESTAMP NULL , `updated_by` VARCHAR NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;

SELECT * FROM renewals WHERE status = 'Online Renewed' AND payment_mode = 'Cash'

UPDATE renewals SET online_amount = cash_amount, payment_mode = 'Online' WHERE status = 'Online Renewed' AND payment_mode = 'Cash'


