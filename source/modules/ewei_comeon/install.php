<?php 
	$sql = "
CREATE TABLE IF NOT EXISTS ".tablename('ewei_comeon_reply')." (
  `id` int(10) unsigned  AUTO_INCREMENT,
  `rid` int(10) unsigned default 0,
  `weid` int(11) default 0 ,
  `title` varchar(50) DEFAULT '',
  `description` varchar(255) DEFAULT '',
  `thumb` varchar(200)  DEFAULT '',
  `isshow` tinyint(1) DEFAULT 0,
  `fansnum` int(11) DEFAULT 0,
  `viewnum` int(11) DEFAULT 0,
  `toppic` varchar(255)  DEFAULT '',
  `bgcolor` varchar(255)  DEFAULT '',
  `fontcolor` varchar(255)  DEFAULT '',
  `btncolor` varchar(255)  DEFAULT '',
  `btnfontcolor` varchar(255)  DEFAULT '',
  `start` decimal(10,2) DEFAULT 0,
  `end` decimal(10,2) DEFAULT 0,    
  `tips` varchar(200) DEFAULT '',
  `info_tips` varchar(200) DEFAULT '' comment '例如 您已经获得 [P] [U]',
  `help_tips` varchar(200) DEFAULT '' comment '例如 给TA助力',
  `join_tips` varchar(200) DEFAULT '' comment '例如 我也来领取加油卡',
  `invite_tips` varchar(200) DEFAULT '' comment '例如 邀请好友助力',
  `rank_tips` varchar(200) DEFAULT '' comment '例如 显示排名',
  `rank_num` int(11) DEFAULT 0 comment '多少名之前的排名',
  `unit` varchar(200) DEFAULT '' comment '单位',
  `ticket_information` varchar(200) DEFAULT '',
  `tel_rename` varchar(200) DEFAULT '',
  `content` text,
  `copyright` varchar(200) DEFAULT '',
  `joincontent` text,
  `overcontent` text,                      
  `self_times` int(11) DEFAULT '0' comment '活动期间可以被助力几次',
  `self_day_times` int(11) DEFAULT '0' comment '每天可以被助力几次',
  `other_times` int(11) DEFAULT '0' comment '活动期间可给别人助力多少次',
  `other_day_times` int(11) DEFAULT '0' comment '每天可给别人助力多少次',
  
  `other_one_times` int(11) DEFAULT '0' comment '活动期间可给相同助力多少次',
  `other_one_day_times` int(11) DEFAULT '0' comment '每天可给相同用户助力多少次',
  
  `type` tinyint(1) default 0 comment '规则类型 0 集分 1 集分',       
  `show_rank` tinyint(1) default 0 comment '显示排名 0 不显示 1 显示',       
  `show_num` tinyint(1) default 0 comment '是否显示奖品数量',       
  `awardtype` tinyint(1) default 0 comment '奖品类型 0 一次性 1 阶梯性',       
  `awards` text comment '奖品',          
  `rules` text comment '规则',          
  `starttime` int(10) DEFAULT 0,
  `endtime` int(10) DEFAULT 0,
  `createtime` int(10) DEFAULT 0,
  `share_title` varchar(200) DEFAULT '',
  `share_desc` varchar(300) DEFAULT '',
  `share_url` varchar(100) DEFAULT '',
  `share_txt` varchar(500) DEFAULT '',
  PRIMARY KEY (`id`),KEY `indx_rid` (`rid`),KEY `indx_weid` (`weid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";

pdo_query($sql);

 $sql="CREATE TABLE IF NOT EXISTS ".tablename('ewei_comeon_award')." (
  `id` int(11)  AUTO_INCREMENT,
  `rid` int(11) DEFAULT 0 ,
  `point` decimal(10,2) default 0,
  `name` varchar(255) default '',
  `num` int(11) default 0,
  PRIMARY KEY (`id`),KEY `indx_rid` (`rid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8; ";
 pdo_query($sql);         

 $sql="CREATE TABLE IF NOT EXISTS ".tablename('ewei_comeon_rule')." (
  `id` int(11) AUTO_INCREMENT,
  `rid` int(11) DEFAULT 0 ,
  `point` decimal(10,2) default 0,
  `start` decimal(10,2) default 0,
  `end` decimal(10,2) default 0,
  PRIMARY KEY (`id`),KEY `indx_rid` (`rid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8; ";         
pdo_query($sql);
 $sql="CREATE TABLE IF NOT EXISTS ".tablename('ewei_comeon_fans')." (
  `id` int(11)  AUTO_INCREMENT,
  `rid` int(11) DEFAULT 0 ,
  `from_user` varchar(100) DEFAULT '' COMMENT '用户ID',
  `mobile` varchar(20) DEFAULT '' COMMENT '登记信息(手机等)',
  `points` decimal(10,2) DEFAULT '0' comment '点数',
  `helps` int(11) DEFAULT '0' comment '被助力次数',
  `createtime` int(10) DEFAULT '0',
  `status` int(10) DEFAULT '0',
  `awardid` int(10) DEFAULT '0',
  `awardtime` int(10) DEFAULT '0',
  PRIMARY KEY (`id`),KEY `indx_rid` (`rid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
pdo_query($sql);
 $sql="CREATE TABLE IF NOT EXISTS ".tablename('ewei_comeon_fans_help')." (
  `id` int(11)  AUTO_INCREMENT,
  `rid` int(11) DEFAULT 0 ,
  `from_user` varchar(100) default ''  COMMENT '助力的',
  `fansid` int(11) default 0  COMMENT '被助力的',
  `date` varchar(20) DEFAULT '',
  PRIMARY KEY (`id`),KEY `indx_rid` (`rid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
pdo_query($sql);