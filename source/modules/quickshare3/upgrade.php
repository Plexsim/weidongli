<?php
if(!pdo_fieldexists('quickshare3_article', 'redirect_url')) {
	pdo_query("ALTER TABLE ".tablename('quickshare3_article')." ADD `redirect_url` varchar(500) NOT NULL DEFAULT '' AFTER `displayorder`;");
}

