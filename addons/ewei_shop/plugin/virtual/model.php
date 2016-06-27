<?php
if (!defined("IN_IA")) {
    exit("Access Denied");
}
if (!class_exists("VirtualModel")) {
    class VirtualModel extends PluginModel
    {
        public function updateGoodsStock($val0 = 0)
        {
            global $_W, $_GPC;
            $val3 = pdo_fetch("select virtual from " . tablename("ewei_shop_goods") . " where id=:id and type=3 and uniacid=:uniacid limit 1", array(":id" => $val0, ":uniacid" => $_W["uniacid"]));
            if (empty($val3)) {
                return;
            }
            $val7 = 0;
            if (!empty($val3["virtual"])) {
                $val7 = pdo_fetchcolumn("select count(*) from " . tablename("ewei_shop_virtual_data") . " where typeid=:typeid and uniacid=:uniacid and openid='' limit 1", array(":typeid" => $val3["virtual"], ":uniacid" => $_W["uniacid"]));
            } else {
                $val12 = array();
                $val13 = pdo_fetchall("select id, virtual from " . tablename("ewei_shop_goods_option") . " where goodsid={$val0}");
                foreach ($val13 as $val16) {
                    if (empty($val16["virtual"])) {
                        continue;
                    }
                    $val18 = pdo_fetchcolumn("select count(*) from " . tablename("ewei_shop_virtual_data") . " where typeid=:typeid and uniacid=:uniacid and openid='' limit 1", array(":typeid" => $val16["virtual"], ":uniacid" => $_W["uniacid"]));
                    pdo_update("ewei_shop_goods_option", array("stock" => $val18), array("id" => $val16["id"]));
                    if (!in_array($val16["virtual"], $val12)) {
                        $val12[] = $val16["virtual"];
                        $val7 += $val18;
                    }
                }
            }
            pdo_update("ewei_shop_goods", array("total" => $val7), array("id" => $val0));
        }
        public function updateStock($val31 = 0)
        {
            global $_W;
            $val33 = array();
            $val3 = pdo_fetchall("select id from " . tablename("ewei_shop_goods") . " where type=3 and virtual=:virtual and uniacid=:uniacid limit 1", array(":virtual" => $val31, ":uniacid" => $_W["uniacid"]));
            foreach ($val3 as $val38) {
                $val33[] = $val38["id"];
            }
            $val13 = pdo_fetchall("select id, goodsid from " . tablename("ewei_shop_goods_option") . " where virtual=:virtual and uniacid=:uniacid", array(":uniacid" => $_W["uniacid"], ":virtual" => $val31));
            foreach ($val13 as $val16) {
                if (!in_array($val16["goodsid"], $val33)) {
                    $val33[] = $val16["goodsid"];
                }
            }
            foreach ($val33 as $val51) {
                $this->updateGoodsStock($val51);
            }
        }
        public function pay($val53)
        {
            global $_W, $_GPC;
            $val3 = pdo_fetch("select id,goodsid,total,realprice from " . tablename("ewei_shop_order_goods") . " where  orderid=:orderid and uniacid=:uniacid limit 1", array(":uniacid" => $_W["uniacid"], ":orderid" => $val53["id"]));
            $val38 = pdo_fetch("select id,credit,sales,salesreal from " . tablename("ewei_shop_goods") . " where  id=:id and uniacid=:uniacid limit 1", array(":uniacid" => $_W["uniacid"], ":id" => $val3["goodsid"]));
            $val62 = pdo_fetchall("SELECT id,typeid,fields FROM " . tablename("ewei_shop_virtual_data") . " WHERE typeid=:typeid and openid=:openid and uniacid=:uniacid order by rand() limit " . $val3["total"], array(":openid" => '', ":typeid" => $val53["virtual"], ":uniacid" => $_W["uniacid"]));
            $val66 = pdo_fetch("select fields from " . tablename("ewei_shop_virtual_type") . " where id=:id and uniacid=:uniacid limit 1 ", array(":id" => $val53["virtual"], ":uniacid" => $_W["uniacid"]));
            $val69 = iunserializer($val66["fields"], true);
            $val71 = array();
            $val72 = array();
            foreach ($val62 as $val74) {
                $val71[] = $val74["fields"];
                $val77 = array();
                $val78 = iunserializer($val74["fields"]);
                foreach ($val78 as $val81 => $val82) {
                    $val77[] = $val69[$val81] . ": " . $val82;
                }
                $val72[] = implode(" ", $val77);
                pdo_update("ewei_shop_virtual_data", array("openid" => $val53["openid"], "orderid" => $val53["id"], "ordersn" => $val53["ordersn"], "price" => round($val3["realprice"] / $val3["total"], 2), "usetime" => time()), array("id" => $val74["id"]));
                pdo_update("ewei_shop_virtual_type", "usedata=usedata+1", array("id" => $val74["typeid"]));
                $this->updateStock($val74["typeid"]);
            }
            $val72 = implode("\r\n", $val72);
            $val71 = "[" . implode(",", $val71) . "]";
            $val101 = time();
            pdo_update("ewei_shop_order", array("virtual_info" => $val71, "virtual_str" => $val72, "status" => "3", "paytime" => $val101, "sendtime" => $val101, "finishtime" => $val101), array("id" => $val53["id"]));
            $val108 = $val3["total"] * $val38["credit"];
            if ($val108 > 0) {
                $val112 = m("common")->getSysset("shop");
                m("member")->setCredit($val53["openid"], "credit1", $val108, array(0, $val112["name"] . "购物积分 订单号: " . $val53["ordersn"]));
            }
            $val117 = pdo_fetchcolumn("select ifnull(sum(total),0) from " . tablename("ewei_shop_order_goods") . " og " . " left join " . tablename("ewei_shop_order") . " o on o.id = og.orderid " . " where og.goodsid=:goodsid and o.status>=1 and o.uniacid=:uniacid limit 1", array(":goodsid" => $val38["id"], ":uniacid" => $_W["uniacid"]));
            pdo_update("ewei_shop_goods", array("salesreal" => $val117), array("id" => $val38["id"]));
            m("member")->upgradeLevel($val53["openid"]);
            m("notice")->sendOrderMessage($val53["id"]);
            if (p("coupon") && !empty($val53["couponid"])) {
                p("coupon")->backConsumeCoupon($val53["id"]);
            }
            if (p("commission")) {
                p("commission")->checkOrderPay($val53["id"]);
                p("commission")->checkOrderFinish($val53["id"]);
            }
        }
        public function perms()
        {
            return array("virtual" => array("text" => $this->getName(), "isplugin" => true, "child" => array("temp" => array("text" => "模板", "view" => "浏览", "add" => "添加-log", "edit" => "修改-log", "delete" => "删除-log"), "data" => array("text" => "数据", "view" => "浏览", "add" => "添加-log", "edit" => "修改-log", "delete" => "删除-log", "import" => "导入-log", "export" => "导出已使用数据-log"), "category" => array("text" => "分类", "view" => "浏览", "add" => "添加-log", "edit" => "修改-log", "delete" => "删除-log"))));
        }
    }
}