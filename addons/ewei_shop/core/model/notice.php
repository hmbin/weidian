<?php
if (!defined("IN_IA")) {
    exit("Access Denied");
}
class Ewei_DShop_Notice
{
    public function sendOrderMessage($val0 = '0', $val1 = false)
    {
        global $_W;
        if (empty($val0)) {
            return;
        }
        $val4 = pdo_fetch("select * from " . tablename("ewei_shop_order") . " where id=:id limit 1", array(":id" => $val0));
        if (empty($val4)) {
            return;
        }
        $val7 = $_W["siteroot"] . "app/index.php?i=" . $_W["uniacid"] . "&c=entry&m=ewei_shop&do=order&p=detail&id=" . $val0;
        if (strexists($val7, "/addons/ewei_shop/")) {
            $val7 = str_replace("/addons/ewei_shop/", "/", $val7);
        }
        if (strexists($val7, "/core/mobile/order/")) {
            $val7 = str_replace("/core/mobile/order/", "/", $val7);
        }
        $val17 = $val4["openid"];
        $val19 = pdo_fetchall("select g.id,g.title,og.realprice,og.total,og.price,og.optionname as optiontitle,g.noticeopenid,g.noticetype from " . tablename("ewei_shop_order_goods") . " og " . " left join " . tablename("ewei_shop_goods") . " g on g.id=og.goodsid " . " where og.uniacid=:uniacid and og.orderid=:orderid ", array(":uniacid" => $_W["uniacid"], ":orderid" => $val0));
        $val22 = '';
        foreach ($val19 as $val24) {
            $val22 .= "" . $val24["title"] . "( ";
            if (!empty($val24["optiontitle"])) {
                $val22 .= " 规格: " . $val24["optiontitle"];
            }
            $val22 .= " 单价: " . $val24["realprice"] / $val24["total"] . " 数量: " . $val24["total"] . " 总价: " . $val24["realprice"] . "); ";
        }
        $val35 = " 订单总价: " . $val4["price"] . "(包含运费:" . $val4["dispatchprice"] . ")";
        $val38 = m("member")->getMember($val17);
        $val40 = unserialize($val38["noticeset"]);
        if (!is_array($val40)) {
            $val40 = array();
        }
        $val44 = m("common")->getSysset();
        $val45 = $val44["shop"];
        $val47 = $val44["notice"];
        if ($val1) {
            $val50 = array('0' => "退款", "1" => "退货退款", "2" => "换货");
            if (!empty($val4["refundid"])) {
                $val52 = pdo_fetch("select * from " . tablename("ewei_shop_order_refund") . " where id=:id limit 1", array(":id" => $val4["refundid"]));
                if (empty($val52)) {
                    return;
                }
                if (empty($val52["status"])) {
                    $val56 = array("first" => array("value" => "您的" . $val50[$val52["rtype"]] . "申请已经提交！", "color" => "#4a5077"), "orderProductPrice" => array("title" => "退款金额", "value" => $val52["rtype"] == 3 ? "-" : "¥" . $val52["applyprice"] . "元", "color" => "#4a5077"), "orderProductName" => array("title" => "商品详情", "value" => $val22 . $val35, "color" => "#4a5077"), "orderName" => array("title" => "订单编号", "value" => $val4["ordersn"], "color" => "#4a5077"), "remark" => array("value" => "\r\n等待商家确认" . $val50[$val52["rtype"]] . "信息！", "color" => "#4a5077"));
                    if (!empty($val47["refund"]) && empty($val40["refund"])) {
                        m("message")->sendTplNotice($val17, $val47["refund"], $val56, $val7);
                    } else {
                        if (empty($val40["refund"])) {
                            m("message")->sendCustomNotice($val17, $val56, $val7);
                        }
                    }
                } else {
                    if ($val52["status"] == 3) {
                        $val76 = iunserializer($val52["refundaddress"]);
                        $val78 = "退货地址: " . $val76["province"] . " " . $val76["city"] . " " . $val76["area"] . " " . $val76["address"] . " 收件人: " . $val76["name"] . " (" . $val76["mobile"] . ")(" . $val76["tel"] . ") ";
                        $val56 = array("first" => array("value" => "您的" . $val50[$val52["rtype"]] . "申请已经通过！", "color" => "#4a5077"), "orderProductPrice" => array("title" => "退款金额", "value" => $val52["rtype"] == 3 ? "-" : "¥" . $val52["applyprice"] . "元", "color" => "#4a5077"), "orderProductName" => array("title" => "商品详情", "value" => $val22 . $val35, "color" => "#4a5077"), "orderName" => array("title" => "订单编号", "value" => $val4["ordersn"], "color" => "#4a5077"), "remark" => array("value" => "\r\n请您根据商家提供的退货地址将商品寄回！" . $val78 . "", "color" => "#4a5077"));
                        if (!empty($val47["refund"]) && empty($val40["refund"])) {
                            m("message")->sendTplNotice($val17, $val47["refund"], $val56, $val7);
                        } else {
                            if (empty($val40["refund"])) {
                                m("message")->sendCustomNotice($val17, $val56, $val7);
                            }
                        }
                    } else {
                        if ($val52["status"] == 5) {
                            if (!empty($val4["address"])) {
                                $val106 = iunserializer($val4["address_send"]);
                                if (!is_array($val106)) {
                                    $val106 = iunserializer($val4["address"]);
                                    if (!is_array($val106)) {
                                        $val106 = pdo_fetch("select id,realname,mobile,address,province,city,area from " . tablename("ewei_shop_member_address") . " where id=:id and uniacid=:uniacid limit 1", array(":id" => $val4["addressid"], ":uniacid" => $_W["uniacid"]));
                                    }
                                }
                            }
                            if (empty($val106)) {
                                return;
                            }
                            $val56 = array("first" => array("value" => "您的换货物品已经发货！", "color" => "#4a5077"), "keyword1" => array("title" => "订单内容", "value" => "【" . $val4["ordersn"] . "】" . $val22, "color" => "#4a5077"), "keyword2" => array("title" => "物流服务", "value" => $val52["rexpresscom"], "color" => "#4a5077"), "keyword3" => array("title" => "快递单号", "value" => $val52["rexpresssn"], "color" => "#4a5077"), "keyword4" => array("title" => "收货信息", "value" => "地址: " . $val106["province"] . " " . $val106["city"] . " " . $val106["area"] . " " . $val106["address"] . "收件人: " . $val106["realname"] . " (" . $val106["mobile"] . ") ", "color" => "#4a5077"), "remark" => array("value" => "\r\n我们正加速送到您的手上，请您耐心等候。", "color" => "#4a5077"));
                            if (!empty($val47["send"]) && empty($val40["send"])) {
                                m("message")->sendTplNotice($val17, $val47["send"], $val56, $val7);
                            } else {
                                if (empty($val40["send"])) {
                                    m("message")->sendCustomNotice($val17, $val56, $val7);
                                }
                            }
                        } else {
                            if ($val52["status"] == 1) {
                                if ($val52["rtype"] == 2) {
                                    $val56 = array("first" => array("value" => "您的订单已经完成换货！", "color" => "#4a5077"), "orderProductPrice" => array("title" => "退款金额", "value" => "-", "color" => "#4a5077"), "orderProductName" => array("title" => "商品详情", "value" => $val22 . $val35, "color" => "#4a5077"), "orderName" => array("title" => "订单编号", "value" => $val4["ordersn"], "color" => "#4a5077"), "remark" => array("value" => "\r\n 换货成功！\r\n【" . $val45["name"] . "】期待您再次购物！", "color" => "#4a5077"));
                                } else {
                                    $val143 = '';
                                    if (empty($val52["refundtype"])) {
                                        $val143 = ", 已经退回您的余额账户，请留意查收！";
                                    } else {
                                        if ($val52["refundtype"] == 1) {
                                            $val143 = ", 已经退回您的对应支付渠道（如银行卡，微信钱包等, 具体到账时间请您查看微信支付通知)，请留意查收！";
                                        } else {
                                            $val143 = ", 请联系客服进行退款事项！";
                                        }
                                    }
                                    $val56 = array("first" => array("value" => "您的订单已经完成退款！", "color" => "#4a5077"), "orderProductPrice" => array("title" => "退款金额", "value" => "¥" . $val52["price"] . "元", "color" => "#4a5077"), "orderProductName" => array("title" => "商品详情", "value" => $val22 . $val35, "color" => "#4a5077"), "orderName" => array("title" => "订单编号", "value" => $val4["ordersn"], "color" => "#4a5077"), "remark" => array("value" => "\r\n 退款金额 ¥" . $val52["price"] . "{$val143}\r\n 【" . $val45["name"] . "】期待您再次购物！", "color" => "#4a5077"));
                                }
                                if (!empty($val47["refund1"]) && empty($val40["refund1"])) {
                                    m("message")->sendTplNotice($val17, $val47["refund1"], $val56, $val7);
                                } else {
                                    if (empty($val40["refund1"])) {
                                        m("message")->sendCustomNotice($val17, $val56, $val7);
                                    }
                                }
                            } elseif ($val52["status"] == -1) {
                                $val167 = "\r\n驳回原因: " . $val52["reply"];
                                if (!empty($val45["phone"])) {
                                    $val167 .= "\r\n客服电话:  " . $val45["phone"];
                                }
                                $val56 = array("first" => array("value" => "您的" . $val50[$val52["rtype"]] . "申请被商家驳回，可与商家协商沟通！", "color" => "#4a5077"), "orderProductPrice" => array("title" => "退款金额", "value" => "¥" . $val52["price"] . "元", "color" => "#4a5077"), "orderProductName" => array("title" => "商品详情", "value" => $val22 . $val35, "color" => "#4a5077"), "orderName" => array("title" => "订单编号", "value" => $val4["ordersn"], "color" => "#4a5077"), "remark" => array("value" => $val167, "color" => "#4a5077"));
                                if (!empty($val47["refund2"]) && empty($val40["refund2"])) {
                                    m("message")->sendTplNotice($val17, $val47["refund2"], $val56, $val7);
                                } else {
                                    if (empty($val40["refund2"])) {
                                        m("message")->sendCustomNotice($val17, $val56, $val7);
                                    }
                                }
                            }
                        }
                    }
                }
                return;
            }
        }
        $val189 = '';
        if (!empty($val4["address"])) {
            $val106 = iunserializer($val4["address_send"]);
            if (!is_array($val106)) {
                $val106 = iunserializer($val4["address"]);
                if (!is_array($val106)) {
                    $val106 = pdo_fetch("select id,realname,mobile,address,province,city,area from " . tablename("ewei_shop_member_address") . " where id=:id and uniacid=:uniacid limit 1", array(":id" => $val4["addressid"], ":uniacid" => $_W["uniacid"]));
                }
            }
            if (!empty($val106)) {
                $val189 = "收件人: " . $val106["realname"] . "\r\n联系电话: " . $val106["mobile"] . "\r\n收货地址: " . $val106["province"] . $val106["city"] . $val106["area"] . " " . $val106["address"];
            }
        } else {
            $val208 = iunserializer($val4["carrier"]);
            if (is_array($val208)) {
                $val189 = "联系人: " . $val208["carrier_realname"] . "\r\n联系电话: " . $val208["carrier_mobile"];
            }
        }
        if ($val4["status"] == -1) {
            if (empty($val4["dispatchtype"])) {
                $val216 = array("title" => "收货信息", "value" => "收货地址: " . $val106["province"] . " " . $val106["city"] . " " . $val106["area"] . " " . $val106["address"] . " 收件人: " . $val106["realname"] . " 联系电话: " . $val106["mobile"], "color" => "#4a5077");
            } else {
                $val216 = array("title" => "收货信息", "value" => "自提地点: " . $val208["address"] . " 联系人: " . $val208["realname"] . " 联系电话: " . $val208["mobile"], "color" => "#4a5077");
            }
            $val56 = array("first" => array("value" => "您的订单已取消!", "color" => "#4a5077"), "orderProductPrice" => array("title" => "订单金额", "value" => "¥" . $val4["price"] . "元(含运费" . $val4["dispatchprice"] . "元)", "color" => "#4a5077"), "orderProductName" => array("title" => "商品详情", "value" => $val22, "color" => "#4a5077"), "orderAddress" => $val216, "orderName" => array("title" => "订单编号", "value" => $val4["ordersn"], "color" => "#4a5077"), "remark" => array("value" => "\r\n【" . $val45["name"] . "】欢迎您的再次购物！", "color" => "#4a5077"));
            if (!empty($val47["cancel"]) && empty($val40["cancel"])) {
                m("message")->sendTplNotice($val17, $val47["cancel"], $val56, $val7);
            } else {
                if (empty($val40["cancel"])) {
                    m("message")->sendCustomNotice($val17, $val56, $val7);
                }
            }
        } else {
            if ($val4["status"] == 0) {
                $val245 = explode(",", $val47["newtype"]);
                if (empty($val47["newtype"]) || is_array($val245) && in_array(0, $val245)) {
                    $val167 = "\r\n订单下单成功,请到后台查看!";
                    if (!empty($val189)) {
                        $val167 .= "\r\n下单者信息:\r\n" . $val189;
                    }
                    $val56 = array("first" => array("value" => "订单下单通知!", "color" => "#4a5077"), "keyword1" => array("title" => "时间", "value" => date("Y-m-d H:i:s", $val4["createtime"]), "color" => "#4a5077"), "keyword2" => array("title" => "商品名称", "value" => $val22 . $val35, "color" => "#4a5077"), "keyword3" => array("title" => "订单号", "value" => $val4["ordersn"], "color" => "#4a5077"), "remark" => array("value" => $val167, "color" => "#4a5077"));
                    $val259 = m("common")->getAccount();
                    if (!empty($val47["openid"])) {
                        $val261 = explode(",", $val47["openid"]);
                        foreach ($val261 as $val264) {
                            if (empty($val264)) {
                                continue;
                            }
                            if (!empty($val47["new"])) {
                                m("message")->sendTplNotice($val264, $val47["new"], $val56, '', $val259);
                            } else {
                                m("message")->sendCustomNotice($val264, $val56, '', $val259);
                            }
                        }
                    }
                }
                $val167 = "\r\n商品已经下单，请及时备货，谢谢!";
                if (!empty($val189)) {
                    $val167 .= "\r\n下单者信息:\r\n" . $val189;
                }
                foreach ($val19 as $val24) {
                    if (!empty($val24["noticeopenid"])) {
                        $val281 = explode(",", $val24["noticetype"]);
                        if (empty($val24["noticetype"]) || is_array($val281) && in_array(0, $val281)) {
                            $val286 = $val24["title"] . "( ";
                            if (!empty($val24["optiontitle"])) {
                                $val286 .= " 规格: " . $val24["optiontitle"];
                            }
                            $val286 .= " 单价: " . $val24["realprice"] / $val24["total"] . " 数量: " . $val24["total"] . " 总价: " . $val24["realprice"] . "); ";
                            $val56 = array("first" => array("value" => "商品下单通知!", "color" => "#4a5077"), "keyword1" => array("title" => "时间", "value" => date("Y-m-d H:i:s", $val4["createtime"]), "color" => "#4a5077"), "keyword2" => array("title" => "商品名称", "value" => $val286, "color" => "#4a5077"), "keyword3" => array("title" => "订单号", "value" => $val4["ordersn"], "color" => "#4a5077"), "remark" => array("value" => $val167, "color" => "#4a5077"));
                            if (!empty($val47["new"])) {
                                m("message")->sendTplNotice($val24["noticeopenid"], $val47["new"], $val56, '', $val259);
                            } else {
                                m("message")->sendCustomNotice($val24["noticeopenid"], $val56, '', $val259);
                            }
                        }
                    }
                }
                if (!empty($val4["addressid"])) {
                    $val167 = "\r\n您的订单我们已经收到，支付后我们将尽快配送~~";
                } else {
                    if (!empty($val4["isverify"])) {
                        $val167 = "\r\n您的订单我们已经收到，支付后您就可以到店使用了~~";
                    } else {
                        if (!empty($val4["virtual"])) {
                            $val167 = "\r\n您的订单我们已经收到，支付后系统将会自动发货~~";
                        } else {
                            $val167 = "\r\n您的订单我们已经收到，支付后您就可以到自提点提货物了~~";
                        }
                    }
                }
                $val56 = array("first" => array("value" => "您的订单已提交成功！", "color" => "#4a5077"), "keyword1" => array("title" => "店铺", "value" => $val45["name"], "color" => "#4a5077"), "keyword2" => array("title" => "下单时间", "value" => date("Y-m-d H:i:s", $val4["createtime"]), "color" => "#4a5077"), "keyword3" => array("title" => "商品", "value" => $val22, "color" => "#4a5077"), "keyword4" => array("title" => "金额", "value" => "¥" . $val4["price"] . "元(含运费" . $val4["dispatchprice"] . "元)", "color" => "#4a5077"), "remark" => array("value" => $val167, "color" => "#4a5077"));
                if (!empty($val47["submit"]) && empty($val40["submit"])) {
                    m("message")->sendTplNotice($val17, $val47["submit"], $val56, $val7);
                } else {
                    if (empty($val40["submit"])) {
                        m("message")->sendCustomNotice($val17, $val56, $val7);
                    }
                }
            } else {
                if ($val4["status"] == 1) {
                    $val245 = explode(",", $val47["newtype"]);
                    if ($val47["newtype"] == 1 || is_array($val245) && in_array(1, $val245)) {
                        $val167 = "\r\n订单已经下单支付，请及时备货，谢谢!";
                        if (!empty($val189)) {
                            $val167 .= "\r\n购买者信息:\r\n" . $val189;
                        }
                        $val56 = array("first" => array("value" => "订单下单支付通知!", "color" => "#4a5077"), "keyword1" => array("title" => "时间", "value" => date("Y-m-d H:i:s", $val4["createtime"]), "color" => "#4a5077"), "keyword2" => array("title" => "商品名称", "value" => $val22 . $val35, "color" => "#4a5077"), "keyword3" => array("title" => "订单号", "value" => $val4["ordersn"], "color" => "#4a5077"), "remark" => array("value" => $val167, "color" => "#4a5077"));
                        $val259 = m("common")->getAccount();
                        if (!empty($val47["openid"])) {
                            $val261 = explode(",", $val47["openid"]);
                            foreach ($val261 as $val264) {
                                if (empty($val264)) {
                                    continue;
                                }
                                if (!empty($val47["new"])) {
                                    m("message")->sendTplNotice($val264, $val47["new"], $val56, '', $val259);
                                } else {
                                    m("message")->sendCustomNotice($val264, $val56, '', $val259);
                                }
                            }
                        }
                    }
                    $val167 = "\r\n商品已经下单支付，请及时备货，谢谢!";
                    if (!empty($val189)) {
                        $val167 .= "\r\n购买者信息:\r\n" . $val189;
                    }
                    foreach ($val19 as $val24) {
                        $val281 = explode(",", $val24["noticetype"]);
                        if ($val24["noticetype"] == "1" || is_array($val281) && in_array(1, $val281)) {
                            $val286 = $val24["title"] . "( ";
                            if (!empty($val24["optiontitle"])) {
                                $val286 .= " 规格: " . $val24["optiontitle"];
                            }
                            $val286 .= " 单价: " . $val24["price"] / $val24["total"] . " 数量: " . $val24["total"] . " 总价: " . $val24["price"] . "); ";
                            $val56 = array("first" => array("value" => "商品下单支付通知!", "color" => "#4a5077"), "keyword1" => array("title" => "时间", "value" => date("Y-m-d H:i:s", $val4["createtime"]), "color" => "#4a5077"), "keyword2" => array("title" => "商品名称", "value" => $val286, "color" => "#4a5077"), "keyword3" => array("title" => "订单号", "value" => $val4["ordersn"], "color" => "#4a5077"), "remark" => array("value" => $val167, "color" => "#4a5077"));
                            if (!empty($val47["new"])) {
                                m("message")->sendTplNotice($val24["noticeopenid"], $val47["new"], $val56, '', $val259);
                            } else {
                                m("message")->sendCustomNotice($val24["noticeopenid"], $val56, '', $val259);
                            }
                        }
                    }
                    $val167 = "\r\n【" . $val45["name"] . "】欢迎您的再次购物！";
                    if ($val4["isverify"]) {
                        $val167 = "\r\n点击订单详情查看可消费门店, 【" . $val45["name"] . "】欢迎您的再次购物！";
                    }
                    $val56 = array("first" => array("value" => "您已支付成功订单！", "color" => "#4a5077"), "keyword1" => array("title" => "订单", "value" => $val4["ordersn"], "color" => "#4a5077"), "keyword2" => array("title" => "支付状态", "value" => "支付成功", "color" => "#4a5077"), "keyword3" => array("title" => "支付日期", "value" => date("Y-m-d H:i:s", $val4["paytime"]), "color" => "#4a5077"), "keyword4" => array("title" => "商户", "value" => $val45["name"], "color" => "#4a5077"), "keyword5" => array("title" => "金额", "value" => "¥" . $val4["price"] . "元(含运费" . $val4["dispatchprice"] . "元)", "color" => "#4a5077"), "remark" => array("value" => $val167, "color" => "#4a5077"));
                    $val409 = $val7;
                    if (strexists($val409, "/addons/ewei_shop/")) {
                        $val409 = str_replace("/addons/ewei_shop/", "/", $val409);
                    }
                    if (strexists($val409, "/core/mobile/order/")) {
                        $val409 = str_replace("/core/mobile/order/", "/", $val409);
                    }
                    if (!empty($val47["pay"]) && empty($val40["pay"])) {
                        m("message")->sendTplNotice($val17, $val47["pay"], $val56, $val409);
                    } else {
                        if (empty($val40["pay"])) {
                            m("message")->sendCustomNotice($val17, $val56, $val409);
                        }
                    }
                    if ($val4["dispatchtype"] == 1 && empty($val4["isverify"])) {
                        $val208 = iunserializer($val4["carrier"]);
                        if (!is_array($val208)) {
                            return;
                        }
                        $val56 = array("first" => array("value" => "自提订单提交成功!", "color" => "#4a5077"), "keyword1" => array("title" => "自提码", "value" => $val4["ordersn"], "color" => "#4a5077"), "keyword2" => array("title" => "商品详情", "value" => $val22 . $val35, "color" => "#4a5077"), "keyword3" => array("title" => "提货地址", "value" => $val208["address"], "color" => "#4a5077"), "keyword4" => array("title" => "提货时间", "value" => $val208["content"], "color" => "#4a5077"), "remark" => array("value" => "\r\n请您到选择的自提点进行取货, 自提联系人: " . $val208["realname"] . " 联系电话: " . $val208["mobile"], "color" => "#4a5077"));
                        if (!empty($val47["carrier"]) && empty($val40["carrier"])) {
                            m("message")->sendTplNotice($val17, $val47["carrier"], $val56, $val7);
                        } else {
                            if (empty($val40["carrier"])) {
                                m("message")->sendCustomNotice($val17, $val56, $val7);
                            }
                        }
                    }
                } else {
                    if ($val4["status"] == 2) {
                        if (empty($val4["dispatchtype"])) {
                            if (empty($val106)) {
                                return;
                            }
                            $val56 = array("first" => array("value" => "您的宝贝已经发货！", "color" => "#4a5077"), "keyword1" => array("title" => "订单内容", "value" => "【" . $val4["ordersn"] . "】" . $val22 . $val35, "color" => "#4a5077"), "keyword2" => array("title" => "物流服务", "value" => $val4["expresscom"], "color" => "#4a5077"), "keyword3" => array("title" => "快递单号", "value" => $val4["expresssn"], "color" => "#4a5077"), "keyword4" => array("title" => "收货信息", "value" => "地址: " . $val106["province"] . " " . $val106["city"] . " " . $val106["area"] . " " . $val106["address"] . "收件人: " . $val106["realname"] . " (" . $val106["mobile"] . ") ", "color" => "#4a5077"), "remark" => array("value" => "\r\n我们正加速送到您的手上，请您耐心等候。", "color" => "#4a5077"));
                            if (!empty($val47["send"]) && empty($val40["send"])) {
                                m("message")->sendTplNotice($val17, $val47["send"], $val56, $val7);
                            } else {
                                if (empty($val40["send"])) {
                                    m("message")->sendCustomNotice($val17, $val56, $val7);
                                }
                            }
                        }
                    } else {
                        if ($val4["status"] == 3) {
                            $val474 = p("virtual");
                            if ($val474 && !empty($val4["virtual"])) {
                                $val477 = $val474->getSet();
                                $val479 = "\r\n" . $val189 . "\r\n" . $val4["virtual_str"];
                                $val56 = array("first" => array("value" => "您购物的物品已自动发货!", "color" => "#4a5077"), "keyword1" => array("title" => "订单金额", "value" => "¥" . $val4["price"] . "元", "color" => "#4a5077"), "keyword2" => array("title" => "商品详情", "value" => $val22, "color" => "#4a5077"), "keyword3" => array("title" => "收货信息", "value" => $val479, "color" => "#4a5077"), "remark" => array("title" => '', "value" => "\r\n【" . $val45["name"] . "】感谢您的支持与厚爱，欢迎您的再次购物！", "color" => "#4a5077"));
                                if (!empty($val477["tm"]["send"]) && empty($val40["finish"])) {
                                    m("message")->sendTplNotice($val17, $val477["tm"]["send"], $val56, $val7);
                                } else {
                                    if (empty($val40["finish"])) {
                                        m("message")->sendCustomNotice($val17, $val56, $val7);
                                    }
                                }
                                $val497 = "买家购买的商品已经自动发货!";
                                $val167 = "\r\n发货信息:" . $val479;
                                $val245 = explode(",", $val47["newtype"]);
                                if ($val47["newtype"] == 2 || is_array($val245) && in_array(2, $val245)) {
                                    $val56 = array("first" => array("value" => $val497, "color" => "#4a5077"), "keyword1" => array("title" => "订单号", "value" => $val4["ordersn"], "color" => "#4a5077"), "keyword2" => array("title" => "商品名称", "value" => $val22 . $val35, "color" => "#4a5077"), "keyword3" => array("title" => "下单时间", "value" => date("Y-m-d H:i:s", $val4["createtime"]), "color" => "#4a5077"), "keyword4" => array("title" => "发货时间", "value" => date("Y-m-d H:i:s", $val4["sendtime"]), "color" => "#4a5077"), "keyword5" => array("title" => "确认收货时间", "value" => date("Y-m-d H:i:s", $val4["finishtime"]), "color" => "#4a5077"), "remark" => array("title" => '', "value" => $val167, "color" => "#4a5077"));
                                    $val259 = m("common")->getAccount();
                                    if (!empty($val47["openid"])) {
                                        $val261 = explode(",", $val47["openid"]);
                                        foreach ($val261 as $val264) {
                                            if (empty($val264)) {
                                                continue;
                                            }
                                            if (!empty($val47["finish"])) {
                                                m("message")->sendTplNotice($val264, $val47["finish"], $val56, '', $val259);
                                            } else {
                                                m("message")->sendCustomNotice($val264, $val56, '', $val259);
                                            }
                                        }
                                    }
                                }
                                foreach ($val19 as $val24) {
                                    $val281 = explode(",", $val24["noticetype"]);
                                    if ($val24["noticetype"] == "2" || is_array($val281) && in_array(2, $val281)) {
                                        $val286 = $val24["title"] . "( ";
                                        if (!empty($val24["optiontitle"])) {
                                            $val286 .= " 规格: " . $val24["optiontitle"];
                                        }
                                        $val286 .= " 单价: " . $val24["price"] / $val24["total"] . " 数量: " . $val24["total"] . " 总价: " . $val24["price"] . "); ";
                                        $val56 = array("first" => array("value" => $val497, "color" => "#4a5077"), "keyword1" => array("title" => "订单号", "value" => $val4["ordersn"], "color" => "#4a5077"), "keyword2" => array("title" => "商品名称", "value" => $val286, "color" => "#4a5077"), "keyword3" => array("title" => "下单时间", "value" => date("Y-m-d H:i:s", $val4["createtime"]), "color" => "#4a5077"), "keyword4" => array("title" => "发货时间", "value" => date("Y-m-d H:i:s", $val4["sendtime"]), "color" => "#4a5077"), "keyword5" => array("title" => "确认收货时间", "value" => date("Y-m-d H:i:s", $val4["finishtime"]), "color" => "#4a5077"), "remark" => array("title" => '', "value" => $val167, "color" => "#4a5077"));
                                        if (!empty($val47["finish"])) {
                                            m("message")->sendTplNotice($val24["noticeopenid"], $val47["finish"], $val56, '', $val259);
                                        } else {
                                            m("message")->sendCustomNotice($val24["noticeopenid"], $val56, '', $val259);
                                        }
                                    }
                                }
                            } else {
                                $val56 = array("first" => array("value" => "亲, 您购买的宝贝已经确认收货!", "color" => "#4a5077"), "keyword1" => array("title" => "订单号", "value" => $val4["ordersn"], "color" => "#4a5077"), "keyword2" => array("title" => "商品名称", "value" => $val22 . $val35, "color" => "#4a5077"), "keyword3" => array("title" => "下单时间", "value" => date("Y-m-d H:i:s", $val4["createtime"]), "color" => "#4a5077"), "keyword4" => array("title" => "发货时间", "value" => date("Y-m-d H:i:s", $val4["sendtime"]), "color" => "#4a5077"), "keyword5" => array("title" => "确认收货时间", "value" => date("Y-m-d H:i:s", $val4["finishtime"]), "color" => "#4a5077"), "remark" => array("title" => '', "value" => "\r\n【" . $val45["name"] . "】感谢您的支持与厚爱，欢迎您的再次购物！", "color" => "#4a5077"));
                                if (!empty($val47["finish"]) && empty($val40["finish"])) {
                                    m("message")->sendTplNotice($val17, $val47["finish"], $val56, $val7);
                                } else {
                                    if (empty($val40["finish"])) {
                                        m("message")->sendCustomNotice($val17, $val56, $val7);
                                    }
                                }
                                $val497 = "买家购买的商品已经确认收货!";
                                if ($val4["isverify"] == 1) {
                                    $val497 = "买家购买的商品已经确认核销!";
                                }
                                $val167 = "";
                                if (!empty($val189)) {
                                    $val167 = "\r\n购买者信息:\r\n" . $val189;
                                }
                                $val245 = explode(",", $val47["newtype"]);
                                if ($val47["newtype"] == 2 || is_array($val245) && in_array(2, $val245)) {
                                    $val56 = array("first" => array("value" => $val497, "color" => "#4a5077"), "keyword1" => array("title" => "订单号", "value" => $val4["ordersn"], "color" => "#4a5077"), "keyword2" => array("title" => "商品名称", "value" => $val22 . $val35, "color" => "#4a5077"), "keyword3" => array("title" => "下单时间", "value" => date("Y-m-d H:i:s", $val4["createtime"]), "color" => "#4a5077"), "keyword4" => array("title" => "发货时间", "value" => date("Y-m-d H:i:s", $val4["sendtime"]), "color" => "#4a5077"), "keyword5" => array("title" => "确认收货时间", "value" => date("Y-m-d H:i:s", $val4["finishtime"]), "color" => "#4a5077"), "remark" => array("title" => '', "value" => $val167, "color" => "#4a5077"));
                                    $val259 = m("common")->getAccount();
                                    if (!empty($val47["openid"])) {
                                        $val261 = explode(",", $val47["openid"]);
                                        foreach ($val261 as $val264) {
                                            if (empty($val264)) {
                                                continue;
                                            }
                                            if (!empty($val47["finish"])) {
                                                m("message")->sendTplNotice($val264, $val47["finish"], $val56, '', $val259);
                                            } else {
                                                m("message")->sendCustomNotice($val264, $val56, '', $val259);
                                            }
                                        }
                                    }
                                }
                                foreach ($val19 as $val24) {
                                    $val281 = explode(",", $val24["noticetype"]);
                                    if ($val24["noticetype"] == "2" || is_array($val281) && in_array(2, $val281)) {
                                        $val286 = $val24["title"] . "( ";
                                        if (!empty($val24["optiontitle"])) {
                                            $val286 .= " 规格: " . $val24["optiontitle"];
                                        }
                                        $val286 .= " 单价: " . $val24["price"] / $val24["total"] . " 数量: " . $val24["total"] . " 总价: " . $val24["price"] . "); ";
                                        $val56 = array("first" => array("value" => $val497, "color" => "#4a5077"), "keyword1" => array("title" => "订单号", "value" => $val4["ordersn"], "color" => "#4a5077"), "keyword2" => array("title" => "商品名称", "value" => $val286, "color" => "#4a5077"), "keyword3" => array("title" => "下单时间", "value" => date("Y-m-d H:i:s", $val4["createtime"]), "color" => "#4a5077"), "keyword4" => array("title" => "发货时间", "value" => date("Y-m-d H:i:s", $val4["sendtime"]), "color" => "#4a5077"), "keyword5" => array("title" => "确认收货时间", "value" => date("Y-m-d H:i:s", $val4["finishtime"]), "color" => "#4a5077"), "remark" => array("title" => '', "value" => $val167, "color" => "#4a5077"));
                                        if (!empty($val47["finish"])) {
                                            m("message")->sendTplNotice($val24["noticeopenid"], $val47["finish"], $val56, '', $val259);
                                        } else {
                                            m("message")->sendCustomNotice($val24["noticeopenid"], $val56, '', $val259);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    public function sendMemberUpgradeMessage($val17 = '', $val647 = null, $val648 = null)
    {
        global $_W, $_GPC;
        $val38 = m("member")->getMember($val17);
        $val40 = unserialize($val38["noticeset"]);
        if (!is_array($val40)) {
            $val40 = array();
        }
        $val45 = m("common")->getSysset("shop");
        $val47 = m("common")->getSysset("notice");
        $val7 = $_W["siteroot"] . "app/index.php?i=" . $_W["uniacid"] . "&c=entry&m=ewei_shop&do=member";
        if (strexists($val7, "/addons/ewei_shop/")) {
            $val7 = str_replace("/addons/ewei_shop/", "/", $val7);
        }
        if (strexists($val7, "/core/mobile/order/")) {
            $val7 = str_replace("/core/mobile/order/", "/", $val7);
        }
        if (!$val648) {
            $val648 = m("member")->getLevel($val17);
        }
        $val671 = empty($val45["levelname"]) ? "普通会员" : $val45["levelname"];
        $val56 = array("first" => array("value" => "亲爱的" . $val38["nickname"] . ", 恭喜您成功升级！", "color" => "#4a5077"), "keyword1" => array("title" => "任务名称", "value" => "会员升级", "color" => "#4a5077"), "keyword2" => array("title" => "通知类型", "value" => "您会员等级从 " . $val671 . " 升级为 " . $val648["levelname"] . ", 特此通知!", "color" => "#4a5077"), "remark" => array("value" => "\r\n您即可享有" . $val648["levelname"] . "的专属优惠及服务！", "color" => "#4a5077"));
        if (!empty($val47["upgrade"]) && empty($val40["upgrade"])) {
            m("message")->sendTplNotice($val17, $val47["upgrade"], $val56, $val7);
        } else {
            if (empty($val40["upgrade"])) {
                m("message")->sendCustomNotice($val17, $val56, $val7);
            }
        }
    }
    public function sendMemberLogMessage($val689 = '')
    {
        global $_W, $_GPC;
        $val692 = pdo_fetch("select * from " . tablename("ewei_shop_member_log") . " where id=:id and uniacid=:uniacid limit 1", array(":id" => $val689, ":uniacid" => $_W["uniacid"]));
        $val38 = m("member")->getMember($val692["openid"]);
        $val45 = m("common")->getSysset("shop");
        $val40 = unserialize($val38["noticeset"]);
        if (!is_array($val40)) {
            $val40 = array();
        }
        $val259 = m("common")->getAccount();
        if (!$val259) {
            return;
        }
        $val47 = m("common")->getSysset("notice");
        if ($val692["type"] == 0) {
            if ($val692["status"] == 1) {
                $val707 = "后台充值";
                if ($val692["rechargetype"] == "wechat") {
                    $val707 = "微信支付";
                } else {
                    if ($val692 == "alipay") {
                        $val707["rechargetype"] = "支付宝";
                    }
                }
                $val712 = "¥" . $val692["money"] . "元";
                if ($val692["gives"] > 0) {
                    $val715 = $val692["money"] + $val692["gives"];
                    $val712 .= "，系统赠送" . $val692["gives"] . "元，合计:" . $val715 . "元";
                }
                $val56 = array("first" => array("value" => "恭喜您充值成功!", "color" => "#4a5077"), "money" => array("title" => "充值金额", "value" => $val712, "color" => "#4a5077"), "product" => array("title" => "充值方式", "value" => $val707, "color" => "#4a5077"), "remark" => array("value" => "\r\n谢谢您对我们的支持！", "color" => "#4a5077"));
                $val7 = $_W["siteroot"] . "app/index.php?i=" . $_W["uniacid"] . "&c=entry&m=ewei_shop&do=member";
                if (strexists($val7, "/addons/ewei_shop/")) {
                    $val7 = str_replace("/addons/ewei_shop/", "/", $val7);
                }
                if (strexists($val7, "/core/mobile/order/")) {
                    $val7 = str_replace("/core/mobile/order/", "/", $val7);
                }
                if (!empty($val47["recharge_ok"]) && empty($val40["recharge_ok"])) {
                    m("message")->sendTplNotice($val692["openid"], $val47["recharge_ok"], $val56, $val7);
                } else {
                    if (empty($val40["recharge_ok"])) {
                        m("message")->sendCustomNotice($val692["openid"], $val56, $val7);
                    }
                }
            } else {
                if ($val692["status"] == 3) {
                    $val56 = array("first" => array("value" => "充值退款成功!", "color" => "#4a5077"), "reason" => array("title" => "退款原因", "value" => "【" . $val45["name"] . "】充值退款", "color" => "#4a5077"), "refund" => array("title" => "退款金额", "value" => "¥" . $val692["money"] . "元", "color" => "#4a5077"), "remark" => array("value" => "\r\n退款成功，请注意查收! 谢谢您对我们的支持！", "color" => "#4a5077"));
                    $val7 = $_W["siteroot"] . "app/index.php?i=" . $_W["uniacid"] . "&c=entry&m=ewei_shop&do=member";
                    if (strexists($val7, "/addons/ewei_shop/")) {
                        $val7 = str_replace("/addons/ewei_shop/", "/", $val7);
                    }
                    if (strexists($val7, "/core/mobile/order/")) {
                        $val7 = str_replace("/core/mobile/order/", "/", $val7);
                    }
                    if (!empty($val47["recharge_fund"]) && empty($val40["recharge_fund"])) {
                        m("message")->sendTplNotice($val692["openid"], $val47["recharge_fund"], $val56, $val7);
                    } else {
                        if (empty($val40["recharge_fund"])) {
                            m("message")->sendCustomNotice($val692["openid"], $val56, $val7);
                        }
                    }
                }
            }
        } else {
            if ($val692["type"] == 1 && $val692["status"] == 0) {
                $val56 = array("first" => array("value" => "提现申请已经成功提交!", "color" => "#4a5077"), "money" => array("title" => "提现金额", "value" => "¥" . $val692["money"] . "元", "color" => "#4a5077"), "timet" => array("title" => "提现时间", "value" => date("Y-m-d H:i:s", $val692["createtime"]), "color" => "#4a5077"), "remark" => array("value" => "\r\n请等待我们的审核并打款！", "color" => "#4a5077"));
                $val7 = $_W["siteroot"] . "app/index.php?i=" . $_W["uniacid"] . "&c=entry&m=ewei_shop&do=member&p=log&type=1";
                if (strexists($val7, "/addons/ewei_shop/")) {
                    $val7 = str_replace("/addons/ewei_shop/", "/", $val7);
                }
                if (!empty($val47["withdraw"]) && empty($val40["withdraw"])) {
                    m("message")->sendTplNotice($val692["openid"], $val47["withdraw"], $val56, $val7);
                } else {
                    if (empty($val40["withdraw"])) {
                        m("message")->sendCustomNotice($val692["openid"], $val56, $val7);
                    }
                }
            } else {
                if ($val692["type"] == 1 && $val692["status"] == 1) {
                    $val56 = array("first" => array("value" => "恭喜您成功提现!", "color" => "#4a5077"), "money" => array("title" => "提现金额", "value" => "¥" . $val692["money"] . "元", "color" => "#4a5077"), "timet" => array("title" => "提现时间", "value" => date("Y-m-d H:i:s", $val692["createtime"]), "color" => "#4a5077"), "remark" => array("value" => "\r\n感谢您的支持！", "color" => "#4a5077"));
                    $val7 = $_W["siteroot"] . "app/index.php?i=" . $_W["uniacid"] . "&c=entry&m=ewei_shop&do=member&p=log&type=1";
                    if (!empty($val47["withdraw_ok"]) && empty($val40["withdraw_ok"])) {
                        m("message")->sendTplNotice($val692["openid"], $val47["withdraw_ok"], $val56, $val7);
                    } else {
                        if (empty($val40["withdraw_ok"])) {
                            m("message")->sendCustomNotice($val692["openid"], $val56, $val7);
                        }
                    }
                } else {
                    if ($val692["type"] == 1 && $val692["status"] == -1) {
                        $val56 = array("first" => array("value" => "抱歉，提现申请审核失败!", "color" => "#4a5077"), "money" => array("title" => "提现金额", "value" => "¥" . $val692["money"] . "元", "color" => "#4a5077"), "timet" => array("title" => "提现时间", "value" => date("Y-m-d H:i:s", $val692["createtime"]), "color" => "#4a5077"), "remark" => array("value" => "\r\n有疑问请联系客服，谢谢您的支持！", "color" => "#4a5077"));
                        $val7 = $_W["siteroot"] . "app/index.php?i=" . $_W["uniacid"] . "&c=entry&m=ewei_shop&do=member&p=log&type=1";
                        if (strexists($val7, "/addons/ewei_shop/")) {
                            $val7 = str_replace("/addons/ewei_shop/", "/", $val7);
                        }
                        if (strexists($val7, "/core/mobile/order/")) {
                            $val7 = str_replace("/core/mobile/order/", "/", $val7);
                        }
                        if (!empty($val47["withdraw_fail"]) && empty($val40["withdraw_fail"])) {
                            m("message")->sendTplNotice($val692["openid"], $val47["withdraw_fail"], $val56, $val7);
                        } else {
                            if (empty($val40["withdraw_fail"])) {
                                m("message")->sendCustomNotice($val692["openid"], $val56, $val7);
                            }
                        }
                    }
                }
            }
        }
    }
}