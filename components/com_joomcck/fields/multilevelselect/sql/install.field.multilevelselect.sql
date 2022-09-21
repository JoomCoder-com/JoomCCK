CREATE TABLE IF NOT EXISTS `#__js_res_field_multilevelselect` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `field_id` int(11) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `level` int(11) DEFAULT NULL,
  `lft` int(11) DEFAULT NULL,
  `rgt` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_field` (`field_id`),
  KEY `idx_lr` (`lft`,`rgt`),
  KEY `idx_parent` (`parent_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DELETE FROM `#__js_res_field_multilevelselect` WHERE id = 1;

INSERT INTO `#__js_res_field_multilevelselect` VALUES (1, 'root', 0, 0, 0, 1, 2);