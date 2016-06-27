<?php
if (!defined("IN_IA")) {
    exit("Access Denied");
}
define("TM_COMMISSION_AGENT_NEW", "commission_agent_new");
define("TM_COMMISSION_ORDER_PAY", "commission_order_pay");
define("TM_COMMISSION_ORDER_FINISH", "commission_order_finish");
define("TM_COMMISSION_APPLY", "commission_apply");
define("TM_COMMISSION_CHECK", "commission_check");
define("TM_COMMISSION_PAY", "commission_pay");
define("TM_COMMISSION_UPGRADE", "commission_upgrade");
define("TM_COMMISSION_BECOME", "commission_become");
if (!class_exists("CommissionModel")) {
    class CommissionModel extends PluginModel
    {
        public function getSet($val0 = 0)
        {
            $val1 = parent::getSet($val0);
            $val1["texts"] = array("agent" => empty($val1["texts"]["agent"]) ? "分销商" : $val1["texts"]["agent"], "shop" => empty($val1["texts"]["shop"]) ? "小店" : $val1["texts"]["shop"], "myshop" => empty($val1["texts"]["myshop"]) ? "我的小店" : $val1["texts"]["myshop"], "center" => empty($val1["texts"]["center"]) ? "分销中心" : $val1["texts"]["center"], "become" => empty($val1["texts"]["become"]) ? "成为分销商" : $val1["texts"]["become"], "withdraw" => empty($val1["texts"]["withdraw"]) ? "提现" : $val1["texts"]["withdraw"], "commission" => empty($val1["texts"]["commission"]) ? "佣金" : $val1["texts"]["commission"], "commission1" => empty($val1["texts"]["commission1"]) ? "分销佣金" : $val1["texts"]["commission1"], "commission_total" => empty($val1["texts"]["commission_total"]) ? "累计佣金" : $val1["texts"]["commission_total"], "commission_ok" => empty($val1["texts"]["commission_ok"]) ? "可提现佣金" : $val1["texts"]["commission_ok"], "commission_apply" => empty($val1["texts"]["commission_apply"]) ? "已申请佣金" : $val1["texts"]["commission_apply"], "commission_check" => empty($val1["texts"]["commission_check"]) ? "待打款佣金" : $val1["texts"]["commission_check"], "commission_lock" => empty($val1["texts"]["commission_lock"]) ? "未结算佣金" : $val1["texts"]["commission_lock"], "commission_detail" => empty($val1["texts"]["commission_detail"]) ? "佣金明细" : $val1["texts"]["commission_detail"], "commission_pay" => empty($val1["texts"]["commission_pay"]) ? "成功提现佣金" : $val1["texts"]["commission_pay"], "order" => empty($val1["texts"]["order"]) ? "分销订单" : $val1["texts"]["order"], "myteam" => empty($val1["texts"]["myteam"]) ? "我的团队" : $val1["texts"]["myteam"], "c1" => empty($val1["texts"]["c1"]) ? "一级" : $val1["texts"]["c1"], "c2" => empty($val1["texts"]["c2"]) ? "二级" : $val1["texts"]["c2"], "c3" => empty($val1["texts"]["c3"]) ? "三级" : $val1["texts"]["c3"], "mycustomer" => empty($val1["texts"]["mycustomer"]) ? "我的客户" : $val1["texts"]["mycustomer"]);
            return $val1;
        }
        public function calculate($val47 = 0, $val48 = true)
        {
            global $_W;
            $val1 = $this->getSet();
            $val52 = $this->getLevels();
            $val54 = pdo_fetchcolumn("select agentid from " . tablename("ewei_shop_order") . " where id=:id limit 1", array(":id" => $val47));
            $val56 = pdo_fetchall("select og.id,og.realprice,og.total,g.hascommission,g.nocommission, g.commission1_rate,g.commission1_pay,g.commission2_rate,g.commission2_pay,g.commission3_rate,g.commission3_pay,og.commissions from " . tablename("ewei_shop_order_goods") . "  og " . " left join " . tablename("ewei_shop_goods") . " g on g.id = og.goodsid" . " where og.orderid=:orderid and og.uniacid=:uniacid", array(":orderid" => $val47, ":uniacid" => $_W["uniacid"]));
            if ($val1["level"] > 0) {
                foreach ($val56 as &$val61) {
                    $val62 = $val61["realprice"];
                    if (empty($val61["nocommission"])) {
                        if ($val61["hascommission"] == 1) {
                            $val61["commission1"] = array("default" => $val1["level"] >= 1 ? $val61["commission1_rate"] > 0 ? round($val61["commission1_rate"] * $val62 / 100, 2) . "" : round($val61["commission1_pay"] * $val61["total"], 2) : 0);
                            $val61["commission2"] = array("default" => $val1["level"] >= 2 ? $val61["commission2_rate"] > 0 ? round($val61["commission2_rate"] * $val62 / 100, 2) . "" : round($val61["commission2_pay"] * $val61["total"], 2) : 0);
                            $val61["commission3"] = array("default" => $val1["level"] >= 3 ? $val61["commission3_rate"] > 0 ? round($val61["commission3_rate"] * $val62 / 100, 2) . "" : round($val61["commission3_pay"] * $val61["total"], 2) : 0);
                            foreach ($val52 as $val88) {
                                $val61["commission1"]["level" . $val88["id"]] = $val61["commission1_rate"] > 0 ? round($val61["commission1_rate"] * $val62 / 100, 2) . "" : round($val61["commission1_pay"] * $val61["total"], 2);
                                $val61["commission2"]["level" . $val88["id"]] = $val61["commission2_rate"] > 0 ? round($val61["commission2_rate"] * $val62 / 100, 2) . "" : round($val61["commission2_pay"] * $val61["total"], 2);
                                $val61["commission3"]["level" . $val88["id"]] = $val61["commission3_rate"] > 0 ? round($val61["commission3_rate"] * $val62 / 100, 2) . "" : round($val61["commission3_pay"] * $val61["total"], 2);
                            }
                        } else {
                            $val61["commission1"] = array("default" => $val1["level"] >= 1 ? round($val1["commission1"] * $val62 / 100, 2) . "" : 0);
                            $val61["commission2"] = array("default" => $val1["level"] >= 2 ? round($val1["commission2"] * $val62 / 100, 2) . "" : 0);
                            $val61["commission3"] = array("default" => $val1["level"] >= 3 ? round($val1["commission3"] * $val62 / 100, 2) . "" : 0);
                            foreach ($val52 as $val88) {
                                $val61["commission1"]["level" . $val88["id"]] = $val1["level"] >= 1 ? round($val88["commission1"] * $val62 / 100, 2) . "" : 0;
                                $val61["commission2"]["level" . $val88["id"]] = $val1["level"] >= 2 ? round($val88["commission2"] * $val62 / 100, 2) . "" : 0;
                                $val61["commission3"]["level" . $val88["id"]] = $val1["level"] >= 3 ? round($val88["commission3"] * $val62 / 100, 2) . "" : 0;
                            }
                        }
                    } else {
                        $val61["commission1"] = array("default" => 0);
                        $val61["commission2"] = array("default" => 0);
                        $val61["commission3"] = array("default" => 0);
                        foreach ($val52 as $val88) {
                            $val61["commission1"]["level" . $val88["id"]] = 0;
                            $val61["commission2"]["level" . $val88["id"]] = 0;
                            $val61["commission3"]["level" . $val88["id"]] = 0;
                        }
                    }
                    if ($val48) {
                        $val151 = array("level1" => 0, "level2" => 0, "level3" => 0);
                        if (!empty($val54)) {
                            $val153 = m("member")->getMember($val54);
                            if ($val153["isagent"] == 1 && $val153["status"] == 1) {
                                $val157 = $this->getLevel($val153["openid"]);
                                $val151["level1"] = empty($val157) ? round($val61["commission1"]["default"], 2) : round($val61["commission1"]["level" . $val157["id"]], 2);
                                if (!empty($val153["agentid"])) {
                                    $val165 = m("member")->getMember($val153["agentid"]);
                                    $val167 = $this->getLevel($val165["openid"]);
                                    $val151["level2"] = empty($val167) ? round($val61["commission2"]["default"], 2) : round($val61["commission2"]["level" . $val167["id"]], 2);
                                    if (!empty($val165["agentid"])) {
                                        $val175 = m("member")->getMember($val165["agentid"]);
                                        $val177 = $this->getLevel($val175["openid"]);
                                        $val151["level3"] = empty($val177) ? round($val61["commission3"]["default"], 2) : round($val61["commission3"]["level" . $val177["id"]], 2);
                                    }
                                }
                            }
                        }
                        pdo_update("ewei_shop_order_goods", array("commission1" => iserializer($val61["commission1"]), "commission2" => iserializer($val61["commission2"]), "commission3" => iserializer($val61["commission3"]), "commissions" => iserializer($val151), "nocommission" => $val61["nocommission"]), array("id" => $val61["id"]));
                    }
                }
                unset($val61);
            }
            return $val56;
        }
        public function getOrderCommissions($val47 = 0, $val193 = 0)
        {
            global $_W;
            $val1 = $this->getSet();
            $val54 = pdo_fetchcolumn("select agentid from " . tablename("ewei_shop_order") . " where id=:id limit 1", array(":id" => $val47));
            $val56 = pdo_fetch("select commission1,commission2,commission3 from " . tablename("ewei_shop_order_goods") . " where id=:id and orderid=:orderid and uniacid=:uniacid and nocommission=0 limit 1", array(":id" => $val193, ":orderid" => $val47, ":uniacid" => $_W["uniacid"]));
            $val151 = array("level1" => 0, "level2" => 0, "level3" => 0);
            if ($val1["level"] > 0) {
                $val205 = iunserializer($val56["commission1"]);
                $val207 = iunserializer($val56["commission2"]);
                $val209 = iunserializer($val56["commission3"]);
                if (!empty($val54)) {
                    $val153 = m("member")->getMember($val54);
                    if ($val153["isagent"] == 1 && $val153["status"] == 1) {
                        $val157 = $this->getLevel($val153["openid"]);
                        $val151["level1"] = empty($val157) ? round($val205["default"], 2) : round($val205["level" . $val157["id"]], 2);
                        if (!empty($val153["agentid"])) {
                            $val165 = m("member")->getMember($val153["agentid"]);
                            $val167 = $this->getLevel($val165["openid"]);
                            $val151["level2"] = empty($val167) ? round($val207["default"], 2) : round($val207["level" . $val167["id"]], 2);
                            if (!empty($val165["agentid"])) {
                                $val175 = m("member")->getMember($val165["agentid"]);
                                $val177 = $this->getLevel($val175["openid"]);
                                $val151["level3"] = empty($val177) ? round($val209["default"], 2) : round($val209["level" . $val177["id"]], 2);
                            }
                        }
                    }
                }
            }
            return $val151;
        }
        public function getInfo($val244, $val245 = null)
        {
            if (empty($val245) || !is_array($val245)) {
                $val245 = array();
            }
            global $_W;
            $val1 = $this->getSet();
            $val88 = intval($val1["level"]);
            $val254 = m("member")->getMember($val244);
            $val256 = $this->getLevel($val244);
            $val258 = time();
            $val259 = intval($val1["settledays"]) * 3600 * 24;
            $val261 = 0;
            $val262 = 0;
            $val263 = 0;
            $val264 = 0;
            $val265 = 0;
            $val266 = 0;
            $val267 = 0;
            $val268 = 0;
            $val269 = 0;
            $val270 = 0;
            $val271 = 0;
            $val272 = 0;
            $val273 = 0;
            $val274 = 0;
            $val275 = 0;
            $val276 = 0;
            $val277 = 0;
            $val278 = 0;
            $val279 = 0;
            $val280 = 0;
            $val281 = 0;
            $val282 = 0;
            $val283 = 0;
            $val284 = 0;
            $val285 = 0;
            $val286 = 0;
            $val287 = 0;
            $val288 = 0;
            if ($val88 >= 1) {
                if (in_array("ordercount0", $val245)) {
                    $val291 = pdo_fetch("select sum(og.realprice) as ordermoney,count(distinct o.id) as ordercount from " . tablename("ewei_shop_order") . " o " . " left join  " . tablename("ewei_shop_order_goods") . " og on og.orderid=o.id " . " where o.agentid=:agentid and o.status>=0 and og.status1>=0 and og.nocommission=0 and o.uniacid=:uniacid  limit 1", array(":uniacid" => $_W["uniacid"], ":agentid" => $val254["id"]));
                    $val277 += $val291["ordercount"];
                    $val262 += $val291["ordercount"];
                    $val263 += $val291["ordermoney"];
                }
                if (in_array("ordercount", $val245)) {
                    $val291 = pdo_fetch("select sum(og.realprice) as ordermoney,count(distinct o.id) as ordercount from " . tablename("ewei_shop_order") . " o " . " left join  " . tablename("ewei_shop_order_goods") . " og on og.orderid=o.id " . " where o.agentid=:agentid and o.status>=1 and og.status1>=0 and og.nocommission=0 and o.uniacid=:uniacid  limit 1", array(":uniacid" => $_W["uniacid"], ":agentid" => $val254["id"]));
                    $val280 += $val291["ordercount"];
                    $val264 += $val291["ordercount"];
                    $val265 += $val291["ordermoney"];
                }
                if (in_array("ordercount3", $val245)) {
                    $val311 = pdo_fetch("select sum(og.realprice) as ordermoney,count(distinct o.id) as ordercount from " . tablename("ewei_shop_order") . " o " . " left join  " . tablename("ewei_shop_order_goods") . " og on og.orderid=o.id " . " where o.agentid=:agentid and o.status>=3 and og.status1>=0 and og.nocommission=0 and o.uniacid=:uniacid  limit 1", array(":uniacid" => $_W["uniacid"], ":agentid" => $val254["id"]));
                    $val283 += $val311["ordercount"];
                    $val266 += $val311["ordercount"];
                    $val267 += $val311["ordermoney"];
                    $val286 += $val311["ordermoney"];
                }
                if (in_array("total", $val245)) {
                    $val323 = pdo_fetchall("select og.commission1,og.commissions  from " . tablename("ewei_shop_order_goods") . " og " . " left join  " . tablename("ewei_shop_order") . " o on o.id = og.orderid" . " where o.agentid=:agentid and o.status>=1 and og.nocommission=0 and o.uniacid=:uniacid", array(":uniacid" => $_W["uniacid"], ":agentid" => $val254["id"]));
                    foreach ($val323 as $val327) {
                        $val151 = iunserializer($val327["commissions"]);
                        $val330 = iunserializer($val327["commission1"]);
                        if (empty($val151)) {
                            $val268 += isset($val330["level" . $val256["id"]]) ? $val330["level" . $val256["id"]] : $val330["default"];
                        } else {
                            $val268 += isset($val151["level1"]) ? floatval($val151["level1"]) : 0;
                        }
                    }
                }
                if (in_array("ok", $val245)) {
                    $val323 = pdo_fetchall("select og.commission1,og.commissions  from " . tablename("ewei_shop_order_goods") . " og " . " left join  " . tablename("ewei_shop_order") . " o on o.id = og.orderid" . " where o.agentid=:agentid and o.status>=3 and og.nocommission=0 and ({$val258} - o.finishtime > {$val259}) and og.status1=0  and o.uniacid=:uniacid", array(":uniacid" => $_W["uniacid"], ":agentid" => $val254["id"]));
                    foreach ($val323 as $val327) {
                        $val151 = iunserializer($val327["commissions"]);
                        $val330 = iunserializer($val327["commission1"]);
                        if (empty($val151)) {
                            $val269 += isset($val330["level" . $val256["id"]]) ? $val330["level" . $val256["id"]] : $val330["default"];
                        } else {
                            $val269 += isset($val151["level1"]) ? $val151["level1"] : 0;
                        }
                    }
                }
                if (in_array("lock", $val245)) {
                    $val365 = pdo_fetchall("select og.commission1,og.commissions  from " . tablename("ewei_shop_order_goods") . " og " . " left join  " . tablename("ewei_shop_order") . " o on o.id = og.orderid" . " where o.agentid=:agentid and o.status>=3 and og.nocommission=0 and ({$val258} - o.finishtime <= {$val259})  and og.status1=0  and o.uniacid=:uniacid", array(":uniacid" => $_W["uniacid"], ":agentid" => $val254["id"]));
                    foreach ($val365 as $val327) {
                        $val151 = iunserializer($val327["commissions"]);
                        $val330 = iunserializer($val327["commission1"]);
                        if (empty($val151)) {
                            $val272 += isset($val330["level" . $val256["id"]]) ? $val330["level" . $val256["id"]] : $val330["default"];
                        } else {
                            $val272 += isset($val151["level1"]) ? $val151["level1"] : 0;
                        }
                    }
                }
                if (in_array("apply", $val245)) {
                    $val387 = pdo_fetchall("select og.commission1,og.commissions  from " . tablename("ewei_shop_order_goods") . " og " . " left join  " . tablename("ewei_shop_order") . " o on o.id = og.orderid" . " where o.agentid=:agentid and o.status>=3 and og.status1=1 and og.nocommission=0 and o.uniacid=:uniacid", array(":uniacid" => $_W["uniacid"], ":agentid" => $val254["id"]));
                    foreach ($val387 as $val327) {
                        $val151 = iunserializer($val327["commissions"]);
                        $val330 = iunserializer($val327["commission1"]);
                        if (empty($val151)) {
                            $val270 += isset($val330["level" . $val256["id"]]) ? $val330["level" . $val256["id"]] : $val330["default"];
                        } else {
                            $val270 += isset($val151["level1"]) ? $val151["level1"] : 0;
                        }
                    }
                }
                if (in_array("check", $val245)) {
                    $val387 = pdo_fetchall("select og.commission1,og.commissions  from " . tablename("ewei_shop_order_goods") . " og " . " left join  " . tablename("ewei_shop_order") . " o on o.id = og.orderid" . " where o.agentid=:agentid and o.status>=3 and og.status1=2 and og.nocommission=0 and o.uniacid=:uniacid ", array(":uniacid" => $_W["uniacid"], ":agentid" => $val254["id"]));
                    foreach ($val387 as $val327) {
                        $val151 = iunserializer($val327["commissions"]);
                        $val330 = iunserializer($val327["commission1"]);
                        if (empty($val151)) {
                            $val271 += isset($val330["level" . $val256["id"]]) ? $val330["level" . $val256["id"]] : $val330["default"];
                        } else {
                            $val271 += isset($val151["level1"]) ? $val151["level1"] : 0;
                        }
                    }
                }
                if (in_array("pay", $val245)) {
                    $val387 = pdo_fetchall("select og.commission1,og.commissions  from " . tablename("ewei_shop_order_goods") . " og " . " left join  " . tablename("ewei_shop_order") . " o on o.id = og.orderid" . " where o.agentid=:agentid and o.status>=3 and og.status1=3 and og.nocommission=0 and o.uniacid=:uniacid ", array(":uniacid" => $_W["uniacid"], ":agentid" => $val254["id"]));
                    foreach ($val387 as $val327) {
                        $val151 = iunserializer($val327["commissions"]);
                        $val330 = iunserializer($val327["commission1"]);
                        if (empty($val151)) {
                            $val273 += isset($val330["level" . $val256["id"]]) ? $val330["level" . $val256["id"]] : $val330["default"];
                        } else {
                            $val273 += isset($val151["level1"]) ? $val151["level1"] : 0;
                        }
                    }
                }
                $val446 = pdo_fetchall("select id from " . tablename("ewei_shop_member") . " where agentid=:agentid and isagent=1 and status=1 and uniacid=:uniacid ", array(":uniacid" => $_W["uniacid"], ":agentid" => $val254["id"]), "id");
                $val274 = count($val446);
                $val261 += $val274;
            }
            if ($val88 >= 2) {
                if ($val274 > 0) {
                    if (in_array("ordercount0", $val245)) {
                        $val456 = pdo_fetch("select sum(og.realprice) as ordermoney,count(distinct o.id) as ordercount from " . tablename("ewei_shop_order") . " o " . " left join  " . tablename("ewei_shop_order_goods") . " og on og.orderid=o.id " . " where o.agentid in( " . implode(",", array_keys($val446)) . ")  and o.status>=0 and og.status2>=0 and og.nocommission=0 and o.uniacid=:uniacid limit 1", array(":uniacid" => $_W["uniacid"]));
                        $val278 += $val456["ordercount"];
                        $val262 += $val456["ordercount"];
                        $val263 += $val456["ordermoney"];
                    }
                    if (in_array("ordercount", $val245)) {
                        $val456 = pdo_fetch("select sum(og.realprice) as ordermoney,count(distinct o.id) as ordercount from " . tablename("ewei_shop_order") . " o " . " left join  " . tablename("ewei_shop_order_goods") . " og on og.orderid=o.id " . " where o.agentid in( " . implode(",", array_keys($val446)) . ")  and o.status>=1 and og.status2>=0 and og.nocommission=0 and o.uniacid=:uniacid limit 1", array(":uniacid" => $_W["uniacid"]));
                        $val281 += $val456["ordercount"];
                        $val264 += $val456["ordercount"];
                        $val265 += $val456["ordermoney"];
                    }
                    if (in_array("ordercount3", $val245)) {
                        $val476 = pdo_fetch("select sum(og.realprice) as ordermoney,count(distinct o.id) as ordercount from " . tablename("ewei_shop_order") . " o " . " left join  " . tablename("ewei_shop_order_goods") . " og on og.orderid=o.id " . " where o.agentid in( " . implode(",", array_keys($val446)) . ")  and o.status>=3 and og.status2>=0 and og.nocommission=0 and o.uniacid=:uniacid limit 1", array(":uniacid" => $_W["uniacid"]));
                        $val284 += $val476["ordercount"];
                        $val266 += $val476["ordercount"];
                        $val267 += $val476["ordermoney"];
                        $val287 += $val476["ordermoney"];
                    }
                    if (in_array("total", $val245)) {
                        $val488 = pdo_fetchall("select og.commission2,og.commissions from " . tablename("ewei_shop_order_goods") . " og " . " left join  " . tablename("ewei_shop_order") . " o on o.id = og.orderid " . " where o.agentid in( " . implode(",", array_keys($val446)) . ")  and o.status>=1 and og.nocommission=0 and o.uniacid=:uniacid", array(":uniacid" => $_W["uniacid"]));
                        foreach ($val488 as $val327) {
                            $val151 = iunserializer($val327["commissions"]);
                            $val330 = iunserializer($val327["commission2"]);
                            if (empty($val151)) {
                                $val268 += isset($val330["level" . $val256["id"]]) ? $val330["level" . $val256["id"]] : $val330["default"];
                            } else {
                                $val268 += isset($val151["level2"]) ? $val151["level2"] : 0;
                            }
                        }
                    }
                    if (in_array("ok", $val245)) {
                        $val488 = pdo_fetchall("select og.commission2,og.commissions  from " . tablename("ewei_shop_order_goods") . " og " . " left join  " . tablename("ewei_shop_order") . " o on o.id = og.orderid " . " where o.agentid in( " . implode(",", array_keys($val446)) . ")  and ({$val258} - o.finishtime > {$val259}) and o.status>=3 and og.status2=0 and og.nocommission=0  and o.uniacid=:uniacid", array(":uniacid" => $_W["uniacid"]));
                        foreach ($val488 as $val327) {
                            $val151 = iunserializer($val327["commissions"]);
                            $val330 = iunserializer($val327["commission2"]);
                            if (empty($val151)) {
                                $val269 += isset($val330["level" . $val256["id"]]) ? $val330["level" . $val256["id"]] : $val330["default"];
                            } else {
                                $val269 += isset($val151["level2"]) ? $val151["level2"] : 0;
                            }
                        }
                    }
                    if (in_array("lock", $val245)) {
                        $val530 = pdo_fetchall("select og.commission2,og.commissions  from " . tablename("ewei_shop_order_goods") . " og " . " left join  " . tablename("ewei_shop_order") . " o on o.id = og.orderid " . " where o.agentid in( " . implode(",", array_keys($val446)) . ")  and ({$val258} - o.finishtime <= {$val259}) and og.status2=0 and o.status>=3 and og.nocommission=0 and o.uniacid=:uniacid", array(":uniacid" => $_W["uniacid"]));
                        foreach ($val530 as $val327) {
                            $val151 = iunserializer($val327["commissions"]);
                            $val330 = iunserializer($val327["commission2"]);
                            if (empty($val151)) {
                                $val272 += isset($val330["level" . $val256["id"]]) ? $val330["level" . $val256["id"]] : $val330["default"];
                            } else {
                                $val272 += isset($val151["level2"]) ? $val151["level2"] : 0;
                            }
                        }
                    }
                    if (in_array("apply", $val245)) {
                        $val552 = pdo_fetchall("select og.commission2,og.commissions  from " . tablename("ewei_shop_order_goods") . " og " . " left join  " . tablename("ewei_shop_order") . " o on o.id = og.orderid " . " where o.agentid in( " . implode(",", array_keys($val446)) . ")  and o.status>=3 and og.status2=1 and og.nocommission=0 and o.uniacid=:uniacid", array(":uniacid" => $_W["uniacid"]));
                        foreach ($val552 as $val327) {
                            $val151 = iunserializer($val327["commissions"]);
                            $val330 = iunserializer($val327["commission2"]);
                            if (empty($val151)) {
                                $val270 += isset($val330["level" . $val256["id"]]) ? $val330["level" . $val256["id"]] : $val330["default"];
                            } else {
                                $val270 += isset($val151["level2"]) ? $val151["level2"] : 0;
                            }
                        }
                    }
                    if (in_array("check", $val245)) {
                        $val572 = pdo_fetchall("select og.commission2,og.commissions  from " . tablename("ewei_shop_order_goods") . " og " . " left join  " . tablename("ewei_shop_order") . " o on o.id = og.orderid " . " where o.agentid in( " . implode(",", array_keys($val446)) . ")  and o.status>=3 and og.status2=2 and og.nocommission=0 and o.uniacid=:uniacid", array(":uniacid" => $_W["uniacid"]));
                        foreach ($val572 as $val327) {
                            $val151 = iunserializer($val327["commissions"]);
                            $val330 = iunserializer($val327["commission2"]);
                            if (empty($val151)) {
                                $val271 += isset($val330["level" . $val256["id"]]) ? $val330["level" . $val256["id"]] : $val330["default"];
                            } else {
                                $val271 += isset($val151["level2"]) ? $val151["level2"] : 0;
                            }
                        }
                    }
                    if (in_array("pay", $val245)) {
                        $val572 = pdo_fetchall("select og.commission2,og.commissions  from " . tablename("ewei_shop_order_goods") . " og " . " left join  " . tablename("ewei_shop_order") . " o on o.id = og.orderid " . " where o.agentid in( " . implode(",", array_keys($val446)) . ")  and o.status>=3 and og.status2=3 and og.nocommission=0 and o.uniacid=:uniacid", array(":uniacid" => $_W["uniacid"]));
                        foreach ($val572 as $val327) {
                            $val151 = iunserializer($val327["commissions"]);
                            $val330 = iunserializer($val327["commission2"]);
                            if (empty($val151)) {
                                $val273 += isset($val330["level" . $val256["id"]]) ? $val330["level" . $val256["id"]] : $val330["default"];
                            } else {
                                $val273 += isset($val151["level2"]) ? $val151["level2"] : 0;
                            }
                        }
                    }
                    $val611 = pdo_fetchall("select id from " . tablename("ewei_shop_member") . " where agentid in( " . implode(",", array_keys($val446)) . ") and isagent=1 and status=1 and uniacid=:uniacid", array(":uniacid" => $_W["uniacid"]), "id");
                    $val275 = count($val611);
                    $val261 += $val275;
                }
            }
            if ($val88 >= 3) {
                if ($val275 > 0) {
                    if (in_array("ordercount0", $val245)) {
                        $val621 = pdo_fetch("select sum(og.realprice) as ordermoney,count(distinct og.orderid) as ordercount from " . tablename("ewei_shop_order") . " o " . " left join  " . tablename("ewei_shop_order_goods") . " og on og.orderid=o.id " . " where o.agentid in( " . implode(",", array_keys($val611)) . ")  and o.status>=0 and og.status3>=0 and og.nocommission=0 and o.uniacid=:uniacid limit 1", array(":uniacid" => $_W["uniacid"]));
                        $val279 += $val621["ordercount"];
                        $val262 += $val621["ordercount"];
                        $val263 += $val621["ordermoney"];
                    }
                    if (in_array("ordercount", $val245)) {
                        $val621 = pdo_fetch("select sum(og.realprice) as ordermoney,count(distinct og.orderid) as ordercount from " . tablename("ewei_shop_order") . " o " . " left join  " . tablename("ewei_shop_order_goods") . " og on og.orderid=o.id " . " where o.agentid in( " . implode(",", array_keys($val611)) . ")  and o.status>=1 and og.status3>=0 and og.nocommission=0 and o.uniacid=:uniacid limit 1", array(":uniacid" => $_W["uniacid"]));
                        $val282 += $val621["ordercount"];
                        $val264 += $val621["ordercount"];
                        $val265 += $val621["ordermoney"];
                    }
                    if (in_array("ordercount3", $val245)) {
                        $val641 = pdo_fetch("select sum(og.realprice) as ordermoney,count(distinct og.orderid) as ordercount from " . tablename("ewei_shop_order") . " o " . " left join  " . tablename("ewei_shop_order_goods") . " og on og.orderid=o.id " . " where o.agentid in( " . implode(",", array_keys($val611)) . ")  and o.status>=3 and og.status3>=0 and og.nocommission=0 and o.uniacid=:uniacid limit 1", array(":uniacid" => $_W["uniacid"]));
                        $val285 += $val641["ordercount"];
                        $val266 += $val641["ordercount"];
                        $val267 += $val641["ordermoney"];
                        $val288 += $val621["ordermoney"];
                    }
                    if (in_array("total", $val245)) {
                        $val653 = pdo_fetchall("select og.commission3,og.commissions  from " . tablename("ewei_shop_order_goods") . " og " . " left join  " . tablename("ewei_shop_order") . " o on o.id = og.orderid" . " where o.agentid in( " . implode(",", array_keys($val611)) . ")  and o.status>=1 and og.nocommission=0 and o.uniacid=:uniacid", array(":uniacid" => $_W["uniacid"]));
                        foreach ($val653 as $val327) {
                            $val151 = iunserializer($val327["commissions"]);
                            $val330 = iunserializer($val327["commission3"]);
                            if (empty($val151)) {
                                $val268 += isset($val330["level" . $val256["id"]]) ? $val330["level" . $val256["id"]] : $val330["default"];
                            } else {
                                $val268 += isset($val151["level3"]) ? $val151["level3"] : 0;
                            }
                        }
                    }
                    if (in_array("ok", $val245)) {
                        $val653 = pdo_fetchall("select og.commission3,og.commissions  from " . tablename("ewei_shop_order_goods") . " og " . " left join  " . tablename("ewei_shop_order") . " o on o.id = og.orderid" . " where o.agentid in( " . implode(",", array_keys($val611)) . ")  and ({$val258} - o.finishtime > {$val259}) and o.status>=3 and og.status3=0  and og.nocommission=0 and o.uniacid=:uniacid", array(":uniacid" => $_W["uniacid"]));
                        foreach ($val653 as $val327) {
                            $val151 = iunserializer($val327["commissions"]);
                            $val330 = iunserializer($val327["commission3"]);
                            if (empty($val151)) {
                                $val269 += isset($val330["level" . $val256["id"]]) ? $val330["level" . $val256["id"]] : $val330["default"];
                            } else {
                                $val269 += isset($val151["level3"]) ? $val151["level3"] : 0;
                            }
                        }
                    }
                    if (in_array("lock", $val245)) {
                        $val695 = pdo_fetchall("select og.commission3,og.commissions  from " . tablename("ewei_shop_order_goods") . " og " . " left join  " . tablename("ewei_shop_order") . " o on o.id = og.orderid" . " where o.agentid in( " . implode(",", array_keys($val611)) . ")  and o.status>=3 and ({$val258} - o.finishtime > {$val259}) and og.status3=0  and og.nocommission=0 and o.uniacid=:uniacid", array(":uniacid" => $_W["uniacid"]));
                        foreach ($val695 as $val327) {
                            $val151 = iunserializer($val327["commissions"]);
                            $val330 = iunserializer($val327["commission3"]);
                            if (empty($val151)) {
                                $val272 += isset($val330["level" . $val256["id"]]) ? $val330["level" . $val256["id"]] : $val330["default"];
                            } else {
                                $val272 += isset($val151["level3"]) ? $val151["level3"] : 0;
                            }
                        }
                    }
                    if (in_array("apply", $val245)) {
                        $val717 = pdo_fetchall("select og.commission3,og.commissions  from " . tablename("ewei_shop_order_goods") . " og " . " left join  " . tablename("ewei_shop_order") . " o on o.id = og.orderid" . " where o.agentid in( " . implode(",", array_keys($val611)) . ")  and o.status>=3 and og.status3=1 and og.nocommission=0 and o.uniacid=:uniacid", array(":uniacid" => $_W["uniacid"]));
                        foreach ($val717 as $val327) {
                            $val151 = iunserializer($val327["commissions"]);
                            $val330 = iunserializer($val327["commission3"]);
                            if (empty($val151)) {
                                $val270 += isset($val330["level" . $val256["id"]]) ? $val330["level" . $val256["id"]] : $val330["default"];
                            } else {
                                $val270 += isset($val151["level3"]) ? $val151["level3"] : 0;
                            }
                        }
                    }
                    if (in_array("check", $val245)) {
                        $val737 = pdo_fetchall("select og.commission3,og.commissions  from " . tablename("ewei_shop_order_goods") . " og " . " left join  " . tablename("ewei_shop_order") . " o on o.id = og.orderid" . " where o.agentid in( " . implode(",", array_keys($val611)) . ")  and o.status>=3 and og.status3=2 and og.nocommission=0 and o.uniacid=:uniacid", array(":uniacid" => $_W["uniacid"]));
                        foreach ($val737 as $val327) {
                            $val151 = iunserializer($val327["commissions"]);
                            $val330 = iunserializer($val327["commission3"]);
                            if (empty($val151)) {
                                $val271 += isset($val330["level" . $val256["id"]]) ? $val330["level" . $val256["id"]] : $val330["default"];
                            } else {
                                $val271 += isset($val151["level3"]) ? $val151["level3"] : 0;
                            }
                        }
                    }
                    if (in_array("pay", $val245)) {
                        $val737 = pdo_fetchall("select og.commission3,og.commissions  from " . tablename("ewei_shop_order_goods") . " og " . " left join  " . tablename("ewei_shop_order") . " o on o.id = og.orderid" . " where o.agentid in( " . implode(",", array_keys($val611)) . ")  and o.status>=3 and og.status3=3 and og.nocommission=0 and o.uniacid=:uniacid", array(":uniacid" => $_W["uniacid"]));
                        foreach ($val737 as $val327) {
                            $val151 = iunserializer($val327["commissions"]);
                            $val330 = iunserializer($val327["commission3"]);
                            if (empty($val151)) {
                                $val273 += isset($val330["level" . $val256["id"]]) ? $val330["level" . $val256["id"]] : $val330["default"];
                            } else {
                                $val273 += isset($val151["level3"]) ? $val151["level3"] : 0;
                            }
                        }
                    }
                    $val776 = pdo_fetchall("select id from " . tablename("ewei_shop_member") . " where uniacid=:uniacid and agentid in( " . implode(",", array_keys($val611)) . ") and isagent=1 and status=1", array(":uniacid" => $_W["uniacid"]), "id");
                    $val276 = count($val776);
                    $val261 += $val276;
                }
            }
            $val254["agentcount"] = $val261;
            $val254["ordercount"] = $val264;
            $val254["ordermoney"] = $val265;
            $val254["order1"] = $val280;
            $val254["order2"] = $val281;
            $val254["order3"] = $val282;
            $val254["ordercount3"] = $val266;
            $val254["ordermoney3"] = $val267;
            $val254["order13"] = $val283;
            $val254["order23"] = $val284;
            $val254["order33"] = $val285;
            $val254["order13money"] = $val286;
            $val254["order23money"] = $val287;
            $val254["order33money"] = $val288;
            $val254["ordercount0"] = $val262;
            $val254["ordermoney0"] = $val263;
            $val254["order10"] = $val277;
            $val254["order20"] = $val278;
            $val254["order30"] = $val279;
            $val254["commission_total"] = round($val268, 2);
            $val254["commission_ok"] = round($val269, 2);
            $val254["commission_lock"] = round($val272, 2);
            $val254["commission_apply"] = round($val270, 2);
            $val254["commission_check"] = round($val271, 2);
            $val254["commission_pay"] = round($val273, 2);
            $val254["level1"] = $val274;
            $val254["level1_agentids"] = $val446;
            $val254["level2"] = $val275;
            $val254["level2_agentids"] = $val611;
            $val254["level3"] = $val276;
            $val254["level3_agentids"] = $val776;
            $val254["agenttime"] = date("Y-m-d H:i", $val254["agenttime"]);
            return $val254;
        }
        public function getAgents($val47 = 0)
        {
            global $_W, $_GPC;
            $val851 = array();
            $val852 = pdo_fetch("select id,agentid,openid from " . tablename("ewei_shop_order") . " where id=:id and uniacid=:uniacid limit 1", array(":id" => $val47, ":uniacid" => $_W["uniacid"]));
            if (empty($val852)) {
                return $val851;
            }
            $val153 = m("member")->getMember($val852["agentid"]);
            if (!empty($val153) && $val153["isagent"] == 1 && $val153["status"] == 1) {
                $val851[] = $val153;
                if (!empty($val153["agentid"])) {
                    $val165 = m("member")->getMember($val153["agentid"]);
                    if (!empty($val165) && $val165["isagent"] == 1 && $val165["status"] == 1) {
                        $val851[] = $val165;
                        if (!empty($val165["agentid"])) {
                            $val175 = m("member")->getMember($val165["agentid"]);
                            if (!empty($val175) && $val175["isagent"] == 1 && $val175["status"] == 1) {
                                $val851[] = $val175;
                            }
                        }
                    }
                }
            }
            return $val851;
        }
        public function isAgent($val244)
        {
            if (empty($val244)) {
                return false;
            }
            if (is_array($val244)) {
                return $val244["isagent"] == 1 && $val244["status"] == 1;
            }
            $val254 = m("member")->getMember($val244);
            return $val254["isagent"] == 1 && $val254["status"] == 1;
        }
        public function getCommission($val56)
        {
            global $_W;
            $val1 = $this->getSet();
            $val330 = 0;
            if ($val56["hascommission"] == 1) {
                $val330 = $val1["level"] >= 1 ? $val56["commission1_rate"] > 0 ? $val56["commission1_rate"] * $val56["marketprice"] / 100 : $val56["commission1_pay"] : 0;
            } else {
                $val244 = m("user")->getOpenid();
                $val88 = $this->getLevel($val244);
                if (!empty($val88)) {
                    $val330 = $val1["level"] >= 1 ? round($val88["commission1"] * $val56["marketprice"] / 100, 2) : 0;
                } else {
                    $val330 = $val1["level"] >= 1 ? round($val1["commission1"] * $val56["marketprice"] / 100, 2) : 0;
                }
            }
            return $val330;
        }
        public function createMyShopQrcode($val915 = 0, $val916 = 0)
        {
            global $_W;
            $val918 = IA_ROOT . "/addons/ewei_shop/data/qrcode/" . $_W["uniacid"];
            if (!is_dir($val918)) {
                load()->func("file");
                mkdirs($val918);
            }
            $val922 = $_W["siteroot"] . "app/index.php?i=" . $_W["uniacid"] . "&c=entry&m=ewei_shop&do=plugin&p=commission&method=myshop&mid=" . $val915;
            if (!empty($val916)) {
                $val922 .= "&posterid=" . $val916;
            }
            $val929 = "myshop_" . $val916 . "_" . $val915 . ".png";
            $val932 = $val918 . "/" . $val929;
            if (!is_file($val932)) {
                require IA_ROOT . "/framework/library/qrcode/phpqrcode.php";
                QRcode::png($val922, $val932, QR_ECLEVEL_H, 4);
            }
            return $_W["siteroot"] . "addons/ewei_shop/data/qrcode/" . $_W["uniacid"] . "/" . $val929;
        }
        private function createImage($val922)
        {
            load()->func("communication");
            $val942 = ihttp_request($val922);
            return imagecreatefromstring($val942["content"]);
        }
        public function createGoodsImage($val56, $val946)
        {
            global $_W, $_GPC;
            $val56 = set_medias($val56, "thumb");
            $val244 = m("user")->getOpenid();
            $val952 = m("member")->getMember($val244);
            if ($val952["isagent"] == 1 && $val952["status"] == 1) {
                $val956 = $val952;
            } else {
                $val915 = intval($_GPC["mid"]);
                if (!empty($val915)) {
                    $val956 = m("member")->getMember($val915);
                }
            }
            $val918 = IA_ROOT . "/addons/ewei_shop/data/poster/" . $_W["uniacid"] . "/";
            if (!is_dir($val918)) {
                load()->func("file");
                mkdirs($val918);
            }
            $val967 = empty($val56["commission_thumb"]) ? $val56["thumb"] : tomedia($val56["commission_thumb"]);
            $val971 = md5(json_encode(array("id" => $val56["id"], "marketprice" => $val56["marketprice"], "productprice" => $val56["productprice"], "img" => $val967, "openid" => $val244, "version" => 4)));
            $val929 = $val971 . ".jpg";
            if (!is_file($val918 . $val929)) {
                set_time_limit(0);
                $val981 = IA_ROOT . "/addons/ewei_shop/static/fonts/msyh.ttf";
                $val982 = imagecreatetruecolor(640, 1225);
                $val983 = imagecreatefromjpeg(IA_ROOT . "/addons/ewei_shop/plugin/commission/images/poster.jpg");
                imagecopy($val982, $val983, 0, 0, 0, 0, 640, 1225);
                imagedestroy($val983);
                $val987 = preg_replace("/\\/0\$/i", "/96", $val956["avatar"]);
                $val990 = $this->createImage($val987);
                $val992 = imagesx($val990);
                $val994 = imagesy($val990);
                imagecopyresized($val982, $val990, 24, 32, 0, 0, 88, 88, $val992, $val994);
                imagedestroy($val990);
                $val1001 = $this->createImage($val967);
                $val992 = imagesx($val1001);
                $val994 = imagesy($val1001);
                imagecopyresized($val982, $val1001, 0, 160, 0, 0, 640, 640, $val992, $val994);
                imagedestroy($val1001);
                $val1012 = imagecreatetruecolor(640, 127);
                imagealphablending($val1012, false);
                imagesavealpha($val1012, true);
                $val1015 = imagecolorallocatealpha($val1012, 0, 0, 0, 25);
                imagefill($val1012, 0, 0, $val1015);
                imagecopy($val982, $val1012, 0, 678, 0, 0, 640, 127);
                imagedestroy($val1012);
                $val1022 = tomedia(m("qrcode")->createGoodsQrcode($val956["id"], $val56["id"]));
                $val1025 = $this->createImage($val1022);
                $val992 = imagesx($val1025);
                $val994 = imagesy($val1025);
                imagecopyresized($val982, $val1025, 50, 835, 0, 0, 250, 250, $val992, $val994);
                imagedestroy($val1025);
                $val1036 = imagecolorallocate($val982, 0, 3, 51);
                $val1038 = imagecolorallocate($val982, 240, 102, 0);
                $val1040 = imagecolorallocate($val982, 255, 255, 255);
                $val1042 = imagecolorallocate($val982, 255, 255, 0);
                $val1044 = "我是";
                imagettftext($val982, 20, 0, 150, 70, $val1036, $val981, $val1044);
                imagettftext($val982, 20, 0, 210, 70, $val1038, $val981, $val956["nickname"]);
                $val1053 = "我要为";
                imagettftext($val982, 20, 0, 150, 105, $val1036, $val981, $val1053);
                $val1058 = $val946["name"];
                imagettftext($val982, 20, 0, 240, 105, $val1038, $val981, $val1058);
                $val1064 = imagettfbbox(20, 0, $val981, $val1058);
                $val1067 = $val1064[4] - $val1064[6];
                $val1070 = "代言";
                imagettftext($val982, 20, 0, 240 + $val1067 + 10, 105, $val1036, $val981, $val1070);
                $val1076 = mb_substr($val56["title"], 0, 50, "utf-8");
                imagettftext($val982, 20, 0, 30, 730, $val1040, $val981, $val1076);
                $val1082 = "￥" . number_format($val56["marketprice"], 2);
                imagettftext($val982, 25, 0, 25, 780, $val1042, $val981, $val1082);
                $val1064 = imagettfbbox(26, 0, $val981, $val1082);
                $val1067 = $val1064[4] - $val1064[6];
                if ($val56["productprice"] > 0) {
                    $val1095 = "￥" . number_format($val56["productprice"], 2);
                    imagettftext($val982, 22, 0, 25 + $val1067 + 10, 780, $val1040, $val981, $val1095);
                    $val1102 = 25 + $val1067 + 10;
                    $val1064 = imagettfbbox(22, 0, $val981, $val1095);
                    $val1067 = $val1064[4] - $val1064[6];
                    imageline($val982, $val1102, 770, $val1102 + $val1067 + 20, 770, $val1040);
                    imageline($val982, $val1102, 771.5, $val1102 + $val1067 + 20, 771, $val1040);
                }
                imagejpeg($val982, $val918 . $val929);
                imagedestroy($val982);
            }
            return $_W["siteroot"] . "addons/ewei_shop/data/poster/" . $_W["uniacid"] . "/" . $val929;
        }
        public function createShopImage($val946)
        {
            global $_W, $_GPC;
            $val946 = set_medias($val946, "signimg");
            $val918 = IA_ROOT . "/addons/ewei_shop/data/poster/" . $_W["uniacid"] . "/";
            if (!is_dir($val918)) {
                load()->func("file");
                mkdirs($val918);
            }
            $val915 = intval($_GPC["mid"]);
            $val244 = m("user")->getOpenid();
            $val952 = m("member")->getMember($val244);
            if ($val952["isagent"] == 1 && $val952["status"] == 1) {
                $val956 = $val952;
            } else {
                $val915 = intval($_GPC["mid"]);
                if (!empty($val915)) {
                    $val956 = m("member")->getMember($val915);
                }
            }
            $val971 = md5(json_encode(array("openid" => $val244, "signimg" => $val946["signimg"], "version" => 4)));
            $val929 = $val971 . ".jpg";
            if (!is_file($val918 . $val929)) {
                set_time_limit(0);
                @ini_set("memory_limit", "256M");
                $val981 = IA_ROOT . "/addons/ewei_shop/static/fonts/msyh.ttf";
                $val982 = imagecreatetruecolor(640, 1225);
                $val1036 = imagecolorallocate($val982, 0, 3, 51);
                $val1038 = imagecolorallocate($val982, 240, 102, 0);
                $val1040 = imagecolorallocate($val982, 255, 255, 255);
                $val1042 = imagecolorallocate($val982, 255, 255, 0);
                $val983 = imagecreatefromjpeg(IA_ROOT . "/addons/ewei_shop/plugin/commission/images/poster.jpg");
                imagecopy($val982, $val983, 0, 0, 0, 0, 640, 1225);
                imagedestroy($val983);
                $val987 = preg_replace("/\\/0\$/i", "/96", $val956["avatar"]);
                $val990 = $this->createImage($val987);
                $val992 = imagesx($val990);
                $val994 = imagesy($val990);
                imagecopyresized($val982, $val990, 24, 32, 0, 0, 88, 88, $val992, $val994);
                imagedestroy($val990);
                $val1001 = $this->createImage($val946["signimg"]);
                $val992 = imagesx($val1001);
                $val994 = imagesy($val1001);
                imagecopyresized($val982, $val1001, 0, 160, 0, 0, 640, 640, $val992, $val994);
                imagedestroy($val1001);
                $val1196 = tomedia($this->createMyShopQrcode($val956["id"]));
                $val1025 = $this->createImage($val1196);
                $val992 = imagesx($val1025);
                $val994 = imagesy($val1025);
                imagecopyresized($val982, $val1025, 50, 835, 0, 0, 250, 250, $val992, $val994);
                imagedestroy($val1025);
                $val1044 = "我是";
                imagettftext($val982, 20, 0, 150, 70, $val1036, $val981, $val1044);
                imagettftext($val982, 20, 0, 210, 70, $val1038, $val981, $val956["nickname"]);
                $val1053 = "我要为";
                imagettftext($val982, 20, 0, 150, 105, $val1036, $val981, $val1053);
                $val1058 = $val946["name"];
                imagettftext($val982, 20, 0, 240, 105, $val1038, $val981, $val1058);
                $val1064 = imagettfbbox(20, 0, $val981, $val1058);
                $val1067 = $val1064[4] - $val1064[6];
                $val1070 = "代言";
                imagettftext($val982, 20, 0, 240 + $val1067 + 10, 105, $val1036, $val981, $val1070);
                imagejpeg($val982, $val918 . $val929);
                imagedestroy($val982);
            }
            return $_W["siteroot"] . "addons/ewei_shop/data/poster/" . $_W["uniacid"] . "/" . $val929;
        }
        public function checkAgent()
        {
            global $_W, $_GPC;
            $val1 = $this->getSet();
            if (empty($val1["level"])) {
                return;
            }
            $val244 = m("user")->getOpenid();
            if (empty($val244)) {
                return;
            }
            $val254 = m("member")->getMember($val244);
            if (empty($val254)) {
                return;
            }
            $val1258 = false;
            $val915 = intval($_GPC["mid"]);
            if (!empty($val915)) {
                $val1258 = m("member")->getMember($val915);
            }
            $val1264 = !empty($val1258) && $val1258["isagent"] == 1 && $val1258["status"] == 1;
            if ($val1264) {
                if ($val1258["openid"] != $val244) {
                    $val1271 = pdo_fetchcolumn("select count(*) from " . tablename("ewei_shop_commission_clickcount") . " where uniacid=:uniacid and openid=:openid and from_openid=:from_openid limit 1", array(":uniacid" => $_W["uniacid"], ":openid" => $val244, ":from_openid" => $val1258["openid"]));
                    if ($val1271 <= 0) {
                        $val1276 = array("uniacid" => $_W["uniacid"], "openid" => $val244, "from_openid" => $val1258["openid"], "clicktime" => time());
                        pdo_insert("ewei_shop_commission_clickcount", $val1276);
                        pdo_update("ewei_shop_member", array("clickcount" => $val1258["clickcount"] + 1), array("uniacid" => $_W["uniacid"], "id" => $val1258["id"]));
                    }
                }
            }
            if ($val254["isagent"] == 1) {
                return;
            }
            if ($val1285 == 0) {
                $val1286 = pdo_fetchcolumn("select count(*) from " . tablename("ewei_shop_member") . " where id<>:id and uniacid=:uniacid limit 1", array(":uniacid" => $_W["uniacid"], ":id" => $val254["id"]));
                if ($val1286 <= 0) {
                    pdo_update("ewei_shop_member", array("isagent" => 1, "status" => 1, "agenttime" => time(), "agentblack" => 0), array("uniacid" => $_W["uniacid"], "id" => $val254["id"]));
                    return;
                }
            }
            $val258 = time();
            $val1293 = intval($val1["become_child"]);
            if ($val1264 && empty($val254["agentid"])) {
                if ($val254["id"] != $val1258["id"]) {
                    if (empty($val1293)) {
                        if (empty($val254["fixagentid"])) {
                            pdo_update("ewei_shop_member", array("agentid" => $val1258["id"], "childtime" => $val258), array("uniacid" => $_W["uniacid"], "id" => $val254["id"]));
                            $this->sendMessage($val1258["openid"], array("nickname" => $val254["nickname"], "childtime" => $val258), TM_COMMISSION_AGENT_NEW);
                            $this->upgradeLevelByAgent($val1258["id"]);
                        }
                    } else {
                        pdo_update("ewei_shop_member", array("inviter" => $val1258["id"]), array("uniacid" => $_W["uniacid"], "id" => $val254["id"]));
                    }
                }
            }
            $val1312 = intval($val1["become_check"]);
            if (empty($val1["become"])) {
                if (empty($val254["agentblack"])) {
                    pdo_update("ewei_shop_member", array("isagent" => 1, "status" => $val1312, "agenttime" => $val1312 == 1 ? $val258 : 0), array("uniacid" => $_W["uniacid"], "id" => $val254["id"]));
                    if ($val1312 == 1) {
                        $this->sendMessage($val244, array("nickname" => $val254["nickname"], "agenttime" => $val258), TM_COMMISSION_BECOME);
                        if ($val1264) {
                            $this->upgradeLevelByAgent($val1258["id"]);
                        }
                    }
                }
            }
        }
        public function checkOrderConfirm($val47 = '0')
        {
            global $_W, $_GPC;
            if (empty($val47)) {
                return;
            }
            $val1 = $this->getSet();
            if (empty($val1["level"])) {
                return;
            }
            $val852 = pdo_fetch("select id,openid,ordersn,goodsprice,agentid,paytime from " . tablename("ewei_shop_order") . " where id=:id and status>=0 and uniacid=:uniacid limit 1", array(":id" => $val47, ":uniacid" => $_W["uniacid"]));
            if (empty($val852)) {
                return;
            }
            $val244 = $val852["openid"];
            $val254 = m("member")->getMember($val244);
            if (empty($val254)) {
                return;
            }
            $val1293 = intval($val1["become_child"]);
            $val1258 = false;
            if (empty($val1293)) {
                $val1258 = m("member")->getMember($val254["agentid"]);
            } else {
                $val1258 = m("member")->getMember($val254["inviter"]);
            }
            $val1264 = !empty($val1258) && $val1258["isagent"] == 1 && $val1258["status"] == 1;
            $val258 = time();
            $val1293 = intval($val1["become_child"]);
            if ($val1264) {
                if ($val1293 == 1) {
                    if (empty($val254["agentid"]) && $val254["id"] != $val1258["id"]) {
                        if (empty($val254["fixagentid"])) {
                            $val254["agentid"] = $val1258["id"];
                            pdo_update("ewei_shop_member", array("agentid" => $val1258["id"], "childtime" => $val258), array("uniacid" => $_W["uniacid"], "id" => $val254["id"]));
                            $this->sendMessage($val1258["openid"], array("nickname" => $val254["nickname"], "childtime" => $val258), TM_COMMISSION_AGENT_NEW);
                            $this->upgradeLevelByAgent($val1258["id"]);
                        }
                    }
                }
            }
            $val54 = $val254["agentid"];
            if ($val254["isagent"] == 1 && $val254["status"] == 1) {
                if (!empty($val1["selfbuy"])) {
                    $val54 = $val254["id"];
                }
            }
            if (!empty($val54)) {
                pdo_update("ewei_shop_order", array("agentid" => $val54), array("id" => $val47));
            }
            $this->calculate($val47);
        }
        public function checkOrderPay($val47 = '0')
        {
            global $_W, $_GPC;
            if (empty($val47)) {
                return;
            }
            $val1 = $this->getSet();
            if (empty($val1["level"])) {
                return;
            }
            $val852 = pdo_fetch("select id,openid,ordersn,goodsprice,agentid,paytime from " . tablename("ewei_shop_order") . " where id=:id and status>=1 and uniacid=:uniacid limit 1", array(":id" => $val47, ":uniacid" => $_W["uniacid"]));
            if (empty($val852)) {
                return;
            }
            $val244 = $val852["openid"];
            $val254 = m("member")->getMember($val244);
            if (empty($val254)) {
                return;
            }
            $val1293 = intval($val1["become_child"]);
            $val1258 = false;
            if (empty($val1293)) {
                $val1258 = m("member")->getMember($val254["agentid"]);
            } else {
                $val1258 = m("member")->getMember($val254["inviter"]);
            }
            $val1264 = !empty($val1258) && $val1258["isagent"] == 1 && $val1258["status"] == 1;
            $val258 = time();
            $val1293 = intval($val1["become_child"]);
            if ($val1264) {
                if ($val1293 == 2) {
                    if (empty($val254["agentid"]) && $val254["id"] != $val1258["id"]) {
                        if (empty($val254["fixagentid"])) {
                            $val254["agentid"] = $val1258["id"];
                            pdo_update("ewei_shop_member", array("agentid" => $val1258["id"], "childtime" => $val258), array("uniacid" => $_W["uniacid"], "id" => $val254["id"]));
                            $this->sendMessage($val1258["openid"], array("nickname" => $val254["nickname"], "childtime" => $val258), TM_COMMISSION_AGENT_NEW);
                            $this->upgradeLevelByAgent($val1258["id"]);
                            if (empty($val852["agentid"])) {
                                $val852["agentid"] = $val1258["id"];
                                pdo_update("ewei_shop_order", array("agentid" => $val1258["id"]), array("id" => $val47));
                                $this->calculate($val47);
                            }
                        }
                    }
                }
            }
            $val1438 = $val254["isagent"] == 1 && $val254["status"] == 1;
            if (!$val1438) {
                if (intval($val1["become"]) == 4 && !empty($val1["become_goodsid"])) {
                    $val1444 = pdo_fetchall("select goodsid from " . tablename("ewei_shop_order_goods") . " where orderid=:orderid and uniacid=:uniacid  ", array(":uniacid" => $_W["uniacid"], ":orderid" => $val852["id"]), "goodsid");
                    if (in_array($val1["become_goodsid"], array_keys($val1444))) {
                        if (empty($val254["agentblack"])) {
                            pdo_update("ewei_shop_member", array("status" => 1, "isagent" => 1, "agenttime" => $val258), array("uniacid" => $_W["uniacid"], "id" => $val254["id"]));
                            $this->sendMessage($val244, array("nickname" => $val254["nickname"], "agenttime" => $val258), TM_COMMISSION_BECOME);
                            if (!empty($val1258)) {
                                $this->upgradeLevelByAgent($val1258["id"]);
                            }
                        }
                    }
                } else {
                    if ($val1["become"] == 2 || $val1["become"] == 3) {
                        if (empty($val1["become_order"])) {
                            $val258 = time();
                            if ($val1["become"] == 2 || $val1["become"] == 3) {
                                $val1464 = true;
                                if (!empty($val254["agentid"])) {
                                    $val1258 = m("member")->getMember($val254["agentid"]);
                                    if (empty($val1258) || $val1258["isagent"] != 1 || $val1258["status"] != 1) {
                                        $val1464 = false;
                                    }
                                }
                                if ($val1464) {
                                    $val1473 = false;
                                    if ($val1["become"] == "2") {
                                        $val264 = pdo_fetchcolumn("select count(*) from " . tablename("ewei_shop_order") . " where openid=:openid and status>=1 and uniacid=:uniacid limit 1", array(":uniacid" => $_W["uniacid"], ":openid" => $val244));
                                        $val1473 = $val264 >= intval($val1["become_ordercount"]);
                                    } else {
                                        if ($val1["become"] == "3") {
                                            $val1482 = pdo_fetchcolumn("select sum(og.realprice) from " . tablename("ewei_shop_order_goods") . " og left join " . tablename("ewei_shop_order") . " o on og.orderid=o.id  where o.openid=:openid and o.status>=1 and o.uniacid=:uniacid limit 1", array(":uniacid" => $_W["uniacid"], ":openid" => $val244));
                                            $val1473 = $val1482 >= floatval($val1["become_moneycount"]);
                                        }
                                    }
                                    if ($val1473) {
                                        if (empty($val254["agentblack"])) {
                                            $val1312 = intval($val1["become_check"]);
                                            pdo_update("ewei_shop_member", array("status" => $val1312, "isagent" => 1, "agenttime" => $val258), array("uniacid" => $_W["uniacid"], "id" => $val254["id"]));
                                            if ($val1312 == 1) {
                                                $this->sendMessage($val244, array("nickname" => $val254["nickname"], "agenttime" => $val258), TM_COMMISSION_BECOME);
                                                if ($val1464) {
                                                    $this->upgradeLevelByAgent($val1258["id"]);
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
            if (!empty($val254["agentid"])) {
                $val1258 = m("member")->getMember($val254["agentid"]);
                if (!empty($val1258) && $val1258["isagent"] == 1 && $val1258["status"] == 1) {
                    if ($val852["agentid"] == $val1258["id"]) {
                        $val1444 = pdo_fetchall("select g.id,g.title,og.total,og.price,og.realprice, og.optionname as optiontitle,g.noticeopenid,g.noticetype,og.commission1 from " . tablename("ewei_shop_order_goods") . " og " . " left join " . tablename("ewei_shop_goods") . " g on g.id=og.goodsid " . " where og.uniacid=:uniacid and og.orderid=:orderid ", array(":uniacid" => $_W["uniacid"], ":orderid" => $val852["id"]));
                        $val56 = '';
                        $val88 = $val1258["agentlevel"];
                        $val268 = 0;
                        $val1517 = 0;
                        foreach ($val1444 as $val1519) {
                            $val56 .= "" . $val1519["title"] . "( ";
                            if (!empty($val1519["optiontitle"])) {
                                $val56 .= " 规格: " . $val1519["optiontitle"];
                            }
                            $val56 .= " 单价: " . $val1519["realprice"] / $val1519["total"] . " 数量: " . $val1519["total"] . " 总价: " . $val1519["realprice"] . "); ";
                            $val330 = iunserializer($val1519["commission1"]);
                            $val268 += isset($val330["level" . $val88]) ? $val330["level" . $val88] : $val330["default"];
                            $val1517 += $val1519["realprice"];
                        }
                        $this->sendMessage($val1258["openid"], array("nickname" => $val254["nickname"], "ordersn" => $val852["ordersn"], "price" => $val1517, "goods" => $val56, "commission" => $val268, "paytime" => $val852["paytime"]), TM_COMMISSION_ORDER_PAY);
                    }
                }
            }
        }
        public function checkOrderFinish($val47 = '')
        {
            global $_W, $_GPC;
            if (empty($val47)) {
                return;
            }
            $val852 = pdo_fetch("select id,openid, ordersn,goodsprice,agentid,finishtime from " . tablename("ewei_shop_order") . " where id=:id and status>=3 and uniacid=:uniacid limit 1", array(":id" => $val47, ":uniacid" => $_W["uniacid"]));
            if (empty($val852)) {
                return;
            }
            $val1 = $this->getSet();
            if (empty($val1["level"])) {
                return;
            }
            $val244 = $val852["openid"];
            $val254 = m("member")->getMember($val244);
            if (empty($val254)) {
                return;
            }
            $val258 = time();
            $val1438 = $val254["isagent"] == 1 && $val254["status"] == 1;
            if (!$val1438 && $val1["become_order"] == 1) {
                if ($val1["become"] == 2 || $val1["become"] == 3) {
                    $val1464 = true;
                    if (!empty($val254["agentid"])) {
                        $val1258 = m("member")->getMember($val254["agentid"]);
                        if (empty($val1258) || $val1258["isagent"] != 1 || $val1258["status"] != 1) {
                            $val1464 = false;
                        }
                    }
                    if ($val1464) {
                        $val1473 = false;
                        if ($val1["become"] == "2") {
                            $val264 = pdo_fetchcolumn("select count(*) from " . tablename("ewei_shop_order") . " where openid=:openid and status>=3 and uniacid=:uniacid limit 1", array(":uniacid" => $_W["uniacid"], ":openid" => $val244));
                            $val1473 = $val264 >= intval($val1["become_ordercount"]);
                        } else {
                            if ($val1["become"] == "3") {
                                $val1482 = pdo_fetchcolumn("select sum(goodsprice) from " . tablename("ewei_shop_order") . " where openid=:openid and status>=3 and uniacid=:uniacid limit 1", array(":uniacid" => $_W["uniacid"], ":openid" => $val244));
                                $val1473 = $val1482 >= floatval($val1["become_moneycount"]);
                            }
                        }
                        if ($val1473) {
                            if (empty($val254["agentblack"])) {
                                $val1312 = intval($val1["become_check"]);
                                pdo_update("ewei_shop_member", array("status" => $val1312, "isagent" => 1, "agenttime" => $val258), array("uniacid" => $_W["uniacid"], "id" => $val254["id"]));
                                if ($val1312 == 1) {
                                    $this->sendMessage($val254["openid"], array("nickname" => $val254["nickname"], "agenttime" => $val258), TM_COMMISSION_BECOME);
                                    if ($val1464) {
                                        $this->upgradeLevelByAgent($val1258["id"]);
                                    }
                                }
                            }
                        }
                    }
                }
            }
            if (!empty($val254["agentid"])) {
                $val1258 = m("member")->getMember($val254["agentid"]);
                if (!empty($val1258) && $val1258["isagent"] == 1 && $val1258["status"] == 1) {
                    if ($val852["agentid"] == $val1258["id"]) {
                        $val1444 = pdo_fetchall("select g.id,g.title,og.total,og.realprice,og.price,og.optionname as optiontitle,g.noticeopenid,g.noticetype,og.commission1 from " . tablename("ewei_shop_order_goods") . " og " . " left join " . tablename("ewei_shop_goods") . " g on g.id=og.goodsid " . " where og.uniacid=:uniacid and og.orderid=:orderid ", array(":uniacid" => $_W["uniacid"], ":orderid" => $val852["id"]));
                        $val56 = '';
                        $val88 = $val1258["agentlevel"];
                        $val268 = 0;
                        $val1517 = 0;
                        foreach ($val1444 as $val1519) {
                            $val56 .= "" . $val1519["title"] . "( ";
                            if (!empty($val1519["optiontitle"])) {
                                $val56 .= " 规格: " . $val1519["optiontitle"];
                            }
                            $val56 .= " 单价: " . $val1519["realprice"] / $val1519["total"] . " 数量: " . $val1519["total"] . " 总价: " . $val1519["realprice"] . "); ";
                            $val330 = iunserializer($val1519["commission1"]);
                            $val268 += isset($val330["level" . $val88]) ? $val330["level" . $val88] : $val330["default"];
                            $val1517 += $val1519["realprice"];
                        }
                        $this->sendMessage($val1258["openid"], array("nickname" => $val254["nickname"], "ordersn" => $val852["ordersn"], "price" => $val1517, "goods" => $val56, "commission" => $val268, "finishtime" => $val852["finishtime"]), TM_COMMISSION_ORDER_FINISH);
                    }
                }
            }
            $this->upgradeLevelByOrder($val244);
        }
        function getShop($val1655)
        {
            global $_W;
            $val254 = m("member")->getMember($val1655);
            $val1659 = pdo_fetch("select * from " . tablename("ewei_shop_commission_shop") . " where uniacid=:uniacid and mid=:mid limit 1", array(":uniacid" => $_W["uniacid"], ":mid" => $val254["id"]));
            $val1662 = m("common")->getSysset(array("shop", "share"));
            $val1 = $val1662["shop"];
            $val1665 = $val1662["share"];
            $val1667 = $val1665["desc"];
            if (empty($val1667)) {
                $val1667 = $val1["description"];
            }
            if (empty($val1667)) {
                $val1667 = $val1["name"];
            }
            $val1675 = $this->getSet();
            if (empty($val1659)) {
                $val1659 = array("name" => $val254["nickname"] . "的" . $val1675["texts"]["shop"], "logo" => $val254["avatar"], "desc" => $val1667, "img" => tomedia($val1["img"]));
            } else {
                if (empty($val1659["name"])) {
                    $val1659["name"] = $val254["nickname"] . "的" . $val1675["texts"]["shop"];
                }
                if (empty($val1659["logo"])) {
                    $val1659["logo"] = tomedia($val254["avatar"]);
                }
                if (empty($val1659["img"])) {
                    $val1659["img"] = tomedia($val1["img"]);
                }
                if (empty($val1659["desc"])) {
                    $val1659["desc"] = $val1667;
                }
            }
            return $val1659;
        }
        function getLevels($val1698 = true)
        {
            global $_W;
            if ($val1698) {
                return pdo_fetchall("select * from " . tablename("ewei_shop_commission_level") . " where uniacid=:uniacid order by commission1 asc", array(":uniacid" => $_W["uniacid"]));
            } else {
                return pdo_fetchall("select * from " . tablename("ewei_shop_commission_level") . " where uniacid=:uniacid and (ordermoney>0 or commissionmoney>0) order by commission1 asc", array(":uniacid" => $_W["uniacid"]));
            }
        }
        function getLevel($val244)
        {
            global $_W;
            if (empty($val244)) {
                return false;
            }
            $val254 = m("member")->getMember($val244);
            if (empty($val254["agentlevel"])) {
                return false;
            }
            $val88 = pdo_fetch("select * from " . tablename("ewei_shop_commission_level") . " where uniacid=:uniacid and id=:id limit 1", array(":uniacid" => $_W["uniacid"], ":id" => $val254["agentlevel"]));
            return $val88;
        }
        function upgradeLevelByOrder($val244)
        {
            global $_W;
            if (empty($val244)) {
                return false;
            }
            $val1 = $this->getSet();
            if (empty($val1["level"])) {
                return false;
            }
            $val1655 = m("member")->getMember($val244);
            if (empty($val1655)) {
                return;
            }
            $val1722 = intval($val1["leveltype"]);
            if ($val1722 == 4 || $val1722 == 5) {
                if (!empty($val1655["agentnotupgrade"])) {
                    return;
                }
                $val1727 = $this->getLevel($val1655["openid"]);
                if (empty($val1727["id"])) {
                    $val1727 = array("levelname" => empty($val1["levelname"]) ? "普通等级" : $val1["levelname"], "commission1" => $val1["commission1"], "commission2" => $val1["commission2"], "commission3" => $val1["commission3"]);
                }
                $val1736 = pdo_fetch("select sum(og.realprice) as ordermoney,count(distinct og.orderid) as ordercount from " . tablename("ewei_shop_order") . " o " . " left join  " . tablename("ewei_shop_order_goods") . " og on og.orderid=o.id " . " where o.openid=:openid and o.status>=3 and o.uniacid=:uniacid limit 1", array(":uniacid" => $_W["uniacid"], ":openid" => $val244));
                $val265 = $val1736["ordermoney"];
                $val264 = $val1736["ordercount"];
                if ($val1722 == 4) {
                    $val1744 = pdo_fetch("select * from " . tablename("ewei_shop_commission_level") . " where uniacid=:uniacid  and {$val265} >= ordermoney and ordermoney>0  order by ordermoney desc limit 1", array(":uniacid" => $_W["uniacid"]));
                    if (empty($val1744)) {
                        return;
                    }
                    if (!empty($val1727["id"])) {
                        if ($val1727["id"] == $val1744["id"]) {
                            return;
                        }
                        if ($val1727["ordermoney"] > $val1744["ordermoney"]) {
                            return;
                        }
                    }
                } else {
                    if ($val1722 == 5) {
                        $val1744 = pdo_fetch("select * from " . tablename("ewei_shop_commission_level") . " where uniacid=:uniacid  and {$val264} >= ordercount and ordercount>0  order by ordercount desc limit 1", array(":uniacid" => $_W["uniacid"]));
                        if (empty($val1744)) {
                            return;
                        }
                        if (!empty($val1727["id"])) {
                            if ($val1727["id"] == $val1744["id"]) {
                                return;
                            }
                            if ($val1727["ordercount"] > $val1744["ordercount"]) {
                                return;
                            }
                        }
                    }
                }
                pdo_update("ewei_shop_member", array("agentlevel" => $val1744["id"]), array("id" => $val1655["id"]));
                $this->sendMessage($val1655["openid"], array("nickname" => $val1655["nickname"], "oldlevel" => $val1727, "newlevel" => $val1744), TM_COMMISSION_UPGRADE);
            } else {
                if ($val1722 >= 0 && $val1722 <= 3) {
                    $val851 = array();
                    if (!empty($val1["selfbuy"])) {
                        $val851[] = $val1655;
                    }
                    if (!empty($val1655["agentid"])) {
                        $val153 = m("member")->getMember($val1655["agentid"]);
                        if (!empty($val153)) {
                            $val851[] = $val153;
                            if (!empty($val153["agentid"]) && $val153["isagent"] == 1 && $val153["status"] == 1) {
                                $val165 = m("member")->getMember($val153["agentid"]);
                                if (!empty($val165) && $val165["isagent"] == 1 && $val165["status"] == 1) {
                                    $val851[] = $val165;
                                    if (empty($val1["selfbuy"])) {
                                        if (!empty($val165["agentid"]) && $val165["isagent"] == 1 && $val165["status"] == 1) {
                                            $val175 = m("member")->getMember($val165["agentid"]);
                                            if (!empty($val175) && $val175["isagent"] == 1 && $val175["status"] == 1) {
                                                $val851[] = $val175;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if (empty($val851)) {
                        return;
                    }
                    foreach ($val851 as $val1804) {
                        $val1805 = $this->getInfo($val1804["id"], array("ordercount3", "ordermoney3", "order13money", "order13"));
                        if (!empty($val1805["agentnotupgrade"])) {
                            continue;
                        }
                        $val1727 = $this->getLevel($val1804["openid"]);
                        if (empty($val1727["id"])) {
                            $val1727 = array("levelname" => empty($val1["levelname"]) ? "普通等级" : $val1["levelname"], "commission1" => $val1["commission1"], "commission2" => $val1["commission2"], "commission3" => $val1["commission3"]);
                        }
                        if ($val1722 == 0) {
                            $val265 = $val1805["ordermoney3"];
                            $val1744 = pdo_fetch("select * from " . tablename("ewei_shop_commission_level") . " where uniacid=:uniacid and {$val265} >= ordermoney and ordermoney>0  order by ordermoney desc limit 1", array(":uniacid" => $_W["uniacid"]));
                            if (empty($val1744)) {
                                continue;
                            }
                            if (!empty($val1727["id"])) {
                                if ($val1727["id"] == $val1744["id"]) {
                                    continue;
                                }
                                if ($val1727["ordermoney"] > $val1744["ordermoney"]) {
                                    continue;
                                }
                            }
                        } else {
                            if ($val1722 == 1) {
                                $val265 = $val1805["order13money"];
                                $val1744 = pdo_fetch("select * from " . tablename("ewei_shop_commission_level") . " where uniacid=:uniacid and {$val265} >= ordermoney and ordermoney>0  order by ordermoney desc limit 1", array(":uniacid" => $_W["uniacid"]));
                                if (empty($val1744)) {
                                    continue;
                                }
                                if (!empty($val1727["id"])) {
                                    if ($val1727["id"] == $val1744["id"]) {
                                        continue;
                                    }
                                    if ($val1727["ordermoney"] > $val1744["ordermoney"]) {
                                        continue;
                                    }
                                }
                            } else {
                                if ($val1722 == 2) {
                                    $val264 = $val1805["ordercount3"];
                                    $val1744 = pdo_fetch("select * from " . tablename("ewei_shop_commission_level") . " where uniacid=:uniacid  and {$val264} >= ordercount and ordercount>0  order by ordercount desc limit 1", array(":uniacid" => $_W["uniacid"]));
                                    if (empty($val1744)) {
                                        continue;
                                    }
                                    if (!empty($val1727["id"])) {
                                        if ($val1727["id"] == $val1744["id"]) {
                                            continue;
                                        }
                                        if ($val1727["ordercount"] > $val1744["ordercount"]) {
                                            continue;
                                        }
                                    }
                                } else {
                                    if ($val1722 == 3) {
                                        $val264 = $val1805["order13"];
                                        $val1744 = pdo_fetch("select * from " . tablename("ewei_shop_commission_level") . " where uniacid=:uniacid  and {$val264} >= ordercount and ordercount>0  order by ordercount desc limit 1", array(":uniacid" => $_W["uniacid"]));
                                        if (empty($val1744)) {
                                            continue;
                                        }
                                        if (!empty($val1727["id"])) {
                                            if ($val1727["id"] == $val1744["id"]) {
                                                continue;
                                            }
                                            if ($val1727["ordercount"] > $val1744["ordercount"]) {
                                                continue;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        pdo_update("ewei_shop_member", array("agentlevel" => $val1744["id"]), array("id" => $val1804["id"]));
                        $this->sendMessage($val1804["openid"], array("nickname" => $val1804["nickname"], "oldlevel" => $val1727, "newlevel" => $val1744), TM_COMMISSION_UPGRADE);
                    }
                }
            }
        }
        function upgradeLevelByAgent($val244)
        {
            global $_W;
            if (empty($val244)) {
                return false;
            }
            $val1 = $this->getSet();
            if (empty($val1["level"])) {
                return false;
            }
            $val1655 = m("member")->getMember($val244);
            if (empty($val1655)) {
                return;
            }
            $val1722 = intval($val1["leveltype"]);
            if ($val1722 < 6 || $val1722 > 9) {
                return;
            }
            $val1805 = $this->getInfo($val1655["id"], array());
            if ($val1722 == 6 || $val1722 == 8) {
                $val851 = array($val1655);
                if (!empty($val1655["agentid"])) {
                    $val153 = m("member")->getMember($val1655["agentid"]);
                    if (!empty($val153)) {
                        $val851[] = $val153;
                        if (!empty($val153["agentid"]) && $val153["isagent"] == 1 && $val153["status"] == 1) {
                            $val165 = m("member")->getMember($val153["agentid"]);
                            if (!empty($val165) && $val165["isagent"] == 1 && $val165["status"] == 1) {
                                $val851[] = $val165;
                            }
                        }
                    }
                }
                if (empty($val851)) {
                    return;
                }
                foreach ($val851 as $val1804) {
                    $val1805 = $this->getInfo($val1804["id"], array());
                    if (!empty($val1805["agentnotupgrade"])) {
                        continue;
                    }
                    $val1727 = $this->getLevel($val1804["openid"]);
                    if (empty($val1727["id"])) {
                        $val1727 = array("levelname" => empty($val1["levelname"]) ? "普通等级" : $val1["levelname"], "commission1" => $val1["commission1"], "commission2" => $val1["commission2"], "commission3" => $val1["commission3"]);
                    }
                    if ($val1722 == 6) {
                        $val1922 = pdo_fetchall("select id from " . tablename("ewei_shop_member") . " where agentid=:agentid and uniacid=:uniacid ", array(":agentid" => $val1655["id"], ":uniacid" => $_W["uniacid"]), "id");
                        $val1925 = count($val1922);
                        if (!empty($val1922)) {
                            $val1928 = pdo_fetchall("select id from " . tablename("ewei_shop_member") . " where agentid in( " . implode(",", array_keys($val1922)) . ") and uniacid=:uniacid", array(":uniacid" => $_W["uniacid"]), "id");
                            $val1925 = count($val1928);
                            if (!empty($val1928)) {
                                $val1934 = pdo_fetchall("select id from " . tablename("ewei_shop_member") . " where agentid in( " . implode(",", array_keys($val1928)) . ") and uniacid=:uniacid", array(":uniacid" => $_W["uniacid"]), "id");
                                $val1925 = count($val1934);
                            }
                        }
                        $val1744 = pdo_fetch("select * from " . tablename("ewei_shop_commission_level") . " where uniacid=:uniacid  and {$val1943} >= downcount and downcount>0  order by downcount desc limit 1", array(":uniacid" => $_W["uniacid"]));
                    } else {
                        if ($val1722 == 8) {
                            $val1943 = $val1805["level1"] + $val1805["level2"] + $val1805["level3"];
                            $val1744 = pdo_fetch("select * from " . tablename("ewei_shop_commission_level") . " where uniacid=:uniacid  and {$val1943} >= downcount and downcount>0  order by downcount desc limit 1", array(":uniacid" => $_W["uniacid"]));
                        }
                    }
                    if (empty($val1744)) {
                        continue;
                    }
                    if ($val1744["id"] == $val1727["id"]) {
                        continue;
                    }
                    if (!empty($val1727["id"])) {
                        if ($val1727["downcount"] > $val1744["downcount"]) {
                            continue;
                        }
                    }
                    pdo_update("ewei_shop_member", array("agentlevel" => $val1744["id"]), array("id" => $val1804["id"]));
                    $this->sendMessage($val1804["openid"], array("nickname" => $val1804["nickname"], "oldlevel" => $val1727, "newlevel" => $val1744), TM_COMMISSION_UPGRADE);
                }
            } else {
                if (!empty($val1655["agentnotupgrade"])) {
                    return;
                }
                $val1727 = $this->getLevel($val1655["openid"]);
                if (empty($val1727["id"])) {
                    $val1727 = array("levelname" => empty($val1["levelname"]) ? "普通等级" : $val1["levelname"], "commission1" => $val1["commission1"], "commission2" => $val1["commission2"], "commission3" => $val1["commission3"]);
                }
                if ($val1722 == 7) {
                    $val1943 = pdo_fetchcolumn("select count(*) from " . tablename("ewei_shop_member") . " where agentid=:agentid and uniacid=:uniacid ", array(":agentid" => $val1655["id"], ":uniacid" => $_W["uniacid"]));
                    $val1744 = pdo_fetch("select * from " . tablename("ewei_shop_commission_level") . " where uniacid=:uniacid  and {$val1943} >= downcount and downcount>0  order by downcount desc limit 1", array(":uniacid" => $_W["uniacid"]));
                } else {
                    if ($val1722 == 9) {
                        $val1943 = $val1805["level1"];
                        $val1744 = pdo_fetch("select * from " . tablename("ewei_shop_commission_level") . " where uniacid=:uniacid  and {$val1943} >= downcount and downcount>0  order by downcount desc limit 1", array(":uniacid" => $_W["uniacid"]));
                    }
                }
                if (empty($val1744)) {
                    return;
                }
                if ($val1744["id"] == $val1727["id"]) {
                    return;
                }
                if (!empty($val1727["id"])) {
                    if ($val1727["downcount"] > $val1744["downcount"]) {
                        return;
                    }
                }
                pdo_update("ewei_shop_member", array("agentlevel" => $val1744["id"]), array("id" => $val1655["id"]));
                $this->sendMessage($val1655["openid"], array("nickname" => $val1655["nickname"], "oldlevel" => $val1727, "newlevel" => $val1744), TM_COMMISSION_UPGRADE);
            }
        }
        function upgradeLevelByCommissionOK($val244)
        {
            global $_W;
            if (empty($val244)) {
                return false;
            }
            $val1 = $this->getSet();
            if (empty($val1["level"])) {
                return false;
            }
            $val1655 = m("member")->getMember($val244);
            if (empty($val1655)) {
                return;
            }
            $val1722 = intval($val1["leveltype"]);
            if ($val1722 != 10) {
                return;
            }
            if (!empty($val1655["agentnotupgrade"])) {
                return;
            }
            $val1727 = $this->getLevel($val1655["openid"]);
            if (empty($val1727["id"])) {
                $val1727 = array("levelname" => empty($val1["levelname"]) ? "普通等级" : $val1["levelname"], "commission1" => $val1["commission1"], "commission2" => $val1["commission2"], "commission3" => $val1["commission3"]);
            }
            $val1805 = $this->getInfo($val1655["id"], array("pay"));
            $val2021 = $val1805["commission_pay"];
            $val1744 = pdo_fetch("select * from " . tablename("ewei_shop_commission_level") . " where uniacid=:uniacid  and {$val2021} >= commissionmoney and commissionmoney>0  order by commissionmoney desc limit 1", array(":uniacid" => $_W["uniacid"]));
            if (empty($val1744)) {
                return;
            }
            if ($val1727["id"] == $val1744["id"]) {
                return;
            }
            if (!empty($val1727["id"])) {
                if ($val1727["commissionmoney"] > $val1744["commissionmoney"]) {
                    return;
                }
            }
            pdo_update("ewei_shop_member", array("agentlevel" => $val1744["id"]), array("id" => $val1655["id"]));
            $this->sendMessage($val1655["openid"], array("nickname" => $val1655["nickname"], "oldlevel" => $val1727, "newlevel" => $val1744), TM_COMMISSION_UPGRADE);
        }
        function sendMessage($val244 = '', $val2039 = array(), $val2040 = '')
        {
            global $_W, $_GPC;
            $val1 = $this->getSet();
            $val2045 = $val1["tm"];
            $val2047 = $val2045["templateid"];
            $val254 = m("member")->getMember($val244);
            $val2051 = unserialize($val254["noticeset"]);
            if (!is_array($val2051)) {
                $val2051 = array();
            }
            if ($val2040 == TM_COMMISSION_AGENT_NEW && !empty($val2045["commission_agent_new"]) && empty($val2051["commission_agent_new"])) {
                $val2058 = $val2045["commission_agent_new"];
                $val2058 = str_replace("[昵称]", $val2039["nickname"], $val2058);
                $val2058 = str_replace("[时间]", date("Y-m-d H:i:s", $val2039["childtime"]), $val2058);
                $val2066 = array("keyword1" => array("value" => !empty($val2045["commission_agent_newtitle"]) ? $val2045["commission_agent_newtitle"] : "新增下线通知", "color" => "#73a68d"), "keyword2" => array("value" => $val2058, "color" => "#73a68d"));
                if (!empty($val2047)) {
                    m("message")->sendTplNotice($val244, $val2047, $val2066);
                } else {
                    m("message")->sendCustomNotice($val244, $val2066);
                }
            } else {
                if ($val2040 == TM_COMMISSION_ORDER_PAY && !empty($val2045["commission_order_pay"]) && empty($val2051["commission_order_pay"])) {
                    $val2058 = $val2045["commission_order_pay"];
                    $val2058 = str_replace("[昵称]", $val2039["nickname"], $val2058);
                    $val2058 = str_replace("[时间]", date("Y-m-d H:i:s", $val2039["paytime"]), $val2058);
                    $val2058 = str_replace("[订单编号]", $val2039["ordersn"], $val2058);
                    $val2058 = str_replace("[订单金额]", $val2039["price"], $val2058);
                    $val2058 = str_replace("[佣金金额]", $val2039["commission"], $val2058);
                    $val2058 = str_replace("[商品详情]", $val2039["goods"], $val2058);
                    $val2066 = array("keyword1" => array("value" => !empty($val2045["commission_order_paytitle"]) ? $val2045["commission_order_paytitle"] : "下线付款通知"), "keyword2" => array("value" => $val2058));
                    if (!empty($val2047)) {
                        m("message")->sendTplNotice($val244, $val2047, $val2066);
                    } else {
                        m("message")->sendCustomNotice($val244, $val2066);
                    }
                } else {
                    if ($val2040 == TM_COMMISSION_ORDER_FINISH && !empty($val2045["commission_order_finish"]) && empty($val2051["commission_order_finish"])) {
                        $val2058 = $val2045["commission_order_finish"];
                        $val2058 = str_replace("[昵称]", $val2039["nickname"], $val2058);
                        $val2058 = str_replace("[时间]", date("Y-m-d H:i:s", $val2039["finishtime"]), $val2058);
                        $val2058 = str_replace("[订单编号]", $val2039["ordersn"], $val2058);
                        $val2058 = str_replace("[订单金额]", $val2039["price"], $val2058);
                        $val2058 = str_replace("[佣金金额]", $val2039["commission"], $val2058);
                        $val2058 = str_replace("[商品详情]", $val2039["goods"], $val2058);
                        $val2066 = array("keyword1" => array("value" => !empty($val2045["commission_order_finishtitle"]) ? $val2045["commission_order_finishtitle"] : "下线确认收货通知", "color" => "#73a68d"), "keyword2" => array("value" => $val2058, "color" => "#73a68d"));
                        if (!empty($val2047)) {
                            m("message")->sendTplNotice($val244, $val2047, $val2066);
                        } else {
                            m("message")->sendCustomNotice($val244, $val2066);
                        }
                    } else {
                        if ($val2040 == TM_COMMISSION_APPLY && !empty($val2045["commission_apply"]) && empty($val2051["commission_apply"])) {
                            $val2058 = $val2045["commission_apply"];
                            $val2058 = str_replace("[昵称]", $val254["nickname"], $val2058);
                            $val2058 = str_replace("[时间]", date("Y-m-d H:i:s", time()), $val2058);
                            $val2058 = str_replace("[金额]", $val2039["commission"], $val2058);
                            $val2058 = str_replace("[提现方式]", $val2039["type"], $val2058);
                            $val2066 = array("keyword1" => array("value" => !empty($val2045["commission_applytitle"]) ? $val2045["commission_applytitle"] : "提现申请提交成功", "color" => "#73a68d"), "keyword2" => array("value" => $val2058, "color" => "#73a68d"));
                            if (!empty($val2047)) {
                                m("message")->sendTplNotice($val244, $val2047, $val2066);
                            } else {
                                m("message")->sendCustomNotice($val244, $val2066);
                            }
                        } else {
                            if ($val2040 == TM_COMMISSION_CHECK && !empty($val2045["commission_check"]) && empty($val2051["commission_check"])) {
                                $val2058 = $val2045["commission_check"];
                                $val2058 = str_replace("[昵称]", $val254["nickname"], $val2058);
                                $val2058 = str_replace("[时间]", date("Y-m-d H:i:s", time()), $val2058);
                                $val2058 = str_replace("[金额]", $val2039["commission"], $val2058);
                                $val2058 = str_replace("[提现方式]", $val2039["type"], $val2058);
                                $val2066 = array("keyword1" => array("value" => !empty($val2045["commission_checktitle"]) ? $val2045["commission_checktitle"] : "提现申请审核处理完成", "color" => "#73a68d"), "keyword2" => array("value" => $val2058, "color" => "#73a68d"));
                                if (!empty($val2047)) {
                                    m("message")->sendTplNotice($val244, $val2047, $val2066);
                                } else {
                                    m("message")->sendCustomNotice($val244, $val2066);
                                }
                            } else {
                                if ($val2040 == TM_COMMISSION_PAY && !empty($val2045["commission_pay"]) && empty($val2051["commission_pay"])) {
                                    $val2058 = $val2045["commission_pay"];
                                    $val2058 = str_replace("[昵称]", $val254["nickname"], $val2058);
                                    $val2058 = str_replace("[时间]", date("Y-m-d H:i:s", time()), $val2058);
                                    $val2058 = str_replace("[金额]", $val2039["commission"], $val2058);
                                    $val2058 = str_replace("[提现方式]", $val2039["type"], $val2058);
                                    $val2066 = array("keyword1" => array("value" => !empty($val2045["commission_paytitle"]) ? $val2045["commission_paytitle"] : "佣金打款通知", "color" => "#73a68d"), "keyword2" => array("value" => $val2058, "color" => "#73a68d"));
                                    if (!empty($val2047)) {
                                        m("message")->sendTplNotice($val244, $val2047, $val2066);
                                    } else {
                                        m("message")->sendCustomNotice($val244, $val2066);
                                    }
                                } else {
                                    if ($val2040 == TM_COMMISSION_UPGRADE && !empty($val2045["commission_upgrade"]) && empty($val2051["commission_upgrade"])) {
                                        $val2058 = $val2045["commission_upgrade"];
                                        $val2058 = str_replace("[昵称]", $val254["nickname"], $val2058);
                                        $val2058 = str_replace("[时间]", date("Y-m-d H:i:s", time()), $val2058);
                                        $val2058 = str_replace("[旧等级]", $val2039["oldlevel"]["levelname"], $val2058);
                                        $val2058 = str_replace("[旧一级分销比例]", $val2039["oldlevel"]["commission1"] . "%", $val2058);
                                        $val2058 = str_replace("[旧二级分销比例]", $val2039["oldlevel"]["commission2"] . "%", $val2058);
                                        $val2058 = str_replace("[旧三级分销比例]", $val2039["oldlevel"]["commission3"] . "%", $val2058);
                                        $val2058 = str_replace("[新等级]", $val2039["newlevel"]["levelname"], $val2058);
                                        $val2058 = str_replace("[新一级分销比例]", $val2039["newlevel"]["commission1"] . "%", $val2058);
                                        $val2058 = str_replace("[新二级分销比例]", $val2039["newlevel"]["commission2"] . "%", $val2058);
                                        $val2058 = str_replace("[新三级分销比例]", $val2039["newlevel"]["commission3"] . "%", $val2058);
                                        $val2066 = array("keyword1" => array("value" => !empty($val2045["commission_upgradetitle"]) ? $val2045["commission_upgradetitle"] : "分销等级升级通知", "color" => "#73a68d"), "keyword2" => array("value" => $val2058, "color" => "#73a68d"));
                                        if (!empty($val2047)) {
                                            m("message")->sendTplNotice($val244, $val2047, $val2066);
                                        } else {
                                            m("message")->sendCustomNotice($val244, $val2066);
                                        }
                                    } else {
                                        if ($val2040 == TM_COMMISSION_BECOME && !empty($val2045["commission_become"]) && empty($val2051["commission_become"])) {
                                            $val2058 = $val2045["commission_become"];
                                            $val2058 = str_replace("[昵称]", $val2039["nickname"], $val2058);
                                            $val2058 = str_replace("[时间]", date("Y-m-d H:i:s", $val2039["agenttime"]), $val2058);
                                            $val2066 = array("keyword1" => array("value" => !empty($val2045["commission_becometitle"]) ? $val2045["commission_becometitle"] : "成为分销商通知", "color" => "#73a68d"), "keyword2" => array("value" => $val2058, "color" => "#73a68d"));
                                            if (!empty($val2047)) {
                                                m("message")->sendTplNotice($val244, $val2047, $val2066);
                                            } else {
                                                m("message")->sendCustomNotice($val244, $val2066);
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
        function perms()
        {
            return array("commission" => array("text" => $this->getName(), "isplugin" => true, "child" => array("cover" => array("text" => "入口设置"), "agent" => array("text" => "分销商", "view" => "浏览", "check" => "审核-log", "edit" => "修改-log", "agentblack" => "黑名单操作-log", "delete" => "删除-log", "user" => "查看下线", "order" => "查看推广订单(还需有订单权限)", "changeagent" => "设置分销商"), "level" => array("text" => "分销商等级", "view" => "浏览", "add" => "添加-log", "edit" => "修改-log", "delete" => "删除-log"), "apply" => array("text" => "佣金审核", "view1" => "浏览待审核", "view2" => "浏览已审核", "view3" => "浏览已打款", "view_1" => "浏览无效", "export1" => "导出待审核-log", "export2" => "导出已审核-log", "export3" => "导出已打款-log", "export_1" => "导出无效-log", "check" => "审核-log", "pay" => "打款-log", "cancel" => "重新审核-log"), "notice" => array("text" => "通知设置-log"), "increase" => array("text" => "分销商趋势图"), "changecommission" => array("text" => "修改佣金-log"), "set" => array("text" => "基础设置-log"))));
        }
    }
}