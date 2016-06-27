<?php
if (!defined("IN_IA")) {
    exit("Access Denied");
}
require IA_ROOT . "/addons/ewei_shop/defines.php";
require EWEI_SHOP_INC . "plugin/plugin_processor.php";
class CreditshopProcessor extends PluginProcessor
{
    public function __construct()
    {
        parent::__construct("creditshop");
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
            if (!$val0->inContext) {
                $val0->beginContext();
                return $val0->respText("请输入兑换码:");
            } else {
                if ($val0->inContext && is_numeric($val6)) {
                    $val24 = pdo_fetch("select * from " . tablename("ewei_shop_creditshop_log") . " where eno=:eno and uniacid=:uniacid  limit 1", array(":eno" => $val6, ":uniacid" => $_W["uniacid"]));
                    if (empty($val24)) {
                        return $val0->respText("未找到要兑换码,请重新输入!");
                    }
                    $val29 = $val24["id"];
                    if (empty($val24)) {
                        return $val0->respText("未找到要兑换码,请重新输入!");
                    }
                    if (empty($val24["status"])) {
                        return $val0->respText("无效兑换记录!");
                    }
                    if ($val24["status"] >= 3) {
                        return $val0->respText("此记录已兑换过了!");
                    }
                    $val37 = m("member")->getMember($val24["openid"]);
                    $val39 = $this->model->getGoods($val24["goodsid"], $val37);
                    if (empty($val39["id"])) {
                        return $val0->respText("商品记录不存在!");
                    }
                    if (empty($val39["isverify"])) {
                        $val0->endContext();
                        return $val0->respText("此商品不支持线下兑换!");
                    }
                    if (!empty($val39["type"])) {
                        if ($val24["status"] <= 1) {
                            return $val0->respText("未中奖，不能兑换!");
                        }
                    }
                    if ($val39["money"] > 0 && empty($val24["paystatus"])) {
                        return $val0->respText("未支付，无法进行兑换!");
                    }
                    if ($val39["dispatch"] > 0 && empty($val24["dispatchstatus"])) {
                        return $val0->respText("未支付运费，无法进行兑换!");
                    }
                    $val56 = explode(",", $val39["storeids"]);
                    if (!empty($val58)) {
                        if (!empty($val14["storeid"])) {
                            if (!in_array($val14["storeid"], $val58)) {
                                return $val0->respText("您无此门店的兑换权限!");
                            }
                        }
                    }
                    $val63 = time();
                    pdo_update("ewei_shop_creditshop_log", array("status" => 3, "usetime" => $val63, "verifyopenid" => $val4), array("id" => $val24["id"]));
                    $this->model->sendMessage($val29);
                    $val0->endContext();
                    return $val0->respText("兑换成功!");
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