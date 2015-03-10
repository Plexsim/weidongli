<?php

if(!pdo_fieldexists('we7photomaker', 'adtype')) {

	pdo_query("ALTER TABLE ".tablename('we7photomaker')." ADD `adtype` tinyint(1) NOT NULL AFTER `maxtotal`;");

}



if(!pdo_fieldexists('we7photomaker', 'adurlh')) {

	pdo_query("ALTER TABLE ".tablename('we7photomaker')." ADD `adurlh` varchar(255) NOT NULL AFTER `admsg`;");

	pdo_query("ALTER TABLE ".tablename('we7photomaker')." ADD `adurlv` varchar(255) NOT NULL AFTER `adurlh`;");

}



