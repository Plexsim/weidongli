<?php 

 $sql="CREATE TABLE IF NOT EXISTS ".tablename('ewei_comeon_fans_help')." (
  `id` int(11)  AUTO_INCREMENT,
  `rid` int(11) DEFAULT 0 ,
  `from_user` varchar(100) default ''  COMMENT '助力的',
  `fansid` int(11) default 0  COMMENT '被助力的',
  `date` varchar(20) DEFAULT '',
  PRIMARY KEY (`id`),KEY `indx_rid` (`rid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
pdo_query($sql);


if(!pdo_fieldexists('ewei_comeon_fans_help', 'from_user')) {
    pdo_query("ALTER TABLE ".tablename('ewei_comeon_fans_help')." ADD `from_user` varchar(100) DEFAULT '';");
}

if(!pdo_fieldexists('ewei_comeon_reply', 'self_day_times')) {
    pdo_query("ALTER TABLE ".tablename('ewei_comeon_reply')." ADD `self_day_times` int(11) default 0;");
}
if(!pdo_fieldexists('ewei_comeon_reply', 'other_day_times')) {
    pdo_query("ALTER TABLE ".tablename('ewei_comeon_reply')." ADD `other_day_times` int(11) default 0;");
}
if(!pdo_fieldexists('ewei_comeon_reply', 'other_one_times')) {
    pdo_query("ALTER TABLE ".tablename('ewei_comeon_reply')." ADD `other_one_times` int(11) default 0;");
}
if(!pdo_fieldexists('ewei_comeon_reply', 'other_one_day_times')) {
    pdo_query("ALTER TABLE ".tablename('ewei_comeon_reply')." ADD `other_one_day_times` int(11) default 0;");
}

if(!pdo_fieldexists('ewei_comeon_reply', 'show_rank')) {
    pdo_query("ALTER TABLE ".tablename('ewei_comeon_reply')." ADD `show_rank` int(11) default 0;");
}
if(!pdo_fieldexists('ewei_comeon_reply', 'rank_tips')) {
    pdo_query("ALTER TABLE ".tablename('ewei_comeon_reply')." ADD `rank_tips` varchar(200) default '';");
}
if(!pdo_fieldexists('ewei_comeon_reply', 'rank_num')) {
    pdo_query("ALTER TABLE ".tablename('ewei_comeon_reply')." ADD `rank_num` int(11) default 10;");
}



