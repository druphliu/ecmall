ALTER TABLE `ecm_store`
ADD COLUMN `sell_area`  varchar(15) NULL AFTER `region_name`;
ALTER TABLE `ecm_store`
ADD COLUMN `folder`  varchar(30) NULL AFTER `enable_radar`;
ALTER TABLE `ecm_coupon`
DROP COLUMN `use_times`;
ALTER TABLE `ecm_coupon_sn`
DROP COLUMN `remain_times`;
ALTER TABLE `ecm_coupon_sn`
ADD COLUMN `is_activity`  tinyint(1) ZEROFILL NULL DEFAULT 0 AFTER `coupon_id`;
ALTER TABLE `ecm_user_coupon`
ADD UNIQUE INDEX `coupon_sn` (`coupon_sn`) USING BTREE ,
ADD UNIQUE INDEX `user_id` (`user_id`) USING BTREE ;
ALTER TABLE `ecm_user_coupon`
ADD COLUMN `is_used`  tinyint(1) ZEROFILL NULL DEFAULT 0 AFTER `coupon_sn`;
ALTER TABLE `ecm_coupon`
ADD COLUMN `use_area`  varchar(100) NULL AFTER `if_issue`;
ALTER TABLE `ecm_coupon`
ADD COLUMN `use_area_name`  varchar(300) NULL AFTER `use_area`;
ALTER TABLE `ecm_user_coupon`
ADD COLUMN `coupon_id`  int(10) NOT NULL AFTER `user_id`;
ALTER TABLE `ecm_coupon_sn`
CHANGE COLUMN `is_activity` `user_id`  int(10) UNSIGNED ZEROFILL NULL DEFAULT 0 AFTER `coupon_id`;
ALTER TABLE `ecm_coupon_sn`
ADD COLUMN `is_used`  tinyint(1) ZEROFILL NOT NULL DEFAULT 0 AFTER `user_id`;
ALTER TABLE `ecm_brand`
ADD COLUMN `brand_desc`  varchar(300) NOT NULL AFTER `brand_name`;
ALTER TABLE `ecm_goods`
ADD COLUMN `morning_start`  varchar(5) NOT NULL AFTER `tags`,
ADD COLUMN `morning_end`  varchar(5) NOT NULL AFTER `morning_start`,
ADD COLUMN `afternoon_start`  varchar(5) NOT NULL AFTER `morning_end`,
ADD COLUMN `afternoon_end`  varchar(5) NOT NULL AFTER `afternoon_start`;




