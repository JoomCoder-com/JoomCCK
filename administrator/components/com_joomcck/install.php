<?php

/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') || die();



class com_joomcckInstallerScript
{
    public function install($parent)
    {
        $this->_updateTables(false);
        $this->_createLink();

        return true;
    }

    public function uninstall($parent)
    {
        $db = \Joomla\CMS\Factory::getDbo();
        $db->setQuery("SHOW TABLES lIKE '%_js_res_%'");
        $list = $db->loadColumn();

        foreach ($list as $table) {
            $db->setQuery("DROP TABLE IF EXISTS `{$table}`");
            $db->execute();
        }
    }

    public function update($parent)
    {

	    include_once JPATH_ROOT.'/components/com_joomcck/api.php';

        $this->_deleteFiles();
        $this->_updateTables();
        //$this->_joomcck();
        $this->_createLink();

        return true;
    }

    public function preflight($type, $parent)
    {}

    public function postflight($type, $parent)
    {}

    private function _createLink()
    {
        $db = \Joomla\CMS\Factory::getDbo();

        $sql = "SELECT menutype FROM `#__menu_types` ORDER BY id ASC";
        $db->setQuery($sql);
        $menu_type = $db->loadResult();

        $et = \Joomla\CMS\Table\Table::getInstance('Extension', 'JTable');
        $et->load([
            "name"    => 'com_joomcck',
            "type"    => 'component',
            "element" => 'com_joomcck'
        ]);

        if (!$et->extension_id) {
            return;
        }

        $params = json_decode(($et->params ?: '{}'));
        if (empty($params->moderator)) {
            $params->moderator = \Joomla\CMS\Factory::getApplication()->getIdentity()->get('id');
        }
        if (empty($params->general_upload)) {
            $params->general_upload = 'uploads';
        }
        $et->params = json_encode($params);
        $et->store();

        \Joomla\CMS\Table\Table::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_menus/tables');
        $menu_table = \Joomla\CMS\Table\Table::getInstance('Menu', 'JTable', []);

        $menu_table->load([
            "link"         => 'index.php?option=com_joomcck&view=cpanel',
            "type"         => 'component',
            "component_id" => $et->extension_id
        ]);

        $sql = "SELECT id FROM `#__usergroups` WHERE title = 'Super Users'";
        $db->setQuery($sql);
        $access = $db->loadResult();

        if (!$menu_table->id) {
            $menu_table->save([
                "title"        => "Joomcck Admin Dashboard",
                "alias"        => "joomcck-cp",
                "menutype"     => $menu_type,
                "path"         => "joomcck-cp",
                "link"         => "index.php?option=com_joomcck&view=cpanel",
                "type"         => "component",
                "published"    => 1,
				'language'     => '*',
                "level"        => 1,
                "parent_id"    => 1,
                "component_id" => $et->extension_id,
                "access"       => 6, // super users
                "client_id"    => 0,
                "params"       => '{"menu-anchor_title":"","menu-anchor_css":"","menu_image":"","menu_image_css":"","menu_text":1,"menu_show":1,"page_title":"","show_page_heading":"","page_heading":"","pageclass_sfx":"","menu-meta_description":"","menu-meta_keywords":"","robots":"","secure":0}'
            ]);
		}
		
		$menu_table->level = 1;
		$menu_table->parent_id = 1;
		$menu_table->published = 1;
		$menu_table->store();
    }
    private function _get_default($field)
    {
        $db      = \Joomla\CMS\Factory::getDbo();
        $default = 'DEFAULT ' . $db->q($field->default);

        if ($field->default === null && $field->null == 'YES') {
            $default = 'DEFAULT NULL';
        }

        if (in_array(strtoupper($field->type), ["TINYBLOB", "BLOB", "MEDIUMBLOB", "LONGBLOB", "TEXT", "TINYTEXT", "MEDIUMTEXT", "LONGTEXT"])) {
            $default = null;
        }

        return $default;
    }

    private function _joomcck()
    {
        define('CDIR', JPATH_ROOT . '/administrator/components/com_joomcck/');

        if (is_dir(CDIR . 'controllers')) {
            \Joomla\Filesystem\Folder::delete(CDIR . 'controllers');
        }

        if (is_dir(CDIR . 'helpers')) {
            \Joomla\Filesystem\Folder::delete(CDIR . 'helpers');
        }

        if (is_dir(CDIR . 'tables')) {
            \Joomla\Filesystem\Folder::delete(CDIR . 'tables');
        }

        if (is_dir(CDIR . 'models')) {
            \Joomla\Filesystem\Folder::delete(CDIR . 'models');
        }

        if (is_dir(CDIR . 'xml')) {
            \Joomla\Filesystem\Folder::delete(CDIR . 'xml');
        }

        if (is_dir(CDIR . 'library/css')) {
            \Joomla\Filesystem\Folder::delete(CDIR . 'library/css');
        }

        if (is_dir(CDIR . 'library/js')) {
            \Joomla\Filesystem\Folder::delete(CDIR . 'library/js');
        }

        if (is_dir(CDIR . 'library/php')) {
            \Joomla\Filesystem\Folder::delete(CDIR . 'library/php');
        }

    }

