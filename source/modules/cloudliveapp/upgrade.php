<?php
if(!pdo_fieldexists('cloudliveapp', 'icon')) {
	pdo_query("ALTER TABLE ".tablename('cloudliveapp')." ADD `icon` VARCHAR(100) NOT NULL DEFAULT '' AFTER  `title`;");
}
if(!pdo_fieldexists('cloudliveapp', 'loading')) {
	pdo_query("ALTER TABLE ".tablename('cloudliveapp')." ADD `loading` VARCHAR(100) NOT NULL DEFAULT '' AFTER  `icon`;");
}
if(!pdo_fieldexists('cloudliveapp', 'mauto')) {
	pdo_query("ALTER TABLE ".tablename('cloudliveapp')." ADD `mauto` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1' AFTER  `music`;");
}
if(!pdo_fieldexists('cloudliveapp', 'mloop')) {
	pdo_query("ALTER TABLE ".tablename('cloudliveapp')." ADD `mloop` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1' AFTER  `mauto`;");
}
if(!pdo_fieldexists('cloudliveapp', 'isloop')) {
	pdo_query("ALTER TABLE ".tablename('cloudliveapp')." ADD `isloop` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER  `displayorder`;");
}
$sql = "
CREATE TABLE IF NOT EXISTS `ims_cloudliveapp_item` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `cloudliveappid` int(10) unsigned NOT NULL,
  `photoid` int(10) unsigned NOT NULL,
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `item` varchar(1000) NOT NULL DEFAULT '',
  `url` varchar(100) NOT NULL DEFAULT '',
  `animation` varchar(20) NOT NULL DEFAULT '',
  `createtime` int(10) unsigned NOT NULL,
  KEY `idx_photoid` (`photoid`),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
";
pdo_run($sql);
if(!pdo_fieldexists('cloudliveapp_item', 'x')) {
	pdo_query("ALTER TABLE ".tablename('cloudliveapp_item')." ADD `x` INT(3) NOT NULL DEFAULT '0' AFTER  `url`;");
}
if(!pdo_fieldexists('cloudliveapp_item', 'y')) {
	pdo_query("ALTER TABLE ".tablename('cloudliveapp_item')." ADD `y` INT(3) NOT NULL DEFAULT '0' AFTER  `x`;");
}
if(pdo_fieldexists('cloudliveapp', 'loading')) {
	pdo_query("ALTER TABLE ".tablename('cloudliveapp')." DROP `loading`;");
}
if((pdo_fieldexists('cloudliveapp', 'thumb'))&(!pdo_fieldexists('cloudliveapp', 'open'))) {
	pdo_query("ALTER TABLE ".tablename('cloudliveapp')." CHANGE `thumb`  `open` VARCHAR(100) NOT NULL DEFAULT '';");
}
if(!pdo_fieldexists('cloudliveapp', 'ostyle')) {
	pdo_query("ALTER TABLE ".tablename('cloudliveapp')." ADD `ostyle` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER `open`;");
}
if(!pdo_fieldexists('cloudliveapp', 'share')) {
	pdo_query("ALTER TABLE ".tablename('cloudliveapp')." ADD `share` VARCHAR(250) NOT NULL DEFAULT '' AFTER `icon`;");
}
if(!pdo_fieldexists('cloudliveapp', 'thumb')) {
	pdo_query("ALTER TABLE ".tablename('cloudliveapp')." ADD `thumb` VARCHAR(100) NOT NULL DEFAULT '' AFTER  `mloop`;");
}
if(pdo_fieldexists('cloudliveapp', 'share')) {
	pdo_query("ALTER TABLE ".tablename('cloudliveapp')." CHANGE `share`  `share` VARCHAR(250) NOT NULL DEFAULT '';");
}
if(pdo_fieldexists('cloudliveapp_item', 'url')) {
	pdo_query("ALTER TABLE ".tablename('cloudliveapp_item')." CHANGE `url`  `url` VARCHAR(250) NOT NULL DEFAULT '';");
}
if(pdo_fieldexists('cloudliveapp_item', 'y')) {
	pdo_query("ALTER TABLE ".tablename('cloudliveapp_item')." CHANGE `y`  `y` INT(3) NOT NULL DEFAULT '0';");
}