<?php
if (!defined("IN_IA")) {
    exit("Access Denied");
}
require IA_ROOT . "/addons/ewei_shop/defines.php";
require EWEI_SHOP_INC . "plugin/plugin_processor.php";
class PosterProcessor extends PluginProcessor
{
    public function __construct()
    {
        parent::__construct("poster");
    }
    public function respond($val0 = null)
    {
        global $_W;
        $val2 = $val0->message;
        $val4 = strtolower($val2["msgtype"]);
        $val6 = strtolower($val2["event"]);
        $val0->member = $this->model->checkMember($val2["from"]);
        if ($val4 == "text" || $val6 == "click") {
            return $this->responseText($val0);
        } else {
            if ($val4 == "event") {
                if ($val6 == "scan") {
                    return $this->responseScan($val0);
                } else {
                    if ($val6 == "subscribe") {
                        return $this->responseSubscribe($val0);
                    }
                }
            }
        }
    }
    private function responseText($val0)
    {
        global $_W;
        $val20 = 4;
        load()->func("communication");
        $val21 = $_W["siteroot"] . "app/index.php?i=" . $_W["uniacid"] . "&c=entry&m=ewei_shop&do=plugin&p=poster&method=build&timestamp=" . time();
        $val24 = ihttp_request($val21, array("openid" => $val0->message["from"], "content" => urlencode($val0->message["content"])), array(), $val20);
        return $this->responseEmpty();
    }
    private function responseEmpty()
    {
        ob_clean();
        ob_start();
        echo '';
        ob_flush();
        ob_end_flush();
        exit(0);
    }
    private function responseDefault($val0)
    {
        global $_W;
        return $val0->respText("感谢您的关注!");
    }
    private function responseScan($val0)
    {
        global $_W;
        $val35 = $val0->message["from"];
        $val37 = $val0->message["eventkey"];
        $val39 = $val0->message["ticket"];
        if (empty($val39)) {
            return $this->responseDefault($val0);
        }
        $val43 = $this->model->getQRByTicket($val39);
        if (empty($val43)) {
            return $this->responseDefault($val0);
        }
        $val47 = pdo_fetch("select * from " . tablename("ewei_shop_poster") . " where type=4 and isdefault=1 and uniacid=:uniacid limit 1", array(":uniacid" => $_W["uniacid"]));
        if (empty($val47)) {
            return $this->responseDefault($val0);
        }
        $this->model->scanTime($val35, $val43["openid"], $val47);
        $val54 = m("member")->getMember($val43["openid"]);
        $this->commission($val47, $val0->member, $val54);
        $val21 = trim($val47["respurl"]);
        if (empty($val21)) {
            if ($val54["isagent"] == 1 && $val54["status"] == 1) {
                $val21 = $_W["siteroot"] . "app/index.php?i={$_W['uniacid']}&c=entry&m=ewei_shop&do=plugin&p=commission&method=myshop&mid=" . $val54["id"];
            } else {
                $val21 = $_W["siteroot"] . "app/index.php?i={$_W['uniacid']}&c=entry&m=ewei_shop&do=shop&mid=" . $val54["id"];
            }
        }
        if ($val47["resptype"] == '0') {
            if (!empty($val47["resptitle"])) {
                $val74 = array(array("title" => $val47["resptitle"], "description" => $val47["respdesc"], "picurl" => tomedia($val47["respthumb"]), "url" => $val21));
                return $val0->respNews($val74);
            }
        }
        if ($val47["resptype"] == "1") {
            if (!empty($val47["resptext"])) {
                return $val0->respText($val47["resptext"]);
            }
        }
        return $this->responseEmpty();
    }
    private function responseSubscribe($val0)
    {
        global $_W;
        $val35 = $val0->message["from"];
        $val88 = explode("_", $val0->message["eventkey"]);
        $val37 = isset($val88[1]) ? $val88[1] : '';
        $val39 = $val0->message["ticket"];
        $val95 = $val0->member;
        if (empty($val39)) {
            return $this->responseDefault($val0);
        }
        $val43 = $this->model->getQRByTicket($val39);
        if (empty($val43)) {
            return $this->responseDefault($val0);
        }
        $val47 = pdo_fetch("select * from " . tablename("ewei_shop_poster") . " where type=4 and isdefault=1 and uniacid=:uniacid limit 1", array(":uniacid" => $_W["uniacid"]));
        if (empty($val47)) {
            return $this->responseDefault($val0);
        }
        if ($val95["isnew"]) {
            pdo_update("ewei_shop_poster", array("follows" => $val47["follows"] + 1), array("id" => $val47["id"]));
        }
        $val54 = m("member")->getMember($val43["openid"]);
        $val112 = pdo_fetch("select * from " . tablename("ewei_shop_poster_log") . " where openid=:openid and posterid=:posterid and uniacid=:uniacid limit 1", array(":openid" => $val35, ":posterid" => $val47["id"], ":uniacid" => $_W["uniacid"]));
        if (empty($val112) && $val35 != $val43["openid"]) {
            $val112 = array("uniacid" => $_W["uniacid"], "posterid" => $val47["id"], "openid" => $val35, "from_openid" => $val43["openid"], "subcredit" => $val47["subcredit"], "submoney" => $val47["submoney"], "reccredit" => $val47["reccredit"], "recmoney" => $val47["recmoney"], "createtime" => time());
            pdo_insert("ewei_shop_poster_log", $val112);
            $val112["id"] = pdo_insertid();
            $val130 = $val47["subpaycontent"];
            if (empty($val130)) {
                $val130 = "您通过 [nickname] 的推广二维码扫码关注的奖励";
            }
            $val130 = str_replace("[nickname]", $val54["nickname"], $val130);
            $val137 = $val47["recpaycontent"];
            if (empty($val137)) {
                $val137 = "推荐 [nickname] 扫码关注的奖励";
            }
            $val137 = str_replace("[nickname]", $val95["nickname"], $val130);
            if ($val47["subcredit"] > 0) {
                m("member")->setCredit($val35, "credit1", $val47["subcredit"], array(0, "扫码关注积分+" . $val47["subcredit"]));
            }
            if ($val47["submoney"] > 0) {
                $val149 = $val47["submoney"];
                if ($val47["paytype"] == 1) {
                    $val149 *= 100;
                }
                m("finance")->pay($val35, $val47["paytype"], $val149, '', $val130);
            }
            if ($val47["reccredit"] > 0) {
                m("member")->setCredit($val43["openid"], "credit1", $val47["reccredit"], array(0, "推荐扫码关注积分+" . $val47["reccredit"]));
            }
            if ($val47["recmoney"] > 0) {
                $val149 = $val47["recmoney"];
                if ($val47["paytype"] == 1) {
                    $val149 *= 100;
                }
                m("finance")->pay($val43["openid"], $val47["paytype"], $val149, '', $val137);
            }
            $val170 = false;
            $val171 = false;
            $val172 = p("coupon");
            if ($val172) {
                if (!empty($val47["reccouponid"]) && $val47["reccouponnum"] > 0) {
                    $val176 = $val172->getCoupon($val47["reccouponid"]);
                    if (!empty($val176)) {
                        $val170 = true;
                    }
                }
                if (!empty($val47["subcouponid"]) && $val47["subcouponnum"] > 0) {
                    $val182 = $val172->getCoupon($val47["subcouponid"]);
                    if (!empty($val182)) {
                        $val171 = true;
                    }
                }
            }
            if (!empty($val47["subtext"])) {
                $val187 = $val47["subtext"];
                $val187 = str_replace("[nickname]", $val95["nickname"], $val187);
                $val187 = str_replace("[credit]", $val47["reccredit"], $val187);
                $val187 = str_replace("[money]", $val47["recmoney"], $val187);
                if ($val176) {
                    $val187 = str_replace("[couponname]", $val176["couponname"], $val187);
                    $val187 = str_replace("[couponnum]", $val47["reccouponnum"], $val187);
                }
                if (!empty($val47["templateid"])) {
                    m("message")->sendTplNotice($val43["openid"], $val47["templateid"], array("first" => array("value" => "推荐关注奖励到账通知", "color" => "#4a5077"), "keyword1" => array("value" => "推荐奖励", "color" => "#4a5077"), "keyword2" => array("value" => $val187, "color" => "#4a5077"), "remark" => array("value" => "\r\n谢谢您对我们的支持！", "color" => "#4a5077")), '');
                } else {
                    m("message")->sendCustomNotice($val43["openid"], $val187);
                }
            }
            if (!empty($val47["entrytext"])) {
                $val212 = $val47["entrytext"];
                $val212 = str_replace("[nickname]", $val54["nickname"], $val212);
                $val212 = str_replace("[credit]", $val47["subcredit"], $val212);
                $val212 = str_replace("[money]", $val47["submoney"], $val212);
                if ($val182) {
                    $val212 = str_replace("[couponname]", $val182["couponname"], $val212);
                    $val212 = str_replace("[couponnum]", $val47["subcouponnum"], $val212);
                }
                if (!empty($val47["templateid"])) {
                    m("message")->sendTplNotice($val35, $val47["templateid"], array("first" => array("value" => "关注奖励到账通知", "color" => "#4a5077"), "keyword1" => array("value" => "关注奖励", "color" => "#4a5077"), "keyword2" => array("value" => $val212, "color" => "#4a5077"), "remark" => array("value" => "\r\n谢谢您对我们的支持！", "color" => "#4a5077")), '');
                } else {
                    m("message")->sendCustomNotice($val35, $val212);
                }
            }
            $val236 = array();
            if ($val170) {
                $val236["reccouponid"] = $val47["reccouponid"];
                $val236["reccouponnum"] = $val47["reccouponnum"];
                $val172->poster($val54, $val47["reccouponid"], $val47["reccouponnum"]);
            }
            if ($val171) {
                $val236["subcouponid"] = $val47["subcouponid"];
                $val236["subcouponnum"] = $val47["subcouponnum"];
                $val172->poster($val95, $val47["subcouponid"], $val47["subcouponnum"]);
            }
            if (!empty($val236)) {
                pdo_update("ewei_shop_poster_log", $val236, array("id" => $val112["id"]));
            }
        }
        $this->commission($val47, $val95, $val54);
        $val21 = trim($val47["respurl"]);
        if (empty($val21)) {
            if ($val54["isagent"] == 1 && $val54["status"] == 1) {
                $val21 = $_W["siteroot"] . "app/index.php?i={$_W["uniacid"]}&c=entry&m=ewei_shop&do=plugin&p=commission&method=myshop&mid=" . $val54["id"];
            } else {
                $val21 = $_W["siteroot"] . "app/index.php?i={$_W["uniacid"]}&c=entry&m=ewei_shop&do=shop&mid=" . $val54["id"];
            }
        }
        if ($val47["resptype"] == '0') {
            if (!empty($val47["resptitle"])) {
                $val74 = array(array("title" => $val47["resptitle"], "description" => $val47["respdesc"], "picurl" => tomedia($val47["respthumb"]), "url" => $val21));
                return $val0->respNews($val74);
            }
        }
        if ($val47["resptype"] == "1") {
            if (!empty($val47["resptext"])) {
                return $val0->respText($val47["resptext"]);
            }
        }
        return $this->responseEmpty();
    }
    private function commission($val47, $val95, $val54)
    {
        $val287 = time();
        $val288 = p("commission");
        if ($val288) {
            $val290 = $val288->getSet();
            if (!empty($val290)) {
                if ($val95["isagent"] != 1) {
                    if ($val54["isagent"] == 1 && $val54["status"] == 1) {
                        if (!empty($val47["bedown"])) {
                            if (empty($val95["agentid"])) {
                                if (empty($val95["fixagentid"])) {
                                    pdo_update("ewei_shop_member", array("agentid" => $val54["id"], "childtime" => $val287), array("id" => $val95["id"]));
                                    $val95["agentid"] = $val54["id"];
                                    $val288->sendMessage($val54["openid"], array("nickname" => $val95["nickname"], "childtime" => $val287), TM_COMMISSION_AGENT_NEW);
                                    $val288->upgradeLevelByAgent($val54["id"]);
                                }
                            }
                            if (!empty($val47["beagent"])) {
                                $val309 = intval($val290["become_check"]);
                                pdo_update("ewei_shop_member", array("isagent" => 1, "status" => $val309, "agenttime" => $val287), array("id" => $val95["id"]));
                                if ($val309 == 1) {
                                    $val288->sendMessage($val95["openid"], array("nickname" => $val95["nickname"], "agenttime" => $val287), TM_COMMISSION_BECOME);
                                    $val288->upgradeLevelByAgent($val54["id"]);
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}