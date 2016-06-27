<?php
if (!defined("IN_IA")) {
    exit("Access Denied");
}
class Ewei_DShop_Order
{
    function getDispatchPrice($weight, $d, $type = -1)
    {
        if (empty($d)) {
            return 0;
        }
        $price = 0;
        if ($type == -1) {
            $type = $d["calculatetype"];
        }
        if ($type == 1) {
            if ($weight <= $d["firstnum"]) {
                $price = floatval($d["firstnumprice"]);
            } else {
                $price = floatval($d["firstnumprice"]);
                $secondweight = $weight - floatval($d["firstnum"]);
                $dsecondweight = floatval($d["secondnum"]) <= 0 ? 1 : floatval($d["secondnum"]);
                $secondprice = 0;
                if ($secondweight % $dsecondweight == 0) {
                    $secondprice = $secondweight / $dsecondweight * floatval($d["secondnumprice"]);
                } else {
                    $secondprice = ((int) ($secondweight / $dsecondweight) + 1) * floatval($d["secondnumprice"]);
                }
                $price += $secondprice;
            }
        } else {
            if ($weight <= $d["firstweight"]) {
                $price = floatval($d["firstprice"]);
            } else {
                $price = floatval($d["firstprice"]);
                $secondweight = $weight - floatval($d["firstweight"]);
                $dsecondweight = floatval($d["secondweight"]) <= 0 ? 1 : floatval($d["secondweight"]);
                $secondprice = 0;
                if ($secondweight % $dsecondweight == 0) {
                    $secondprice = $secondweight / $dsecondweight * floatval($d["secondprice"]);
                } else {
                    $secondprice = ((int) ($secondweight / $dsecondweight) + 1) * floatval($d["secondprice"]);
                }
                $price += $secondprice;
            }
        }
        return $price;
    }
    function getCityDispatchPrice($areas, $city, $weight, $d)
    {
        if (is_array($areas) && count($areas) > 0) {
            foreach ($areas as $var) {
                $citys = explode(";", $var["citys"]);
                if (in_array($city, $citys) && !empty($citys)) {
                    return $this->getDispatchPrice($weight, $var, $d["calculatetype"]);
                }
            }
        }
        return $this->getDispatchPrice($weight, $d);
    }
    public function payResult($params)
    {
        global $_W;
        $fee = intval($params["fee"]);
        $data = array("status" => $params["result"] == "success" ? 1 : 0);
        $ordersn = $params["tid"];
        $order = pdo_fetch("select id,ordersn, price,openid,dispatchtype,addressid,carrier,status,isverify,deductcredit2,virtual,isvirtual,couponid from " . tablename("ewei_shop_order") . " where  ordersn=:ordersn and uniacid=:uniacid limit 1", array(":uniacid" => $_W["uniacid"], ":ordersn" => $ordersn));
        $orderid = $order["id"];
        if ($params["from"] == "return") {
            $address = false;
            if (empty($order["dispatchtype"])) {
                $address = pdo_fetch("select realname,mobile,address from " . tablename("ewei_shop_member_address") . " where id=:id limit 1", array(":id" => $order["addressid"]));
            }
            $carrier = false;
            if ($order["dispatchtype"] == 1 || $order["isvirtual"] == 1) {
                $carrier = unserialize($order["carrier"]);
            }
            if ($params["type"] == "cash") {
                return array("result" => "success", "order" => $order, "address" => $address, "carrier" => $carrier);
            } else {
                if ($order["status"] == 0) {
                    $pv = p("virtual");
                    if (!empty($order["virtual"]) && $pv) {
                        $pv->pay($order);
                    } else {
                        pdo_update("ewei_shop_order", array("status" => 1, "paytime" => time()), array("id" => $orderid));
                        $this->setStocksAndCredits($orderid, 1);
                        if (p("coupon") && !empty($order["couponid"])) {
                            p("coupon")->backConsumeCoupon($order["id"]);
                        }
                        m("notice")->sendOrderMessage($orderid);
                        if (p("commission")) {
                            p("commission")->checkOrderPay($order["id"]);
                        }
                    }
                }
				
				//供应商 订单分解
                if(p('supplier')){
                    $order_info = $order;
                    $resolve_order_goods = pdo_fetchall('select * from ' . tablename('ewei_shop_order_goods') . ' where orderid=:orderid and uniacid=:uniacid ',array(
                            ':orderid' => $order['id'],
                            ':uniacid' => $_W['uniacid']
                        ));
                    $datas = array();
                    $num = false;
                    //对应供应商商品循环到对应供应商下
                    foreach ($resolve_order_goods as $key => $value) {
                        $datas[$value['supplier_uid']][]['id'] = $value['id'];
                    }
                    unset($order['id']);
                    $dispatchprice = $order['dispatchprice'];
                    $olddispatchprice = $order['olddispatchprice'];
                    $changedispatchprice = $order['changedispatchprice'];
                    if(!empty($datas)){
                        foreach ($datas as $key => $value) {
                            $price = 0;
                            $realprice = 0;
                            $oldprice = 0;
                            $changeprice = 0;
                            $goodsprice = 0;
                            $couponprice = 0;
                            $discountprice = 0;
                            $deductprice = 0;
                            $deductcredit2 = 0;
                            foreach($value as $v){
                                $resu = pdo_fetch('select price,realprice,oldprice,supplier_uid from ' . tablename('ewei_shop_order_goods') . ' where id=:id and uniacid=:uniacid ',array(
                                        ':id' => $v['id'],
                                        ':uniacid' => $_W['uniacid']
                                ));
                                $price += $resu['price'];
                                $realprice += $resu['realprice'];
                                $oldprice += $resu['oldprice'];
                                $goodsprice += $resu['price'];
                                $supplier_uid = $resu['supplier_uid'];
                                $changeprice += $resu['changeprice'];
                                //计算order_goods表中的价格占订单商品总额的比例
                                $scale = $resu['price']/$order['goodsprice'];
                                //按比例计算优惠劵金额
                                $couponprice += round($scale*$order['couponprice'],2);
                                //按比例计算会员折扣金额
                                $discountprice += round($scale*$order['discountprice'],2);
                                //按比例计算积分金额
                                $deductprice += round($scale*$order['deductprice'],2);
                                //按比例计算消费余额金额
                                $deductcredit2 += round($scale*$order['deductcredit2'],2); 
                            }
                            
                            $order['oldprice'] = $oldprice;
                            $order['goodsprice'] = $goodsprice;
                            $order['supplier_uid'] = $supplier_uid;
                            $order['couponprice'] = $couponprice;
                            $order['discountprice'] = $discountprice;
                            $order['deductprice'] = $deductprice;
                            $order['deductcredit2'] = $deductcredit2;
                            $order['changeprice'] = $changeprice;
                            //平分实际支付运费金额
                            $order['dispatchprice'] = $dispatchprice;
                            //老的支付运费金额
                            $order['olddispatchprice'] = $olddispatchprice;
                            //平分修改后支付运费金额
                            $order['changedispatchprice'] = $changedispatchprice;
                            //新订单金额计算，实际支付金额减计算后优惠劵金额、会员折金额、积分金额、余额抵扣金额，在加上实际运费的金额。
                            $order['price'] = $realprice - $couponprice - $discountprice - $deductprice - $deductcredit2 + $order['dispatchprice'];
							//修复订单付款状态不同步
							$order['status'] = 1;
							//修复订单付款时间不同步
                            $order['paytime'] = time();
                            if($num == false){
								//是否只有一个供应商的商品
								$supplier_num = count($datas);
								if($supplier_num != 1){

									$order_id = pdo_fetchcolumn('select id from ' . tablename('ewei_shop_order') . ' where ordersn=:ordersn and uniacid=:uniacid ',array(
										':ordersn' => $order['ordersn'],
										':uniacid' => $_W['uniacid']
									));
									$goodsres = pdo_fetch('select price,realprice,oldprice from ' . tablename('ewei_shop_order_goods') . ' where id=:id and uniacid=:uniacid ',array(
										':id' => $order_id,
										':uniacid' => $_W['uniacid']
									));
									
									if($goodsres['supplier_uid'] == $supplier_uid){
										$order['price'] += $goodsres['price']+$order['olddispatchprice'];
										$order['goodsprice'] += $goodsres['price'];
										$order['oldprice'] += $goodsres['oldprice']+$order['olddispatchprice'];
									}	
								}else{
									$order['supplier_uid'] = $supplier_uid;
								}
                                pdo_update('ewei_shop_order', $order, array(
                                    'ordersn' => $order['ordersn'],
                                    'uniacid' => $_W['uniacid']
                                    ));
                                $num = true;
                            }else{
                                $ordersn = m('common')->createNO('order', 'ordersn', 'SH');
                                $order['ordersn'] = $ordersn;
                                pdo_insert('ewei_shop_order', $order);
                                $logid = pdo_insertid();
                                $oid = array(
                                    'orderid' => $logid
                                    );
                                foreach ($value as $val) {
                                    pdo_update('ewei_shop_order_goods',$oid ,array('id' => $val['id'],'uniacid' => $_W['uniacid']));
                                }
                                
                            }
                        }
                    }
                }else{
                    $order_info = $order;
                }
				
				
                return array("result" => "success", "order" => $order_info, "address" => $address, "carrier" => $carrier, "virtual" => $order["virtual"]);
            }
        }
    }
    function setDeductCredit2($order)
    {
        global $_W;
        $shop = m("common")->getSysset("shop");
        if ($order["deductcredit2"] > 0) {
            m("member")->setCredit($order["openid"], "credit2", $order["deductcredit2"], array('0', $shop["name"] . "购物返还抵扣余额 余额: {$order['deductcredit2']} 订单号: {$order['ordersn']}"));
        }
    }
    function setStocksAndCredits($orderid = '', $type = 0)
    {
        global $_W;
        $order = pdo_fetch("select id,ordersn,price,openid,dispatchtype,addressid,carrier,status from " . tablename("ewei_shop_order") . " where id=:id limit 1", array(":id" => $orderid));
        $goods = pdo_fetchall("select og.goodsid,og.total,g.totalcnf,og.realprice, g.credit,og.optionid,g.total as goodstotal,og.optionid,g.sales,g.salesreal from " . tablename("ewei_shop_order_goods") . " og " . " left join " . tablename("ewei_shop_goods") . " g on g.id=og.goodsid " . " where og.orderid=:orderid and og.uniacid=:uniacid ", array(":uniacid" => $_W["uniacid"], ":orderid" => $orderid));
        $credits = 0;
        foreach ($goods as $g) {
            $stocktype = 0;
            if ($type == 0) {
                if ($g["totalcnf"] == 0) {
                    $stocktype = -1;
                }
            } else {
                if ($type == 1) {
                    if ($g["totalcnf"] == 1) {
                        $stocktype = -1;
                    }
                } else {
                    if ($type == 2) {
                        if ($order["status"] >= 1) {
                            if ($g["totalcnf"] == 1) {
                                $stocktype = 1;
                            }
                        } else {
                            if ($g["totalcnf"] == 0) {
                                $stocktype = 1;
                            }
                        }
                    }
                }
            }
            if (!empty($stocktype)) {
                if (!empty($g["optionid"])) {
                    $option = m("goods")->getOption($g["goodsid"], $g["optionid"]);
                    if (!empty($option) && $option["stock"] != -1) {
                        $stock = -1;
                        if ($stocktype == 1) {
                            $stock = $option["stock"] + $g["total"];
                        } else {
                            if ($stocktype == -1) {
                                $stock = $option["stock"] - $g["total"];
                                $stock <= 0 && ($stock = 0);
                            }
                        }
                        if ($stock != -1) {
                            pdo_update("ewei_shop_goods_option", array("stock" => $stock), array("uniacid" => $_W["uniacid"], "goodsid" => $g["goodsid"], "id" => $g["optionid"]));
                        }
                    }
                }
                if (!empty($g["goodstotal"]) && $g["goodstotal"] != -1) {
                    $totalstock = -1;
                    if ($stocktype == 1) {
                        $totalstock = $g["goodstotal"] + $g["total"];
                    } else {
                        if ($stocktype == -1) {
                            $totalstock = $g["goodstotal"] - $g["total"];
                            $totalstock <= 0 && ($totalstock = 0);
                        }
                    }
                    if ($totalstock != -1) {
                        pdo_update("ewei_shop_goods", array("total" => $totalstock), array("uniacid" => $_W["uniacid"], "id" => $g["goodsid"]));
                    }
                }
            }
            $gcredit = trim($g["credit"]);
            if (!empty($gcredit)) {
                if (strexists($gcredit, "%")) {
                    $credits += intval(floatval(str_replace("%", '', $gcredit)) / 100 * $g["realprice"]);
                } else {
                    $credits += intval($g["credit"]) * $g["total"];
                }
            }
            if ($type == 0) {
                pdo_update("ewei_shop_goods", array("sales" => $g["sales"] + $g["total"]), array("uniacid" => $_W["uniacid"], "id" => $g["goodsid"]));
            } elseif ($type == 1) {
                if ($order["status"] >= 1) {
                    $salesreal = pdo_fetchcolumn("select ifnull(sum(total),0) from " . tablename("ewei_shop_order_goods") . " og " . " left join " . tablename("ewei_shop_order") . " o on o.id = og.orderid " . " where og.goodsid=:goodsid and o.status>=1 and o.uniacid=:uniacid limit 1", array(":goodsid" => $g["goodsid"], ":uniacid" => $_W["uniacid"]));
                    pdo_update("ewei_shop_goods", array("salesreal" => $salesreal), array("id" => $g["goodsid"]));
                }
            }
        }
        if ($credits > 0) {
            $shop = m("common")->getSysset("shop");
            if ($type == 1) {
                m("member")->setCredit($order["openid"], "credit1", $credits, array(0, $shop["name"] . "购物积分 订单号: " . $order["ordersn"]));
            } elseif ($type == 2) {
                if ($order["status"] >= 1) {
                    m("member")->setCredit($order["openid"], "credit1", -$credits, array(0, $shop["name"] . "购物取消订单扣除积分 订单号: " . $order["ordersn"]));
                }
            }
        }
    }
    function getDefaultDispatch(){
        global $_W;
        $dispatch = 'select * from ' . tablename('ewei_shop_dispatch') . ' where isdefault=1 and uniacid=:uniacid and enabled=1 Limit 1';
        $params = array(':uniacid' => $_W['uniacid']);
        $data = pdo_fetch($dispatch, $params);
        return $data;
    }
    function getNewDispatch(){
        global $_W;
        $dispatch = 'select * from ' . tablename('ewei_shop_dispatch') . ' where uniacid=:uniacid and enabled=1 order by id desc Limit 1';
        $params = array(':uniacid' => $_W['uniacid']);
        $data = pdo_fetch($dispatch, $params);
        return $data;
    }
    function getOneDispatch($id){
        global $_W;
        $dispatch = 'select * from ' . tablename('ewei_shop_dispatch') . ' where id=:id and uniacid=:uniacid and enabled=1 Limit 1';
        $params = array(':id' => $id, ':uniacid' => $_W['uniacid']);
        $data = pdo_fetch($dispatch, $params);
        return $data;
    }
    function getTotals()
    {
        global $_W;
        $params = array(":uniacid" => $_W["uniacid"]);
        $type57 = "";
        $order["all"] = pdo_fetchcolumn("SELECT COUNT(1) FROM " . tablename("ewei_shop_order") . " o {$type57}" . " WHERE o.uniacid = :uniacid and o.deleted=0", $params);
        $order["status_1"] = pdo_fetchcolumn("SELECT COUNT(1) FROM " . tablename("ewei_shop_order") . " o {$type57}" . " WHERE o.uniacid = :uniacid and o.status=-1 and o.refundtime=0", $params);
        $order["status0"] = pdo_fetchcolumn("SELECT COUNT(1) FROM " . tablename("ewei_shop_order") . " o {$type57}" . " WHERE o.uniacid = :uniacid  and o.status=0 and o.paytype<>3", $params);
        $order["status1"] = pdo_fetchcolumn("SELECT COUNT(1) FROM " . tablename("ewei_shop_order") . " o {$type57}" . " WHERE o.uniacid = :uniacid  and ( o.status=1 or ( o.status=0 and o.paytype=3) )", $params);
        $order["status2"] = pdo_fetchcolumn("SELECT COUNT(1) FROM " . tablename("ewei_shop_order") . " o {$type57}" . " WHERE o.uniacid = :uniacid  and o.status=2", $params);
        $order["status3"] = pdo_fetchcolumn("SELECT COUNT(1) FROM " . tablename("ewei_shop_order") . " o {$type57}" . " WHERE o.uniacid = :uniacid  and o.status=3", $params);
        $order["status4"] = pdo_fetchcolumn("SELECT COUNT(1) FROM " . tablename("ewei_shop_order") . " o {$type57}" . " WHERE o.uniacid = :uniacid  and o.refundstate>0 and o.refundid<>0", $params);
        $order["status5"] = pdo_fetchcolumn("SELECT COUNT(1) FROM " . tablename("ewei_shop_order") . " o {$type57}" . " WHERE o.uniacid = :uniacid  and o.refundtime<>0", $params);
		/*供应商插件*/
		if(p('supplier')){
			$order["status9"] = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename("ewei_shop_supplier_apply") . " WHERE uid = ".$_W['uid']);
		}
		//print_r($order);
        return $order;
    }
}