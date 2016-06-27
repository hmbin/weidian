<?php
if (!defined("IN_IA")) {
    exit("Access Denied");
}
require IA_ROOT . "/addons/ewei_shop/defines.php";
require EWEI_SHOP_INC . "plugin/plugin_processor.php";
class PosteraProcessor extends PluginProcessor
{
    public function __construct()
    {
        parent::__construct("postera");
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
        $val21 = $_W["siteroot"] . "app/index.php?i=" . $_W["uniacid"] . "&c=entry&m=ewei_shop&do=plugin&p=postera&method=build&timestamp=" . time();
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
        $val47 = pdo_fetch("select * from " . tablename("ewei_shop_postera") . " where id=:id and uniacid=:uniacid limit 1", array(":id" => $val43["posterid"], ":uniacid" => $_W["uniacid"]));
        if (empty($val47)) {
            return $this->responseDefault($val0);
        }
        $val52 = m("member")->getMember($val43["openid"]);
        $this->commission($val47, $val0->member, $val52);
        $val21 = trim($val47["respurl"]);
        if (empty($val21)) {
            if ($val52["isagent"] == 1 && $val52["status"] == 1) {
                $val21 = $_W["siteroot"] . "app/index.php?i={$_W["uniacid"]}&c=entry&m=ewei_shop&do=plugin&p=commission&method=myshop&mid=" . $val52["id"];
            } else {
                $val21 = $_W["siteroot"] . "app/index.php?i={$_W["uniacid"]}&c=entry&m=ewei_shop&do=shop&mid=" . $val52["id"];
            }
        }
        if ($val47["resptype"] == '0') {
            if (!empty($val47["resptitle"])) {
                $val72 = array(array("title" => $val47["resptitle"], "description" => $val47["respdesc"], "picurl" => tomedia($val47["respthumb"]), "url" => $val21));
                return $val0->respNews($val72);
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
        $val86 = explode("_", $val0->message["eventkey"]);
        $val37 = isset($val86[1]) ? $val86[1] : '';
        $val39 = $val0->message["ticket"];
        $val93 = $val0->member;
        if (empty($val39)) {
            return $this->responseDefault($val0);
        }

        $val43 = $this->model->getQRByTicket($val39);
        if (empty($val43)) {
            return $this->responseDefault($val0);
        }
        $val47 = pdo_fetch("select * from " . tablename("ewei_shop_postera") . " where id=:id and uniacid=:uniacid limit 1", array(":id" => $val43["posterid"], ":uniacid" => $_W["uniacid"]));
        if (empty($val47)) {
            return $this->responseDefault($val0);
        }
        $val52 = m("member")->getMember($val43["openid"]);
        $val108 = pdo_fetch("select * from " . tablename("ewei_shop_postera_log") . " where openid=:openid and posterid=:posterid and uniacid=:uniacid limit 1", array(":openid" => $val35, ":posterid" => $val47["id"], ":uniacid" => $_W["uniacid"]));
        if (empty($val108) && $val35 != $val43["openid"]) {
            $val108 = array("uniacid" => $_W["uniacid"], "posterid" => $val47["id"], "openid" => $val35, "from_openid" => $val43["openid"], "subcredit" => $val47["subcredit"], "submoney" => $val47["submoney"], "reccredit" => $val47["reccredit"], "recmoney" => $val47["recmoney"], "createtime" => time());
            pdo_insert("ewei_shop_postera_log", $val108);
            $val108["id"] = pdo_insertid();
            $val126 = $val47["subpaycontent"];
            if (empty($val126)) {
                $val126 = "您通过 [nickname] 的推广二维码扫码关注的奖励";
            }
            $val126 = str_replace("[nickname]", $val52["nickname"], $val126);
            $val133 = $val47["recpaycontent"];
            if (empty($val133)) {
                $val133 = "推荐 [nickname] 扫码关注的奖励";
            }
            $val133 = str_replace("[nickname]", $val93["nickname"], $val126);
            if ($val47["subcredit"] > 0) {
                m("member")->setCredit($val35, "credit1", $val47["subcredit"], array(0, "扫码关注积分+" . $val47["subcredit"]));
            }
            if ($val47["submoney"] > 0) {
                $val145 = $val47["submoney"];
                if ($val47["paytype"] == 1) {
                    $val145 *= 100;
                }
                m("finance")->pay($val35, $val47["paytype"], $val145, '', $val126);
            }
            if ($val47["reccredit"] > 0) {
                m("member")->setCredit($val43["openid"], "credit1", $val47["reccredit"], array(0, "推荐扫码关注积分+" . $val47["reccredit"]));
            }
            if ($val47["recmoney"] > 0) {
                $val145 = $val47["recmoney"];
                if ($val47["paytype"] == 1) {
                    $val145 *= 100;
                }
                m("finance")->pay($val43["openid"], $val47["paytype"], $val145, '', $val133);
            }
            $val166 = false;
            $val167 = false;
            $val168 = p("coupon");
            if ($val168) {
                if (!empty($val47["reccouponid"]) && $val47["reccouponnum"] > 0) {
                    $val172 = $val168->getCoupon($val47["reccouponid"]);
                    if (!empty($val172)) {
                        $val166 = true;
                    }
                }
                if (!empty($val47["subcouponid"]) && $val47["subcouponnum"] > 0) {
                    $val178 = $val168->getCoupon($val47["subcouponid"]);
                    if (!empty($val178)) {
                        $val167 = true;
                    }
                }
            }
            if (!empty($val47["subtext"])) {
                $val183 = $val47["subtext"];
                $val183 = str_replace("[nickname]", $val93["nickname"], $val183);
                $val183 = str_replace("[credit]", $val47["reccredit"], $val183);
                $val183 = str_replace("[money]", $val47["recmoney"], $val183);
                if ($val172) {
                    $val183 = str_replace("[couponname]", $val172["couponname"], $val183);
                    $val183 = str_replace("[couponnum]", $val47["reccouponnum"], $val183);
                }
                if (!empty($val47["templateid"])) {
                    m("message")->sendTplNotice($val43["openid"], $val47["templateid"], array("first" => array("value" => "推荐关注奖励到账通知", "color" => "#4a5077"), "keyword1" => array("value" => "推荐奖励", "color" => "#4a5077"), "keyword2" => array("value" => $val183, "color" => "#4a5077"), "remark" => array("value" => "\r\n谢谢您对我们的支持！", "color" => "#4a5077")), '');
                } else {
                    m("message")->sendCustomNotice($val43["openid"], $val183);
                }
            }
            if (!empty($val47["entrytext"])) {
                $val208 = $val47["entrytext"];
                $val208 = str_replace("[nickname]", $val52["nickname"], $val208);
                $val208 = str_replace("[credit]", $val47["subcredit"], $val208);
                $val208 = str_replace("[money]", $val47["submoney"], $val208);
                if ($val178) {
                    $val208 = str_replace("[couponname]", $val178["couponname"], $val208);
                    $val208 = str_replace("[couponnum]", $val47["subcouponnum"], $val208);
                }
                if (!empty($val47["templateid"])) {
                    m("message")->sendTplNotice($val35, $val47["templateid"], array("first" => array("value" => "关注奖励到账通知", "color" => "#4a5077"), "keyword1" => array("value" => "关注奖励", "color" => "#4a5077"), "keyword2" => array("value" => $val208, "color" => "#4a5077"), "remark" => array("value" => "\r\n谢谢您对我们的支持！", "color" => "#4a5077")), '');
                } else {
                    m("message")->sendCustomNotice($val35, $val208);
                }
            }
            $val232 = array();
            if ($val166) {
                $val232["reccouponid"] = $val47["reccouponid"];
                $val232["reccouponnum"] = $val47["reccouponnum"];
                $val168->poster($val52, $val47["reccouponid"], $val47["reccouponnum"]);
            }
            if ($val167) {
                $val232["subcouponid"] = $val47["subcouponid"];
                $val232["subcouponnum"] = $val47["subcouponnum"];
                $val168->poster($val93, $val47["subcouponid"], $val47["subcouponnum"]);
            }
            if (!empty($val232)) {
                pdo_update("ewei_shop_postera_log", $val232, array("id" => $val108["id"]));
            }
        }
        $this->commission($val47, $val93, $val52);
        $val21 = trim($val47["respurl"]);
        if (empty($val21)) {
            if ($val52["isagent"] == 1 && $val52["status"] == 1) {
                $val21 = $_W["siteroot"] . "app/index.php?i={$_W["uniacid"]}&c=entry&m=ewei_shop&do=plugin&p=commission&method=myshop&mid=" . $val52["id"];
            } else {
                $val21 = $_W["siteroot"] . "app/index.php?i={$_W["uniacid"]}&c=entry&m=ewei_shop&do=shop&mid=" . $val52["id"];
            }
        }
        if ($val47["resptype"] == '0') {
            if (!empty($val47["resptitle"])) {
                $val72 = array(array("title" => $val47["resptitle"], "description" => $val47["respdesc"], "picurl" => tomedia($val47["respthumb"]), "url" => $val21));
                return $val0->respNews($val72);
            }
        }
        if ($val47["resptype"] == "1") {
            if (!empty($val47["resptext"])) {
                return $val0->respText($val47["resptext"]);
            }
        }
        return $this->responseEmpty();
    }
    private function commission($val47, $val93, $val52)
    {
        $val283 = time();
        $val284 = p("commission");
        if ($val284) {
            $val286 = $val284->getSet();
            if (!empty($val286)) {
                if ($val93["isagent"] != 1) {
                    if ($val52["isagent"] == 1 && $val52["status"] == 1) {
                        if (!empty($val47["bedown"])) {
                            if (empty($val93["agentid"])) {
                                if (empty($val93["fixagentid"])) {
                                    pdo_update("ewei_shop_member", array("agentid" => $val52["id"], "childtime" => $val283), array("id" => $val93["id"]));
                                    $val93["agentid"] = $val52["id"];
                                    $val284->sendMessage($val52["openid"], array("nickname" => $val93["nickname"], "childtime" => $val283), TM_COMMISSION_AGENT_NEW);
                                    $val284->upgradeLevelByAgent($val52["id"]);
                                }
                            }
                            if (!empty($val47["beagent"])) {
                                $val305 = intval($val286["become_check"]);
                                pdo_update("ewei_shop_member", array("isagent" => 1, "status" => $val305, "agenttime" => $val283), array("id" => $val93["id"]));
                                if ($val305 == 1) {
                                    $val284->sendMessage($val93["openid"], array("nickname" => $val93["nickname"], "agenttime" => $val283), TM_COMMISSION_BECOME);
                                    $val284->upgradeLevelByAgent($val52["id"]);
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}