    /**
     *
     * @param  bool|TRUE   $update
     * @throws Exception
     */
    public function _updateTables($update = true)
    {
        $prefix = \Joomla\CMS\Factory::getApplication()->getCfg('dbprefix');
        $db     = \Joomla\CMS\Factory::getDbo();

        $db->setQuery("SHOW TABLES lIKE '%_js_res_%'");
        $list = $db->loadColumn();

        $tables = \Joomla\Filesystem\Folder::files(JPATH_ROOT . '/administrator/components/com_joomcck/library/db', '\.json$');


        foreach ($tables as $file) {
            if (substr($file, 0, 6) == 'h0o6u_') {
                continue;
            }

            $table  = $prefix . str_replace('.json', '', $file);
            $source = json_decode(file_get_contents(JPATH_ROOT . '/administrator/components/com_joomcck/library/db/' . $file));

            if (in_array($table, $list)) {
                $db->setQuery("DESCRIBE `{$table}`");
                $info = $db->loadObjectList('Field');

                $all_fields  = [];
                $real_fields = array_keys($info);

                foreach ($source->fields as $field) {
                    if ($field->name == 'id' && $file != 'js_res_country.json') {
                        continue;
                    }
                    $all_fields[] = $field->name;

                    $update = sprintf(
                        "`%s` %s %s %s COMMENT '%s'",
                        $field->name,
                        strtoupper($field->type),
                        ($field->null == 'YES' ? 'NULL' : 'NOT NULL'),
                        $this->_get_default($field),
                        @$field->comment
                    );

                    $sql = null;

                    if (empty($info[$field->name])) {
                        $sql = "ALTER TABLE `{$table}` ADD COLUMN " . $update;
                    } elseif (
                        strtolower($info[$field->name]->Type) != strtolower($field->type) ||
                        strtolower($info[$field->name]->Null) != strtolower($field->null) ||
                        $info[$field->name]->Default != $field->default
                    ) {
                        $sql = "ALTER TABLE `{$table}` CHANGE COLUMN `{$field->name}` " . $update;
                    }



                    if ($sql) {
                        if ($update) {
                            \Joomla\CMS\Factory::getApplication()->enqueueMessage("Table update: " . $sql);
                        }

                        $db->setQuery($sql);
                        $db->execute();
                    }
                }


                if (!empty($source->primary)) {
                    $all_fields[] = $source->primary;
                }

                $diff = array_diff($real_fields, $all_fields);

                if (!empty($diff)) {
                    $sql = sprintf("ALTER TABLE `%s` DROP COLUMN `%s`", $table, implode("`, DROP COLUMN `", $diff));
                    $db->setQuery($sql);
                    $db->execute();
                    if ($update) {
                        \Joomla\CMS\Factory::getApplication()->enqueueMessage("Column drop: " . $sql);
                    }

                }

                $db->setQuery("SHOW INDEXES FROM `{$table}`");
                $keys = $db->loadObjectList('Key_name');

                $all_keys  = [];
                $real_keys = array_keys($keys);

                if (!empty($source->keys)) {
                    foreach ($source->keys as $key_name => $key) {
                        $all_keys[] = $key_name;

                        if (empty($keys[$key_name])) {
                            $array = explode(",", $key->fields);

                            $sql = sprintf(
                                "ALTER TABLE `%s` ADD INDEX `%s` (%s ASC)",
                                $table,
                                $key_name,
                                implode(" ASC, ", $array)
                            );
                            $db->setQuery($sql);
                            $db->execute();
                            if ($update) {
                                \Joomla\CMS\Factory::getApplication()->enqueueMessage("Index update: " . $sql);
                            }

                        }
                    }
                }

                if (!empty($source->primary)) {
                    $all_keys[] = 'PRIMARY';
                }

                $diff = array_diff($real_keys, $all_keys);

                if (!empty($diff) && $file != 'js_res_country.json') {
                    $sql = sprintf("ALTER TABLE `%s` DROP INDEX %s", $table, implode(", DROP INDEX ", $diff));
                    $db->setQuery($sql);
                    $db->execute();
                    if ($update) {
                        \Joomla\CMS\Factory::getApplication()->enqueueMessage("Index drop: " . $sql);
                    }

                }

                $db->setQuery("SHOW TABLE STATUS WHERE Name = '{$table}'");
                $engine = $db->loadObject()->Engine;

                if ($engine != $source->engine) {
                    $db->setQuery("ALTER TABLE `{$table}` ENGINE = {$source->engine}");
                    $db->execute();
                    if ($update) {
                        \Joomla\CMS\Factory::getApplication()->enqueueMessage("Engine changed: " . $source->engine);
                    }

                }
            } else {

                $sql = [];

                if (!empty($source->primary)) {
                    $sql[] = "`{$source->primary}` int(10) unsigned NOT NULL AUTO_INCREMENT";
                }
                foreach ($source->fields as $field) {
                    if (@$source->primary == $field->name) {
                        continue;
                    }
                    $sql[] = sprintf(
                        "`%s` %s %s %s COMMENT '%s'",
                        $field->name,
                        strtoupper($field->type),
                        ($field->null == 'YES' ? 'NULL' : 'NOT NULL'),
                        $this->_get_default($field),
                        @$field->comment
                    );
                }

                if (!empty($source->primary)) {
                    $sql[] = "PRIMARY KEY (`{$source->primary}`)";
                }

                if (!empty($source->keys)) {
                    foreach ($source->keys as $key_name => $key) {
                        $sql[] = sprintf(
                            "%s `%s` (%s)",
                            strtoupper($key->type),
                            $key_name,
                            $key->fields
                        );
                    }
                }

                $query = sprintf(
                    "CREATE TABLE IF NOT EXISTS `%s` (%s) ENGINE=%s  DEFAULT CHARSET=utf8;",
                    $table,
                    implode(",  ", $sql),
                    $source->engine
                );

                $db->setQuery($query);
                $db->execute();
                if ($update && $file != 'js_ip_2_country.json') {
                    \Joomla\CMS\Factory::getApplication()->enqueueMessage("Table created: " . $query);
                }

            }

            $db->setQuery("OPTIMIZE TABLE {$table}");
            $db->execute();
        }

        $db->setQuery("SELECT id FROM `#__js_res_categories` WHERE id = 1");
        if (!$db->loadResult()) {
            $db->setQuery("INSERT INTO `#__js_res_categories` VALUES ('1', '0', '0', '0', '1', '0', '', '0', 'ROOT', 'root', '', '', '', '0', '0', NULL, '0', '{}', ' ', ' ', ' ', '0', NULL, '0', NULL, '0', '*', '0', '0', '{}')");
            $db->execute();
        }

        $db->setQuery("SELECT id FROM `#__js_res_comments` WHERE id = 1");
        if (!$db->loadResult()) {
            $db->setQuery("INSERT INTO `#__js_res_comments` VALUES ('1', '0', '0', ' ', NULL, '1', '', '', 'en-GB', '0', '0', '0', '0', '0', '0', ' ', ' ', '0', '', '0', '1', '0', '0', '0', '0')");

            $db->execute();
        }
        $db->setQuery("UPDATE `#__js_res_comments` SET published = 1 WHERE id = 1")->execute();

        $db->setQuery("SELECT id FROM `#__js_res_field_multilevelselect` WHERE id = 1");
        if (!$db->loadResult()) {
            $db->setQuery("INSERT INTO `#__js_res_field_multilevelselect` VALUES (1, 'root', 0, 0, 0, 1, 2)");
            $db->execute();
        }

        $db->setQuery('DELETE FROM `#__js_res_country`');
        $db->execute();

        $db->setQuery("INSERT INTO `#__js_res_country` (`id`, `name`) VALUES ('AD', 'Andorra'), ('AE', 'United Arab Emirates'),
		 ('AF', 'Afghanistan'), ('AG', 'Antigua and Barbuda'), ('AI', 'Anguilla'), ('AL', 'Albania'), ('AM', 'Armenia'),
		 ('AN', 'Netherlands Antilles'), ('AO', 'Angola'), ('AQ', 'Antarctica'), ('AR', 'Argentina'), ('AS', 'American Samoa'),
		 ('AT', 'Austria'), ('AU', 'Australia'), ('AW', 'Aruba'), ('AX', 'Åland Islands'), ('AZ', 'Azerbaijan'),
		 ('BA', 'Bosnia and Herzegovina'), ('BB', 'Barbados'), ('BD', 'Bangladesh'), ('BE', 'Belgium'), ('BF', 'Burkina Faso'),
		 ('BG', 'Bulgaria'), ('BH', 'Bahrain'), ('BI', 'Burundi'), ('BJ', 'Benin'), ('BL', 'Saint Barthélemy'), ('BM', 'Bermuda'),
		 ('BN', 'Brunei'), ('BO', 'Bolivia'), ('BQ', 'British Antarctic Territory'), ('BR', 'Brazil'), ('BS', 'Bahamas'),
		 ('BT', 'Bhutan'), ('BV', 'Bouvet Island'), ('BW', 'Botswana'), ('BY', 'Belarus'), ('BZ', 'Belize'), ('CA', 'Canada'),
		 ('CC', 'Cocos [Keeling] Islands'), ('CD', 'Congo - Kinshasa'), ('CF', 'Central African Republic'), ('CG', 'Congo - Brazzaville'),
		 ('CH', 'Switzerland'), ('CI', 'Côte d’Ivoire'), ('CK', 'Cook Islands'), ('CL', 'Chile'), ('CM', 'Cameroon'), ('CN', 'China'),
		 ('CO', 'Colombia'), ('CR', 'Costa Rica'), ('CS', 'Serbia and Montenegro'), ('CT', 'Canton and Enderbury Islands'), ('CU', 'Cuba'),
		 ('CV', 'Cape Verde'), ('CX', 'Christmas Island'), ('CY', 'Cyprus'), ('CZ', 'Czech Republic'), ('DD', 'East Germany'), ('DE', 'Germany'),
		 ('DJ', 'Djibouti'), ('DK', 'Denmark'), ('DM', 'Dominica'), ('DO', 'Dominican Republic'), ('DZ', 'Algeria'), ('EC', 'Ecuador'),
		 ('EE', 'Estonia'), ('EG', 'Egypt'), ('EH', 'Western Sahara'), ('ER', 'Eritrea'), ('ES', 'Spain'), ('ET', 'Ethiopia'),
		 ('FI', 'Finland'), ('FJ', 'Fiji'), ('FK', 'Falkland Islands'), ('FM', 'Micronesia'), ('FO', 'Faroe Islands'),
		 ('FQ', 'French Southern and Antarctic Territories'), ('FR', 'France'), ('FX', 'Metropolitan France'), ('GA', 'Gabon'),
		 ('GB', 'United Kingdom'), ('GD', 'Grenada'), ('GE', 'Georgia'), ('GF', 'French Guiana'), ('GG', 'Guernsey'), ('GH', 'Ghana'),
		 ('GI', 'Gibraltar'), ('GL', 'Greenland'), ('GM', 'Gambia'), ('GN', 'Guinea'), ('GP', 'Guadeloupe'), ('GQ', 'Equatorial Guinea'),
		 ('GR', 'Greece'), ('GS', 'South Georgia and the South Sandwich Islands'), ('GT', 'Guatemala'), ('GU', 'Guam'), ('GW', 'Guinea-Bissau'),
		 ('GY', 'Guyana'), ('HK', 'Hong Kong SAR China'), ('HM', 'Heard Island and McDonald Islands'), ('HN', 'Honduras'), ('HR', 'Croatia'),
		 ('HT', 'Haiti'), ('HU', 'Hungary'), ('ID', 'Indonesia'), ('IE', 'Ireland'), ('IL', 'Israel'), ('IM', 'Isle of Man'),
		 ('IN', 'India'), ('IO', 'British Indian Ocean Territory'), ('IQ', 'Iraq'), ('IR', 'Iran'), ('IS', 'Iceland'), ('IT', 'Italy'),
		 ('JE', 'Jersey'), ('JM', 'Jamaica'), ('JO', 'Jordan'), ('JP', 'Japan'), ('JT', 'Johnston Island'), ('KE', 'Kenya'),
		 ('KG', 'Kyrgyzstan'), ('KH', 'Cambodia'), ('KI', 'Kiribati'), ('KM', 'Comoros'), ('KN', 'Saint Kitts and Nevis'),
		 ('KP', 'North Korea'), ('KR', 'South Korea'), ('KW', 'Kuwait'), ('KY', 'Cayman Islands'), ('KZ', 'Kazakhstan'),
		 ('LA', 'Laos'), ('LB', 'Lebanon'), ('LC', 'Saint Lucia'), ('LI', 'Liechtenstein'), ('LK', 'Sri Lanka'), ('LR', 'Liberia'),
		 ('LS', 'Lesotho'), ('LT', 'Lithuania'), ('LU', 'Luxembourg'), ('LV', 'Latvia'), ('LY', 'Libya'), ('MA', 'Morocco'),
		 ('MC', 'Monaco'), ('MD', 'Moldova'), ('ME', 'Montenegro'), ('MF', 'Saint Martin'), ('MG', 'Madagascar'), ('MH', 'Marshall Islands'),
		 ('MI', 'Midway Islands'), ('MK', 'Macedonia'), ('ML', 'Mali'), ('MM', 'Myanmar [Burma]'), ('MN', 'Mongolia'), ('MO', 'Macau SAR China'),
		 ('MP', 'Northern Mariana Islands'), ('MQ', 'Martinique'), ('MR', 'Mauritania'), ('MS', 'Montserrat'), ('MT', 'Malta'), ('MU', 'Mauritius'),
		 ('MV', 'Maldives'), ('MW', 'Malawi'), ('MX', 'Mexico'), ('MY', 'Malaysia'), ('MZ', 'Mozambique'), ('NA', 'Namibia'),
		 ('NC', 'New Caledonia'), ('NE', 'Niger'), ('NF', 'Norfolk Island'), ('NG', 'Nigeria'), ('NI', 'Nicaragua'), ('NL', 'Netherlands'),
		 ('NO', 'Norway'), ('NP', 'Nepal'), ('NQ', 'Dronning Maud Land'), ('NR', 'Nauru'), ('NT', 'Neutral Zone'), ('NU', 'Niue'),
		 ('NZ', 'New Zealand'), ('OM', 'Oman'), ('PA', 'Panama'), ('PC', 'Pacific Islands Trust Territory'), ('PE', 'Peru'),
		 ('PF', 'French Polynesia'), ('PG', 'Papua New Guinea'), ('PH', 'Philippines'), ('PK', 'Pakistan'), ('PL', 'Poland'),
		 ('PM', 'Saint Pierre and Miquelon'), ('PN', 'Pitcairn Islands'), ('PR', 'Puerto Rico'), ('PS', 'Palestinian Territories'),
		 ('PT', 'Portugal'), ('PU', 'U.S. Miscellaneous Pacific Islands'), ('PW', 'Palau'), ('PY', 'Paraguay'), ('PZ', 'Panama Canal Zone'),
		 ('QA', 'Qatar'), ('RE', 'Reunion'), ('RO', 'Romania'), ('RS', 'Serbia'), ('RU', 'Russia'), ('RW', 'Rwanda'), ('SA', 'Saudi Arabia'),
		 ('SB', 'Solomon Islands'), ('SC', 'Seychelles'), ('SD', 'Sudan'), ('SE', 'Sweden'), ('SG', 'Singapore'), ('SH', 'Saint Helena'),
		 ('SI', 'Slovenia'), ('SJ', 'Svalbard and Jan Mayen'), ('SK', 'Slovakia'), ('SL', 'Sierra Leone'), ('SM', 'San Marino'),
		 ('SN', 'Senegal'), ('SO', 'Somalia'), ('SR', 'Suriname'), ('ST', 'São Tomé and Príncipe'), ('SU', 'Union of Soviet Socialist Republics'),
		 ('SV', 'El Salvador'), ('SY', 'Syria'), ('SZ', 'Swaziland'), ('TC', 'Turks and Caicos Islands'), ('TD', 'Chad'),
		 ('TF', 'French Southern Territories'), ('TG', 'Togo'), ('TH', 'Thailand'), ('TJ', 'Tajikistan'), ('TK', 'Tokelau'),
		 ('TL', 'Timor-Leste'), ('TM', 'Turkmenistan'), ('TN', 'Tunisia'), ('TO', 'Tonga'), ('TR', 'Turkey'), ('TT', 'Trinidad and Tobago'),
		 ('TV', 'Tuvalu'), ('TW', 'Taiwan'), ('TZ', 'Tanzania'), ('UA', 'Ukraine'), ('UG', 'Uganda'), ('UM', 'U.S. Minor Outlying Islands'),
		 ('US', 'United States'), ('UY', 'Uruguay'), ('UZ', 'Uzbekistan'), ('VA', 'Vatican City'), ('VC', 'Saint Vincent and the Grenadines'),
		 ('VD', 'North Vietnam'), ('VE', 'Venezuela'), ('VG', 'British Virgin Islands'), ('VI', 'U.S. Virgin Islands'), ('VN', 'Vietnam'),
		 ('VU', 'Vanuatu'), ('WF', 'Wallis and Futuna'), ('WK', 'Wake Island'), ('WS', 'Samoa'), ('YD', 'People\'s Democratic Republic of Yemen'),
		 ('YE', 'Yemen'), ('YT', 'Mayotte'), ('ZA', 'South Africa'), ('ZM', 'Zambia'), ('ZW', 'Zimbabwe'), ('ZZ', 'UNKNOWN OR Invalid Region');");
        $db->execute();

        $db->setQuery('DELETE FROM `#__js_res_field_telephone`');
        $db->execute();

        $db->setQuery("INSERT INTO `#__js_res_field_telephone` VALUES (1,'Afghanistan','AF','AFG','93'), (2,'Albania','AL','ALB','355'),
		(3,'Algeria','DZ','DZA','213'), (4,'American Samoa','AS','ASM','1-684'), (5,'Andorra','AD','AND','376'), (6,'Angola','AO','AGO','244'),
		(7,'Anguilla','AI','AIA','1-264'), (8,'Antarctica','AQ','ATA','672'), (9,'Antigua and Barbuda','AG','ATG','1-268'),
		(10,'Argentina','AR','ARG','54'), (11,'Armenia','AM','ARM','374'), (12,'Aruba','AW','ABW','297'), (13,'Australia','AU','AUS','61'),
		(14,'Austria','AT','AUT','43'), (15,'Azerbaijan','AZ','AZE','994'), (16,'Bahamas','BS','BHS','1-242'), (17,'Bahrain','BH','BHR','973'),
		(18,'Bangladesh','BD','BGD','880'), (19,'Barbados','BB','BRB','1-246'), (20,'Belarus','BY','BLR','375'), (21,'Belgium','BE','BEL','32'),
		(22,'Belize','BZ','BLZ','501'), (23,'Benin','BJ','BEN','229'), (24,'Bermuda','BM','BMU','1-441'), (25,'Bhutan','BT','BTN','975'),
		(26,'Bolivia','BO','BOL','591'), (27,'Bosnia and Herzegovina','BA','BIH','387'), (28,'Botswana','BW','BWA','267'),
		(29,'Brazil','BR','BRA','55'), (30,'British Indian Ocean Territory','IO','IOT',''), (31,'British Virgin Islands','VG','VGB','1-284'),
		(32,'Brunei','BN','BRN','673'), (33,'Bulgaria','BG','BGR','359'), (34,'Burkina Faso','BF','BFA','226'), (35,'Burma (Myanmar)','MM','MMR','95'),
		(36,'Burundi','BI','BDI','257'), (37,'Cambodia','KH','KHM','855'), (38,'Cameroon','CM','CMR','237'), (39,'Canada','CA','CAN','1'),
		(40,'Cape Verde','CV','CPV','238'), (41,'Cayman Islands','KY','CYM','1-345'), (42,'Central African Republic','CF','CAF','236'),
		(43,'Chad','TD','TCD','235'), (44,'Chile','CL','CHL','56'), (45,'China','CN','CHN','86'), (46,'Christmas Island','CX','CXR','61'),
		(47,'Cocos (Keeling) Islands','CC','CCK','61'), (48,'Colombia','CO','COL','57'), (49,'Comoros','KM','COM','269'),
		(50,'Cook Islands','CK','COK','682'), (51,'Costa Rica','CR','CRC','506'), (52,'Croatia','HR','HRV','385'),
		(53,'Cuba','CU','CUB','53'), (54,'Cyprus','CY','CYP','357'), (55,'Czech Republic','CZ','CZE','420'),
		(56,'Democratic Republic of the Congo','CD','COD','243'), (57,'Denmark','DK','DNK','45'), (58,'Djibouti','DJ','DJI','253'),
		(59,'Dominica','DM','DMA','1-767'), (60,'Dominican Republic','DO','DOM','1-809'), (61,'Ecuador','EC','ECU','593'),
		(62,'Egypt','EG','EGY','20'), (63,'El Salvador','SV','SLV','503'), (64,'Equatorial Guinea','GQ','GNQ','240'),
		(65,'Eritrea','ER','ERI','291'), (66,'Estonia','EE','EST','372'), (67,'Ethiopia','ET','ETH','251'),
		(68,'Falkland Islands','FK','FLK','500'), (69,'Faroe Islands','FO','FRO','298'), (70,'Fiji','FJ','FJI','679'),
		(71,'Finland','FI','FIN','358'), (72,'France','FR','FRA','33'), (73,'French Polynesia','PF','PYF','689'), (74,'Gabon','GA','GAB','241'),
		(75,'Gambia','GM','GMB','220'), (76,'Gaza Strip','','','970'), (77,'Georgia','GE','GEO','995'), (78,'Germany','DE','DEU','49'),
		(79,'Ghana','GH','GHA','233'), (80,'Gibraltar','GI','GIB','350'), (81,'Greece','GR','GRC','30'), (82,'Greenland','GL','GRL','299'),
		(83,'Grenada','GD','GRD','1-473'), (84,'Guam','GU','GUM','1-671'), (85,'Guatemala','GT','GTM','502'), (86,'Guinea','GN','GIN','224'),
		(87,'Guinea-Bissau','GW','GNB','245'), (88,'Guyana','GY','GUY','592'), (89,'Haiti','HT','HTI','509'), (90,'Holy See (Vatican City)','VA','VAT','39'),
		(91,'Honduras','HN','HND','504'), (92,'Hong Kong','HK','HKG','852'), (93,'Hungary','HU','HUN','36'), (94,'Iceland','IS','IS','354'), (95,'India','IN','IND','91'),
		(96,'Indonesia','ID','IDN','62'), (97,'Iran','IR','IRN','98'), (98,'Iraq','IQ','IRQ','964'), (99,'Ireland','IE','IRL','353'),
		(100,'Isle of Man','IM','IMN','44'), (101,'Israel','IL','ISR','972'), (102,'Italy','IT','ITA','39'), (103,'Ivory Coast','CI','CIV','225'),
		(104,'Jamaica','JM','JAM','1-876'), (105,'Japan','JP','JPN','81'), (106,'Jersey','JE','JEY',''), (107,'Jordan','JO','JOR','962'),
		(108,'Kazakhstan','KZ','KAZ','7'), (109,'Kenya','KE','KEN','254'), (110,'Kiribati','KI','KIR','686'), (111,'Kosovo','','','381'),
		(112,'Kuwait','KW','KWT','965'), (113,'Kyrgyzstan','KG','KGZ','996'), (114,'Laos','LA','LAO','856'), (115,'Latvia','LV','LVA','371'),
		(116,'Lebanon','LB','LBN','961'), (117,'Lesotho','LS','LSO','266'), (118,'Liberia','LR','LBR','231'), (119,'Libya','LY','LBY','218'),
		(120,'Liechtenstein','LI','LIE','423'), (121,'Lithuania','LT','LTU','370'), (122,'Luxembourg','LU','LUX','352'), (123,'Macau','MO','MAC','853'),
		(124,'Macedonia','MK','MKD','389'), (125,'Madagascar','MG','MDG','261'), (126,'Malawi','MW','MWI','265'), (127,'Malaysia','MY','MYS','60'),
		(128,'Maldives','MV','MDV','960'), (129,'Mali','ML','MLI','223'), (130,'Malta','MT','MLT','356'), (131,'Marshall Islands','MH','MHL','692'),
		(132,'Mauritania','MR','MRT','222'), (133,'Mauritius','MU','MUS','230'), (134,'Mayotte','YT','MYT','262'), (135,'Mexico','MX','MEX','52'),
		(136,'Micronesia','FM','FSM','691'), (137,'Moldova','MD','MDA','373'), (138,'Monaco','MC','MCO','377'), (139,'Mongolia','MN','MNG','976'),
		(140,'Montenegro','ME','MNE','382'), (141,'Montserrat','MS','MSR','1-664'), (142,'Morocco','MA','MAR','212'), (143,'Mozambique','MZ','MOZ','258'),
		(144,'Namibia','NA','NAM','264'), (145,'Nauru','NR','NRU','674'), (146,'Nepal','NP','NPL','977'), (147,'Netherlands','NL','NLD','31'),
		(148,'Netherlands Antilles','AN','ANT','599'), (149,'New Caledonia','NC','NCL','687'), (150,'New Zealand','NZ','NZL','64'),
		(151,'Nicaragua','NI','NIC','505'), (152,'Niger','NE','NER','227'), (153,'Nigeria','NG','NGA','234'), (154,'Niue','NU','NIU','683'),
		(155,'Norfolk Island','','','672'), (156,'North Korea','KP','PRK','850'), (157,'Northern Mariana Islands','MP','MNP','1-670'),
		(158,'Norway','NO','NOR','47'), (159,'Oman','OM','OMN','968'), (160,'Pakistan','PK','PAK','92'), (161,'Palau','PW','PLW','680'),
		(162,'Panama','PA','PAN','507'), (163,'Papua New Guinea','PG','PNG','675'), (164,'Paraguay','PY','PRY','595'), (165,'Peru','PE','PER','51'),
		(166,'Philippines','PH','PHL','63'), (167,'Pitcairn Islands','PN','PCN','870'), (168,'Poland','PL','POL','48'), (169,'Portugal','PT','PRT','351'),
		(170,'Puerto Rico','PR','PRI','1'), (171,'Qatar','QA','QAT','974'), (172,'Republic of the Congo','CG','COG','242'), (173,'Romania','RO','ROU','40'),
		(174,'Russia','RU','RUS','7'), (175,'Rwanda','RW','RWA','250'), (176,'Saint Barthelemy','BL','BLM','590'), (177,'Saint Helena','SH','SHN','290'),
		(178,'Saint Kitts and Nevis','KN','KNA','1-869'), (179,'Saint Lucia','LC','LCA','1-758'), (180,'Saint Martin','MF','MAF','1-599'),
		(181,'Saint Pierre and Miquelon','PM','SPM','508'), (182,'Saint Vincent and the Grenadines','VC','VCT','1-784'), (183,'Samoa','WS','WSM','685'),
		(184,'San Marino','SM','SMR','378'), (185,'Sao Tome and Principe','ST','STP','239'), (186,'Saudi Arabia','SA','SAU','966'), (187,'Senegal','SN','SEN','221'),
		(188,'Serbia','RS','SRB','381'), (189,'Seychelles','SC','SYC','248'), (190,'Sierra Leone','SL','SLE','232'), (191,'Singapore','SG','SGP','65'),
		(192,'Slovakia','SK','SVK','421'), (193,'Slovenia','SI','SVN','386'), (194,'Solomon Islands','SB','SLB','677'), (195,'Somalia','SO','SOM','252'),
		(196,'South Africa','ZA','ZAF','27'), (197,'South Korea','KR','KOR','82'), (198,'Spain','ES','ESP','34'), (199,'Sri Lanka','LK','LKA','94'),
		(200,'Sudan','SD','SDN','249'), (201,'Suriname','SR','SUR','597'), (202,'Svalbard','SJ','SJM',''), (203,'Swaziland','SZ','SWZ','268'),
		(204,'Sweden','SE','SWE','46'), (205,'Switzerland','CH','CHE','41'), (206,'Syria','SY','SYR','963'), (207,'Taiwan','TW','TWN','886'),
		(208,'Tajikistan','TJ','TJK','992'), (209,'Tanzania','TZ','TZA','255'), (210,'Thailand','TH','THA','66'), (211,'Timor-Leste','TL','TLS','670'),
		(212,'Togo','TG','TGO','228'), (213,'Tokelau','TK','TKL','690'), (214,'Tonga','TO','TON','676'), (215,'Trinidad and Tobago','TT','TTO','1-868'),
		(216,'Tunisia','TN','TUN','216'), (217,'Turkey','TR','TUR','90'), (218,'Turkmenistan','TM','TKM','993'), (219,'Turks and Caicos Islands','TC','TCA','1-649'),
		(220,'Tuvalu','TV','TUV','688'), (221,'Uganda','UG','UGA','256'), (222,'Ukraine','UA','UKR','380'), (223,'United Arab Emirates','AE','ARE','971'),
		(224,'United Kingdom','GB','GBR','44'), (225,'United States','US','USA','1'), (226,'Uruguay','UY','URY','598'), (227,'US Virgin Islands','VI','VIR','1-340'),
		(228,'Uzbekistan','UZ','UZB','998'), (229,'Vanuatu','VU','VUT','678'), (230,'Venezuela','VE','VEN','58'), (231,'Vietnam','VN','VNM','84'),
		(232,'Wallis and Futuna','WF','WLF','681'), (233,'West Bank','','','970'), (234,'Western Sahara','EH','ESH',''), (235,'Yemen','YE','YEM','967'),
		(236,'Zambia','ZM','ZMB','260'), (237,'Zimbabwe','ZW','ZWE','263');");
        $db->execute();

		/*
        $db->setQuery("UPDATE #__js_res_comments AS c SET c.section_id = (SELECT section_id FROM #__js_res_record WHERE id = c.record_id) WHERE c.section_id = 0");
        $db->execute();

        $db->setQuery("UPDATE `#__js_res_record_values` AS s SET s.section_id = (SELECT r.section_id FROM `#__js_res_record` AS r WHERE r.id = s.record_id) WHERE s.section_id = 0");
        $db->execute();

        $db->setQuery("UPDATE `#__js_res_record` SET parent = 'com_joomcck' WHERE parent = ''");
        $db->execute();

        $db->setQuery("UPDATE `#__js_res_record` SET pubtime = NOW() WHERE published = 1");
        $db->execute();

        $db->setQuery("DELETE FROM `#__js_res_subscribe` WHERE type NOT IN ('record', 'section')");
        $db->execute();

        $db->setQuery("INSERT INTO `#__js_res_subscribe` (user_id, ref_id, type, ctime, section_id)
				SELECT user_id, u_id, 'user', ctime, section_id FROM `#__js_res_subscribe_user`");
        $db->execute();

        $db->setQuery("INSERT INTO `#__js_res_subscribe` (user_id, ref_id, type, ctime, section_id)
				SELECT user_id, cat_id, 'category', ctime, section_id FROM `#__js_res_subscribe_cat`");
        $db->execute();*/
    }

    private function _deleteFiles()
    {
        $files = [
            '/administrator/components/com_joomcck/tables/comments.php',
            '/components/com_joomcck/fields/geo/qwer.php',
            '/administrator/components/com_joomcck/tables/category.php'
        ];

        foreach ($files as $file) {
            if (is_file(JPATH_ROOT . $file)) {
                \Joomla\Filesystem\File::delete(JPATH_ROOT . $file);
            }
        }
    }
}

class JoomlaTableExtensions extends \Joomla\CMS\Table\Table
{
    public function __construct(&$_db)
    {
        parent::__construct('#__extensions', 'extension_id', $_db);
    }
}
