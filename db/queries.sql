CREATE TABLE `product_favourites` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `status` enum('AC','IN','DL') NOT NULL DEFAULT 'AC',
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `product_favourites`
--
ALTER TABLE `product_favourites`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `product_favourites`
--
ALTER TABLE `product_favourites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


---------------------------------------------------------------------

INSERT INTO `g_roles` (`id`, `role`, `name`, `status`, `created_at`, `updated_at`) VALUES (NULL, 'driver', 'Driver', 'AC', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

ALTER TABLE `product_variations` CHANGE `price` `price` DECIMAL(11,2) NOT NULL, CHANGE `special_price` `special_price` DECIMAL(11,2) NULL DEFAULT '0';

ALTER TABLE `c_cart_items` CHANGE `price` `price` DECIMAL(11,2) NOT NULL, CHANGE `special_price` `special_price` DECIMAL(11,2) NOT NULL DEFAULT '0';

ALTER TABLE `order_items` CHANGE `price` `price` DECIMAL(11,2) NOT NULL, CHANGE `special_price` `special_price` DECIMAL(11,2) NOT NULL DEFAULT '0';

ALTER TABLE `order_drivers` CHANGE `status` `status` ENUM('PN','CM','DL','UN') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'PN';

ALTER TABLE `order_drivers` ADD `reason` TINYTEXT NULL DEFAULT NULL AFTER `status`;