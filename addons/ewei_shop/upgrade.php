<?php

if(!pdo_fieldexists('ewei_shop_order', 'refundstate')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order')." ADD `refundstate` tinyint(3) NULL DEFAULT 0 AFTER `printstate`;");
}
if(!pdo_fieldexists('ewei_shop_order_refund', 'orderprice')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order_refund')." ADD `orderprice` decimal(10,2) NULL DEFAULT 0.00 AFTER `refundtype`;");
}
if(!pdo_fieldexists('ewei_shop_order_refund', 'applyprice')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order_refund')." ADD `applyprice` decimal(10,2) NULL DEFAULT 0.00 AFTER `orderprice`;");
}
if(!pdo_fieldexists('ewei_shop_order_refund', 'imgs')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order_refund')." ADD `imgs` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL AFTER `applyprice`;");
}
if(!pdo_fieldexists('ewei_shop_order_refund', 'rtype')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order_refund')." ADD `rtype` tinyint(3) NULL DEFAULT 0 AFTER `imgs`;");
}
if(!pdo_fieldexists('ewei_shop_order_refund', 'refundaddress')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order_refund')." ADD `refundaddress` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL AFTER `rtype`;");
}
if(!pdo_fieldexists('ewei_shop_order_refund', 'message')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order_refund')." ADD `message` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL AFTER `refundaddress`;");
}
if(!pdo_fieldexists('ewei_shop_order_refund', 'express')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order_refund')." ADD `express` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' AFTER `message`;");
}
if(!pdo_fieldexists('ewei_shop_order_refund', 'expresscom')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order_refund')." ADD `expresscom` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' AFTER `express`;");
}
if(!pdo_fieldexists('ewei_shop_order_refund', 'expresssn')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order_refund')." ADD `expresssn` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' AFTER `expresscom`;");
}
if(!pdo_fieldexists('ewei_shop_order_refund', 'operatetime')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order_refund')." ADD `operatetime` int(11) NULL DEFAULT 0 AFTER `expresssn`;");
}
if(!pdo_fieldexists('ewei_shop_order_refund', 'sendtime')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order_refund')." ADD `sendtime` int(11) NULL DEFAULT 0 AFTER `operatetime`;");
}
if(!pdo_fieldexists('ewei_shop_order_refund', 'returntime')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order_refund')." ADD `returntime` int(11) NULL DEFAULT 0 AFTER `sendtime`;");
}
if(!pdo_fieldexists('ewei_shop_order_refund', 'refundtime')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order_refund')." ADD `refundtime` int(11) NULL DEFAULT 0 AFTER `returntime`;");
}
if(!pdo_fieldexists('ewei_shop_order_refund', 'rexpress')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order_refund')." ADD `rexpress` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' AFTER `refundtime`;");
}
if(!pdo_fieldexists('ewei_shop_order_refund', 'rexpresscom')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order_refund')." ADD `rexpresscom` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' AFTER `rexpress`;");
}
if(!pdo_fieldexists('ewei_shop_order_refund', 'rexpresssn')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order_refund')." ADD `rexpresssn` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' AFTER `rexpresscom`");
}
if(!pdo_fieldexists('ewei_shop_order_refund', 'refundaddressid')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order_refund')." ADD `refundaddressid` int(11) NULL DEFAULT 0 AFTER `rexpresssn`;");
}
if(!pdo_fieldexists('ewei_shop_order_refund', 'endtime')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order_refund')." ADD `endtime` int(11) NULL DEFAULT 0 AFTER `refundaddressid`;");
}
if(!pdo_fieldexists('ewei_shop_poster', 'resptype')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_poster')." ADD `resptype` tinyint(3) NULL DEFAULT 0 AFTER `subcouponnum`;");
}
if(!pdo_fieldexists('ewei_shop_poster', 'resptext')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_poster')." ADD `resptext` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL AFTER `resptype`;");
}
if(!pdo_fieldexists('ewei_shop_postera', 'resptype')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_postera')." ADD `resptype` tinyint(3) NULL DEFAULT 0 AFTER `endtext`;");
}
if(!pdo_fieldexists('ewei_shop_postera', 'resptext')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_postera')." ADD `resptext` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL AFTER `resptype`;");
}
if(!pdo_fieldexists('ewei_shop_postera', 'testflag')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_postera')." ADD `testflag` tinyint(1) NULL DEFAULT 0 AFTER `resptext`;");
}
 
$sql = "
CREATE TABLE IF NOT EXISTS ".tablename('ewei_shop_refund_address'). " (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`uniacid`  int(11) NULL DEFAULT 0 ,
`title`  varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' ,
`name`  varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' ,
`tel`  varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' ,
`mobile`  varchar(11) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' ,
`province`  varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' ,
`city`  varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' ,
`area`  varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' ,
`address`  varchar(300) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' ,
`isdefault`  tinyint(1) NULL DEFAULT 0 ,
`zipcode`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' ,
`content`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL ,
`deleted`  tinyint(1) NULL DEFAULT 0 ,
PRIMARY KEY (`id`),
INDEX `idx_uniacid` (`uniacid`) USING BTREE 
)
ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci;
";
pdo_query($sql);