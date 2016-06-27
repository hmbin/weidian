<?php
if (!defined("IN_IA")) {
    exit("Access Denied");
}
require IA_ROOT . "/addons/ewei_shop/defines.php";
require EWEI_SHOP_INC . "plugin/plugin_processor.php";
class VerifyProcessor extends PluginProcessor
{
    public function __construct()
    {
        parent::__construct("verify");
    }
    public function respond($val0 = null)
    {
        global $_W;
        $val2 = $val0->message;
        $val4 = $val0->message["from"];
        $val6 = $val0->message["content"];
        $val8 = strtolower($val2["msgtype"]);
        $val10 = strtolower($val2["event"]);
        if ($val8 == "text" || $val10 == "click") {
            $val14 = pdo_fetch("select * from " . tablename("ewei_shop_saler") . " where openid=:openid and uniacid=:uniacid limit 1", array(":uniacid" => $_W["uniacid"], ":openid" => $val4));
            if (empty($val14)) {
                return $this->responseEmpty();
            }
            $val19 = m("common")->getSysset("trade");
            if (!$val0->inContext) {
                $val0->beginContext();
                return $val0->respText("请输入订单消费码:");
            } else {
                if ($val0->inContext && is_numeric($val6)) {
                    $val25 = pdo_fetch("select * from " . tablename("ewei_shop_order") . " where verifycode=:verifycode and uniacid=:uniacid  limit 1", array(":verifycode" => $val6, ":uniacid" => $_W["uniacid"]));
                    if (empty($val25)) {
                        return $val0->respText("未找到要核销的订单,请重新输入!");
                    }
                    $val30 = $val25["id"];
                    if (empty($val25["isverify"])) {
                        $val0->endContext();
                        return $val0->respText("订单无需核销!");
                    }
                    if (!empty($val25["verified"])) {
                        $val0->endContext();
                        return $val0->respText("此订单已核销，无需重复核销!");
                    }
                    if ($val25["status"] != 1) {
                        $val0->endContext();
                        return $val0->respText("订单未付款，无法核销!");
                    }
                    $val41 = array();
                    $val42 = pdo_fetchall("select og.goodsid,og.price,g.title,g.thumb,og.total,g.credit,og.optionid,g.isverify,g.storeids from " . tablename("ewei_shop_order_goods") . " og " . " left join " . tablename("ewei_shop_goods") . " g on g.id=og.goodsid " . " where og.orderid=:orderid and og.uniacid=:uniacid ", array(":uniacid" => $_W["uniacid"], ":orderid" => $val25["id"]));
                    foreach ($val42 as $val46) {
                        if (!empty($val46["storeids"])) {
                            $val41 = array_merge(explode(",", $val46["storeids"]), $val41);
                        }
                    }
                    if (!empty($val41)) {
                        if (!empty($val14["storeid"])) {
                            if (!in_array($val14["storeid"], $val41)) {
                                return $val0->respText("您无此门店的核销权限!");
                            }
                        }
                    }
                    $val56 = time();
                    pdo_update("ewei_shop_order", array("status" => 3, "sendtime" => $val56, "finishtime" => $val56, "verifytime" => $val56, "verified" => 1, "verifyopenid" => $val4, "verifystoreid" => $val14["storeid"]), array("id" => $val25["id"]));
                    m("notice")->sendOrderMessage($val30);
                    if (p("commission")) {
                        p("commission")->checkOrderFinish($val30);
                    }
                    $val0->endContext();
                    return $val0->respText("核销成功!");
                }
            }
        }
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
}