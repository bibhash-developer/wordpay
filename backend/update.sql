/** NOT ON SERVER **/
ALTER TABLE `users`
ADD `test_balance_coins` DOUBLE
(10,2) NOT NULL DEFAULT '1000.00' AFTER `balance_coins`;








ALTER TABLE `articles`
ADD `total_revenue_with_vat` DOUBLE
(10,2) NOT NULL DEFAULT '0.00' AFTER `total_revenue`;


/** NOT ON SERVER **/
ALTER TABLE `articles` CHANGE `signups` `purchased` INT
(11) NOT NULL DEFAULT '0';
ALTER TABLE `article_transactions`
ADD `payout_id` INT
(11) NULL DEFAULT NULL AFTER `coins_balance`,
ADD `payout_date` TIMESTAMP NULL DEFAULT NULL AFTER `payout_id`;
ALTER TABLE `articles` CHANGE `fixed_coins` `fixed_coins` DOUBLE
(10,2) NOT NULL DEFAULT '0.00';
ALTER TABLE `articles`
ADD `total_revenue` DOUBLE
(10,2) NULL DEFAULT '0.00' AFTER `purchased`;
ALTER TABLE `user_coin_transactions`
ADD `transaction_mode` ENUM
('DR','CR') NULL DEFAULT NULL AFTER `balance_coins`;
UPDATE `user_coin_transactions`
SET
`transaction_mode` = 'CR';

ALTER TABLE `user_coin_transactions` CHANGE `package_id` `package_article_id` INT
(11) NOT NULL;



CREATE TABLE `article_payout`
(
  `payout_id` int
(11) NOT NULL,
  `company_id` int
(11) NOT NULL,
  `description` text,
  `no_of_articles` int
(11) DEFAULT NULL,
  `paid_amount` double
(10,2) NOT NULL DEFAULT '0.00',
  `total_paid_coins` double
(10,2) NOT NULL DEFAULT '0.00',
  `payout_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `article_payout`
ADD PRIMARY KEY
(`payout_id`);
ALTER TABLE `article_payout` MODIFY `payout_id` int
(11) NOT NULL AUTO_INCREMENT;









/** ON SERVER **/
ALTER TABLE `users`
ADD `social_type` ENUM
('google','facebook') NULL DEFAULT NULL AFTER `balance_coins`,
ADD `social_id` VARCHAR
(255) NULL DEFAULT NULL AFTER `social_type`;
ALTER TABLE `users` CHANGE `password` `password` VARCHAR
(191) CHARACTER
SET utf8mb4
COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;

ALTER TABLE `users`
ADD `status` TINYINT
(1) NULL DEFAULT '0' AFTER `remember_token`;
ALTER TABLE `users` CHANGE `status` `activation_code` VARCHAR
(255) NULL DEFAULT NULL;

ALTER TABLE `articles` CHANGE `wordpress_article_type` `article_type` VARCHAR
(191) CHARACTER
SET utf8mb4
COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `wordpress_title` `title` VARCHAR
(255) CHARACTER
SET utf8mb4
COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `wordpress_post_id` `post_id` INT
(11) NOT NULL, CHANGE `wordpress_post_url` `post_url` VARCHAR
(191) CHARACTER
SET utf8mb4
COLLATE utf8mb4_unicode_ci NOT NULL, CHANGE `wordpress_max_price` `max_price` DOUBLE
(10,2) NOT NULL, CHANGE `wordpress_min_price` `min_price` DOUBLE
(10,2) NOT NULL, CHANGE `wordpress_fixed_coins` `fixed_coins` INT
(11) NOT NULL;
ALTER TABLE `article_transactions` CHANGE `wordpress_article_type` `article_type` VARCHAR
(191) CHARACTER
SET utf8mb4
COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `wordpress_title` `title` VARCHAR
(255) CHARACTER
SET utf8mb4
COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `wordpress_post_id` `post_id` INT
(11) NOT NULL, CHANGE `wordpress_post_url` `post_url` VARCHAR
(191) CHARACTER
SET utf8mb4
COLLATE utf8mb4_unicode_ci NOT NULL, CHANGE `wordpress_max_price` `max_price` DOUBLE
(10,2) NOT NULL, CHANGE `wordpress_min_price` `min_price` DOUBLE
(10,2) NOT NULL;


ALTER TABLE `companies`
ADD `revenue_per_coin` DOUBLE
(10,2) NOT NULL DEFAULT '0.00' AFTER `phone`;

CREATE TABLE `company_payout`
(
  `pay_id` int
(11) NOT NULL,
  `company_id` int
(11) NOT NULL,
  `revenue_per_coin` double
(10,2) NOT NULL DEFAULT '0.00',
  `description` text,
  `no_of_articles` int
(11) DEFAULT '0',
  `paid_amount` double
(10,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `company_payout`
ADD PRIMARY KEY
(`pay_id`);

ALTER TABLE `company_payout` MODIFY `pay_id` int
(11) NOT NULL AUTO_INCREMENT;


ALTER TABLE `packages` CHANGE `currency_id` `country_id` INT
(11) NOT NULL COMMENT 'for currency';
