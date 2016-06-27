<?php
if (!defined("IN_IA")) {
    exit("Access Denied");
}
class Ewei_DShop_Member
{
    public function getInfo($val0 = '')
    {
        global $_W;
        $val2 = intval($val0);
        if ($val2 == 0) {
            $val5 = pdo_fetch("select * from " . tablename("ewei_shop_member") . " where openid=:openid and uniacid=:uniacid limit 1", array(":uniacid" => $_W["uniacid"], ":openid" => $val0));
        } else {
            $val5 = pdo_fetch("select * from " . tablename("ewei_shop_member") . " where id=:id  and uniacid=:uniacid limit 1", array(":uniacid" => $_W["uniacid"], ":id" => $val2));
        }
        if (!empty($val5["uid"])) {
            load()->model("mc");
            $val2 = mc_openid2uid($val5["openid"]);
            $val14 = mc_fetch($val2, array("credit1", "credit2", "birthyear", "birthmonth", "birthday", "gender", "avatar", "resideprovince", "residecity", "nickname"));
            $val5["credit1"] = $val14["credit1"];
            $val5["credit2"] = $val14["credit2"];
            $val5["birthyear"] = empty($val5["birthyear"]) ? $val14["birthyear"] : $val5["birthyear"];
            $val5["birthmonth"] = empty($val5["birthmonth"]) ? $val14["birthmonth"] : $val5["birthmonth"];
            $val5["birthday"] = empty($val5["birthday"]) ? $val14["birthday"] : $val5["birthday"];
            $val5["nickname"] = empty($val5["nickname"]) ? $val14["nickname"] : $val5["nickname"];
            $val5["gender"] = empty($val5["gender"]) ? $val14["gender"] : $val5["gender"];
            $val5["sex"] = $val5["gender"];
            $val5["avatar"] = empty($val5["avatar"]) ? $val14["avatar"] : $val5["avatar"];
            $val5["headimgurl"] = $val5["avatar"];
            $val5["province"] = empty($val5["province"]) ? $val14["resideprovince"] : $val5["province"];
            $val5["city"] = empty($val5["city"]) ? $val14["residecity"] : $val5["city"];
        }
        if (!empty($val5["birthyear"]) && !empty($val5["birthmonth"]) && !empty($val5["birthday"])) {
            $val5["birthday"] = $val5["birthyear"] . "-" . (strlen($val5["birthmonth"]) <= 1 ? '0' . $val5["birthmonth"] : $val5["birthmonth"]) . "-" . (strlen($val5["birthday"]) <= 1 ? '0' . $val5["birthday"] : $val5["birthday"]);
        }
        if (empty($val5["birthday"])) {
            $val5["birthday"] = '';
        }
        return $val5;
    }
    public function getMember($val0 = '')
    {
        global $_W;
        $val2 = intval($val0);
        if (empty($val2)) {
            $val5 = pdo_fetch("select * from " . tablename("ewei_shop_member") . " where  openid=:openid and uniacid=:uniacid limit 1", array(":uniacid" => $_W["uniacid"], ":openid" => $val0));
        } else {
            $val5 = pdo_fetch("select * from " . tablename("ewei_shop_member") . " where id=:id and uniacid=:uniacid limit 1", array(":uniacid" => $_W["uniacid"], ":id" => $val2));
        }
        if (!empty($val5)) {
            $val0 = $val5["openid"];
            if (empty($val5["uid"])) {
                $val85 = m("user")->followed($val0);
                if ($val85) {
                    load()->model("mc");
                    $val2 = mc_openid2uid($val0);
                    if (!empty($val2)) {
                        $val5["uid"] = $val2;
                        $val93 = array("uid" => $val2);
                        if ($val5["credit1"] > 0) {
                            mc_credit_update($val2, "credit1", $val5["credit1"]);
                            $val93["credit1"] = 0;
                        }
                        if ($val5["credit2"] > 0) {
                            mc_credit_update($val2, "credit2", $val5["credit2"]);
                            $val93["credit2"] = 0;
                        }
                        if (!empty($val93)) {
                            pdo_update("ewei_shop_member", $val93, array("id" => $val5["id"]));
                        }
                    }
                }
            }
            $val106 = $this->getCredits($val0);
            $val5["credit1"] = $val106["credit1"];
            $val5["credit2"] = $val106["credit2"];
        }
        return $val5;
    }
    public function getMid()
    {
        global $_W;
        $val0 = m("user")->getOpenid();
        $val115 = $this->getMember($val0);
        return $val115["id"];
    }
    public function setCredit($val0 = '', $val119 = 'credit1', $val106 = 0, $val121 = array())
    {
        global $_W;
        load()->model("mc");
        $val2 = mc_openid2uid($val0);
        if (!empty($val2)) {
            $val126 = pdo_fetchcolumn("SELECT {$val119} FROM " . tablename("mc_members") . " WHERE `uid` = :uid", array(":uid" => $val2));
            $val129 = $val106 + $val126;
            if ($val129 <= 0) {
                $val129 = 0;
            }
            pdo_update("mc_members", array($val119 => $val129), array("uid" => $val2));
            if (empty($val121) || !is_array($val121)) {
                $val121 = array($val2, "未记录");
            }
            $val141 = array("uid" => $val2, "credittype" => $val119, "uniacid" => $_W["uniacid"], "num" => $val106, "module" => "ewei_shop", "createtime" => TIMESTAMP, "operator" => intval($val121[0]), "remark" => $val121[1]);
            pdo_insert("mc_credits_record", $val141);
        } else {
            $val126 = pdo_fetchcolumn("SELECT {$val119} FROM " . tablename("ewei_shop_member") . " WHERE  uniacid=:uniacid and openid=:openid limit 1", array(":uniacid" => $_W["uniacid"], ":openid" => $val0));
            $val129 = $val106 + $val126;
            if ($val129 <= 0) {
                $val129 = 0;
            }
            pdo_update("ewei_shop_member", array($val119 => $val129), array("uniacid" => $_W["uniacid"], "openid" => $val0));
        }
    }
    public function getCredit($val0 = '', $val119 = 'credit1')
    {
        global $_W;
        load()->model("mc");
        $val2 = mc_openid2uid($val0);
        if (!empty($val2)) {
            return pdo_fetchcolumn("SELECT {$val119} FROM " . tablename("mc_members") . " WHERE `uid` = :uid", array(":uid" => $val2));
        } else {
            return pdo_fetchcolumn("SELECT {$val119} FROM " . tablename("ewei_shop_member") . " WHERE  openid=:openid and uniacid=:uniacid limit 1", array(":uniacid" => $_W["uniacid"], ":openid" => $val0));
        }
    }
    public function getCredits($val0 = '', $val174 = array('credit1', 'credit2'))
    {
        global $_W;
        load()->model("mc");
        $val2 = mc_openid2uid($val0);
        $val178 = implode(",", $val174);
        if (!empty($val2)) {
            return pdo_fetch("SELECT {$val178} FROM " . tablename("mc_members") . " WHERE `uid` = :uid limit 1", array(":uid" => $val2));
        } else {
            return pdo_fetch("SELECT {$val178} FROM " . tablename("ewei_shop_member") . " WHERE  openid=:openid and uniacid=:uniacid limit 1", array(":uniacid" => $_W["uniacid"], ":openid" => $val0));
        }
    }
    public function checkMember($val0 = '')
    {
        global $_W, $_GPC;
        if (strexists($_SERVER["REQUEST_URI"], "/web/")) {
            return;
        }
        if (empty($val0)) {
            $val0 = m("user")->getOpenid();
        }
        if (empty($val0)) {
            return;
        }
        $val115 = m("member")->getMember($val0);
        $val195 = m("user")->getInfo();
        $val85 = m("user")->followed($val0);
        $val2 = 0;
        $val199 = array();
        load()->model("mc");
        if ($val85) {
            $val2 = mc_openid2uid($val0);
            $val199 = mc_fetch($val2, array("realname", "mobile", "avatar", "resideprovince", "residecity", "residedist"));
        }
        if (empty($val115)) {
            $val115 = array("uniacid" => $_W["uniacid"], "uid" => $val2, "openid" => $val0, "realname" => !empty($val199["realname"]) ? $val199["realname"] : '', "mobile" => !empty($val199["mobile"]) ? $val199["mobile"] : '', "nickname" => !empty($val199["nickname"]) ? $val199["nickname"] : $val195["nickname"], "avatar" => !empty($val199["avatar"]) ? $val199["avatar"] : $val195["avatar"], "gender" => !empty($val199["gender"]) ? $val199["gender"] : $val195["sex"], "province" => !empty($val199["residecity"]) ? $val199["resideprovince"] : $val195["province"], "city" => !empty($val199["residecity"]) ? $val199["residecity"] : $val195["city"], "area" => !empty($val199["residedist"]) ? $val199["residedist"] : '', "createtime" => time(), "status" => 0);
            pdo_insert("ewei_shop_member", $val115);
        } else {
            if ($val115["isblack"] == 1) {
                die("<!DOCTYPE html>
						<html>
							<head>
								<meta name='viewport' content='width=device-width, initial-scale=1, user-scalable=0'>
								<title>抱歉，出错了</title><meta charset='utf-8'><meta name='viewport' content='width=device-width, initial-scale=1, user-scalable=0'><link rel='stylesheet' type='text/css' href='https://res.wx.qq.com/connect/zh_CN/htmledition/style/wap_err1a9853.css'>
							</head>
							<body>
							<div class='page_msg'><div class='inner'><span class='msg_icon_wrp'><i class='icon80_smile'></i></span><div class='msg_content'><h4>暂时无法访问，请稍后再试!</h4></div></div></div>
							</body>
						</html>");
            }
            $val93 = array();
            if ($val195["nickname"] != $val115["nickname"] && !empty($val195["nickname"])) {
                $val93["nickname"] = $val195["nickname"];
            }
            if ($val195["avatar"] != $val115["avatar"] && !empty($val195["avatar"])) {
                $val93["avatar"] = $val195["avatar"];
            }
            if (!empty($val93)) {
                pdo_update("ewei_shop_member", $val93, array("id" => $val115["id"]));
            }
        }
        if (p("commission")) {
            p("commission")->checkAgent();
        }
        if (p("poster")) {
            p("poster")->checkScan();
        }
    }
    function getLevels()
    {
        global $_W;
        return pdo_fetchall("select * from " . tablename("ewei_shop_member_level") . " where uniacid=:uniacid order by level asc", array(":uniacid" => $_W["uniacid"]));
    }
    function getLevel($val0)
    {
        global $_W;
        if (empty($val0)) {
            return false;
        }
        $val252 = m("common")->getSysset("shop");
        $val115 = m("member")->getMember($val0);
        if (empty($val115["level"])) {
            return array("discount" => $val252["leveldiscount"]);
        }
        $val257 = pdo_fetch("select * from " . tablename("ewei_shop_member_level") . " where id=:id and uniacid=:uniacid order by level asc", array(":uniacid" => $_W["uniacid"], ":id" => $val115["level"]));
        if (empty($val257)) {
            return array("discount" => $val252["leveldiscount"]);
        }
        return $val257;
    }
    function upgradeLevel($val0)
    {
        global $_W;
        if (empty($val0)) {
            return;
        }
        $val266 = m("common")->getSysset("shop");
        $val267 = intval($val266["leveltype"]);
        $val115 = m("member")->getMember($val0);
        if (empty($val115)) {
            return;
        }
        $val257 = false;
        if (empty($val267)) {
            $val274 = pdo_fetchcolumn("select ifnull( sum(og.realprice),0) from " . tablename("ewei_shop_order_goods") . " og " . " left join " . tablename("ewei_shop_order") . " o on o.id=og.orderid " . " where o.openid=:openid and o.status=3 and o.uniacid=:uniacid ", array(":uniacid" => $_W["uniacid"], ":openid" => $val115["openid"]));
            $val257 = pdo_fetch("select * from " . tablename("ewei_shop_member_level") . " where uniacid=:uniacid  and {$val274} >= ordermoney and ordermoney>0  order by level desc limit 1", array(":uniacid" => $_W["uniacid"]));
        } else {
            if ($val267 == 1) {
                $val281 = pdo_fetchcolumn("select count(*) from " . tablename("ewei_shop_order") . " where openid=:openid and status=3 and uniacid=:uniacid ", array(":uniacid" => $_W["uniacid"], ":openid" => $val115["openid"]));
                $val257 = pdo_fetch("select * from " . tablename("ewei_shop_member_level") . " where uniacid=:uniacid  and {$val281} >= ordercount and ordercount>0  order by level desc limit 1", array(":uniacid" => $_W["uniacid"]));
            }
        }
        if (empty($val257)) {
            return;
        }
        if ($val257["id"] == $val115["level"]) {
            return;
        }
        $val290 = $this->getLevel($val0);
        $val292 = false;
        if (empty($val290["id"])) {
            $val292 = true;
        } else {
            if ($val257["level"] > $val290["level"]) {
                $val292 = true;
            }
        }
        if ($val292) {
            pdo_update("ewei_shop_member", array("level" => $val257["id"]), array("id" => $val115["id"]));
            m("notice")->sendMemberUpgradeMessage($val0, $val290, $val257);
        }
    }
    function getGroups()
    {
        global $_W;
        return pdo_fetchall("select * from " . tablename("ewei_shop_member_group") . " where uniacid=:uniacid order by id asc", array(":uniacid" => $_W["uniacid"]));
    }
    function getGroup($val0)
    {
        if (empty($val0)) {
            return false;
        }
        $val115 = m("member")->getMember($val0);
        return $val115["groupid"];
    }
    function setRechargeCredit($val0 = '', $val312 = 0)
    {
        if (empty($val0)) {
            return;
        }
        global $_W;
        $val315 = 0;
        $val316 = m("common")->getSysset(array("trade", "shop"));
        if ($val316["trade"]) {
            $val318 = floatval($val316["trade"]["money"]);
            $val320 = intval($val316["trade"]["credit"]);
            if ($val318 > 0) {
                if ($val312 % $val318 == 0) {
                    $val315 = intval($val312 / $val318) * $val320;
                } else {
                    $val315 = (intval($val312 / $val318) + 1) * $val320;
                }
            }
        }
        if ($val315 > 0) {
            $this->setCredit($val0, "credit1", $val315, array(0, $val316["shop"]["name"] . "会员充值积分:credit2:" . $val315));
        }
    }
}