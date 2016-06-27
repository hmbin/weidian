<?php
if (!defined("IN_IA")) {
    exit("Access Denied");
}
require IA_ROOT . "/addons/ewei_shop/defines.php";
require EWEI_SHOP_INC . "plugin/plugin_processor.php";
class CouponProcessor extends PluginProcessor
{
    public function __construct()
    {
        parent::__construct("coupon");
    }
    public function respond($val0 = null)
    {
        global $_W;
        $val2 = $val0->message;
        $val4 = $val0->message["content"];
        $val6 = strtolower($val2["msgtype"]);
        $val8 = strtolower($val2["event"]);
        if ($val6 == "text" || $val8 == "click") {
            return $this->respondText($val0);
        }
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
    function replaceCoupon($val14, $val15, $val16, $val17)
    {
        $val18 = array("pwdask" => "请输入优惠券口令: ", "pwdfail" => "很抱歉，您猜错啦，继续猜~", "pwdsuc" => "恭喜你，猜中啦！优惠券已发到您账户了! ", "pwdfull" => "很抱歉，您已经没有机会啦~ ", "pwdown" => "您已经参加过啦,等待下次活动吧~", "pwdexit" => '0', "pwdexitstr" => "好的，等待您下次来玩!");
        foreach ($val18 as $val20 => $val21) {
            if (empty($val14[$val20])) {
                $val14[$val20] = $val21;
            } else {
                $val14[$val20] = str_replace("[nickname]", $val15["nickname"], $val14[$val20]);
                $val14[$val20] = str_replace("[couponname]", $val14["couponname"], $val14[$val20]);
                $val14[$val20] = str_replace("[times]", $val16, $val14[$val20]);
                $val14[$val20] = str_replace("[lasttimes]", $val17, $val14[$val20]);
            }
        }
        return $val14;
    }
    function getGuess($val14, $val49)
    {
        global $_W;
        $val17 = 1;
        $val16 = 0;
        $val53 = pdo_fetch("select id,times from " . tablename("ewei_shop_coupon_guess") . " where couponid=:couponid and openid=:openid and pwdkey=:pwdkey and uniacid=:uniacid limit 1 ", array(":couponid" => $val14["id"], ":openid" => $val49, ":pwdkey" => $val14["pwdkey"], ":uniacid" => $_W["uniacid"]));
        if ($val14["pwdtimes"] > 0) {
            $val16 = $val53["times"];
            $val17 = $val14["pwdtimes"] - intval($val16);
            if ($val17 <= 0) {
                $val17 = 0;
            }
        }
        return array("times" => $val16, "lasttimes" => $val17);
    }
    function respondText($val0)
    {
        global $_W;
        @session_start();
        $val4 = $val0->message["content"];
        $val49 = $val0->message["from"];
        $val15 = m("member")->getMember($val49);
        $val76 = $val4;
        if (isset($_SESSION["ewei_shop_coupon_key"])) {
            $val76 = $_SESSION["ewei_shop_coupon_key"];
        } else {
            $_SESSION["ewei_shop_coupon_key"] = $val4;
        }
        $val14 = pdo_fetch("select id,couponname,pwdkey,pwdask,pwdsuc,pwdfail,pwdfull,pwdtimes,pwdurl,pwdwords,pwdown,pwdexit,pwdexitstr from " . tablename("ewei_shop_coupon") . " where pwdkey=:pwdkey and uniacid=:uniacid limit 1", array(":uniacid" => $_W["uniacid"], ":pwdkey" => $val76));
        $val86 = explode(",", $val14["pwdwords"]);
        if (empty($val14)) {
            $val0->endContext();
            unset($_SESSION["ewei_shop_coupon_key"]);
            return $this->responseEmpty();
        }
        if (!$val0->inContext) {
            $val93 = pdo_fetch("select id,times from " . tablename("ewei_shop_coupon_guess") . " where couponid=:couponid and openid=:openid and pwdkey=:pwdkey and ok=1 and uniacid=:uniacid limit 1 ", array(":couponid" => $val14["id"], ":openid" => $val49, ":pwdkey" => $val14["pwdkey"], ":uniacid" => $_W["uniacid"]));
            if (!empty($val93)) {
                $val53 = $this->getGuess($val14, $val49);
                $val14 = $this->replaceCoupon($val14, $val15, $val53["times"], $val53["lasttimes"]);
                $val0->endContext();
                unset($_SESSION["ewei_shop_coupon_key"]);
                return $val0->respText($val14["pwdown"]);
            }
            $val53 = $this->getGuess($val14, $val49);
            $val14 = $this->replaceCoupon($val14, $val15, $val53["times"], $val53["lasttimes"]);
            if ($val53["lasttimes"] <= 0) {
                $val0->endContext();
                unset($_SESSION["ewei_shop_coupon_key"]);
                return $val0->respText($val14["pwdfull"]);
            }
            $val0->beginContext();
            return $val0->respText($val14["pwdask"]);
        } else {
            if ($val4 == $val14["pwdexit"]) {
                unset($_SESSION["ewei_shop_coupon_key"]);
                $val0->endContext();
                $val53 = $this->getGuess($val14, $val49);
                $val14 = $this->replaceCoupon($val14, $val15, $val53["times"], $val53["lasttimes"]);
                return $val0->respText($val14["pwdexitstr"]);
            }
            $val53 = pdo_fetch("select id,times from " . tablename("ewei_shop_coupon_guess") . " where couponid=:couponid and openid=:openid and pwdkey=:pwdkey and uniacid=:uniacid limit 1 ", array(":couponid" => $val14["id"], ":openid" => $val49, ":pwdkey" => $val14["pwdkey"], ":uniacid" => $_W["uniacid"]));
            $val142 = in_array($val4, $val86);
            if (empty($val53)) {
                $val53 = array("uniacid" => $_W["uniacid"], "couponid" => $val14["id"], "openid" => $val49, "times" => 1, "pwdkey" => $val14["pwdkey"], "ok" => $val142 ? 1 : 0);
                pdo_insert("ewei_shop_coupon_guess", $val53);
            } else {
                pdo_update("ewei_shop_coupon_guess", array("times" => $val53["times"] + 1, "ok" => $val142 ? 1 : 0), array("id" => $val53["id"]));
            }
            $val156 = time();
            if ($val142) {
                $val158 = array("uniacid" => $_W["uniacid"], "openid" => $val49, "logno" => m("common")->createNO("coupon_log", "logno", "CC"), "couponid" => $val14["id"], "status" => 1, "paystatus" => -1, "creditstatus" => -1, "createtime" => $val156, "getfrom" => 5);
                pdo_insert("ewei_shop_coupon_log", $val158);
                $val164 = array("uniacid" => $_W["uniacid"], "openid" => $val49, "couponid" => $val14["id"], "gettype" => 5, "gettime" => $val156);
                pdo_insert("ewei_shop_coupon_data", $val164);
                unset($_SESSION["ewei_shop_coupon_key"]);
                $val0->endContext();
                $val172 = $this->model->getSet();
                $val174 = $this->model->getCoupon($val14["id"]);
                $this->model->sendMessage($val174, 1, $val15, $val172["templateid"]);
                $val53 = $this->getGuess($val14, $val49);
                $val14 = $this->replaceCoupon($val14, $val15, $val53["times"], $val53["lasttimes"]);
                return $val0->respText($val14["pwdsuc"]);
            } else {
                $val53 = $this->getGuess($val14, $val49);
                $val14 = $this->replaceCoupon($val14, $val15, $val53["times"], $val53["lasttimes"]);
                if ($val53["lasttimes"] <= 0) {
                    $val0->endContext();
                    unset($_SESSION["ewei_shop_coupon_key"]);
                    return $val0->respText($val14["pwdfull"]);
                }
                return $val0->respText($val14["pwdfail"]);
            }
        }
    }
}