<?php
if (!defined("IN_IA")) {
    exit("Access Denied");
}
if (!class_exists("YunpayModel")) {
    class YunpayModel extends PluginModel {
        function getYunpay() {
            global $_W;
            $val1 = pdo_fetch("select * from " . tablename("ewei_shop_sysset") . " where uniacid=:uniacid limit 1", array(
                ":uniacid" => $_W["uniacid"]
            ));
            $val3 = unserialize($val1["sets"]);
            return $val3["pay"]["yunpay"];
        }
        function isYunpayNotify($val6) {
            global $_W;
            $val8 = $this->getYunpay();
            if (!isset($val8) or !$val8["switch"]) {
                return false;
            }
            $val12 = $val6["i1"] . $val6["i2"] . $val8["partner"] . $val8["secret"];
            $val17 = md5($val12);
            if ($val17 != $val6["i3"]) {
                return false;
            } else {
                return true;
            }
        }
        public function yunpay_build($val21, $val8 = array() , $val23 = 0, $val24 = '') {
            global $_W;
            $val26 = $val21["tid"] . ":" . $_W["uniacid"] . ":" . $val23;
            if (empty($val23)) {
                $val31 = $_W["siteroot"] . "addons/ewei_shop/plugin/yunpay/notify.php";
                $val33 = $_W["siteroot"] . "app/index.php?i={$_W["uniacid"]}&c=entry&m=ewei_shop&do=order&p=pay&op=returnyunpay&openid=" . $val24;
            } else {
                $val31 = $_W["siteroot"] . "addons/ewei_shop/plugin/yunpay/notify.php";
                $val33 = $_W["siteroot"] . "app/index.php?i={$_W["uniacid"]}&c=entry&m=ewei_shop&do=member&p=recharge&op=returnyunpay&openid=" . $val24;
            }
            $val43 = $val26;
            $val45 = $val21["title"];
            $val47 = $val21["fee"];
            $val49 = $_W["uniacid"] . ":" . $val23;
            $val52 = "";
            $val53 = "";
            $val54 = array(
                "partner" => trim($val8["partner"]) ,
                "seller_email" => $val8["account"],
                "out_trade_no" => $val43,
                "subject" => $val45,
                "total_fee" => floor($val47) ,
                "body" => $val49,
                "nourl" => $val31,
                "reurl" => $val33,
                "orurl" => $val52,
                "orimg" => $val53
            );
            foreach ($val54 as $val66) {
                $val67 = $val66;
            }
            $val69 = md5($val67['i2eapi'] . $val8['secret']);
            $val71 = '<form name="yunsubmit" action="http://pay.yunpay.net.cn/i2eorder/yunpay/" accept-charset="utf-8" method="get">
			<input type="hidden" name="body" value='. $val54['body'] . '/>
			<input type="hidden" name="out_trade_no" value=' . $val54['out_trade_no'] . '/>
			<input type="hidden" name="partner" value='. $val54['partner'] . '/>
			<input type="hidden" name="seller_email" value='. $val54['seller_email'] . '/>
			<input type="hidden" name="subject" value='. $val54['subject'] . '/>
			<input type="hidden" name="total_fee" value=' . $val54['total_fee'] . '/>
			<input type="hidden" name="nourl" value=' . $val54['nourl'] . '/>
			<input type="hidden" name="reurl" value='. $val54['reurl'] . '/>
			<input type="hidden" name="orurl" value=' . $val54['orurl'] . '/>
			<input type="hidden" name="orimg" value=' . $val54['orimg'] . '/>
			<input type="hidden" name="sign" value=' . $val69 . '/></form>
			<script>document.forms["yunsubmit"].submit();</script>';
            return $val71;
        }
        function perms() {
            return array(
                "yunpay" => array(
                    "text" => $this->getName() ,
                    "isplugin" => true,
                    "child" => array(
                        "yunpay" => array(
                            "text" => "批量上假2"
                        )
                    )
                )
            );
        }
    }
} ?>
