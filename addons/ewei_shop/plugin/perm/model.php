<?php
if (!defined("IN_IA")) {
    exit("Access Denied");
}
if (!class_exists("PermModel")) {
    class PermModel extends PluginModel
    {
        public function allPerms()
        {
            $val0 = array("shop" => array("text" => "商城管理", "child" => array("goods" => array("text" => "商品", "view" => "浏览", "add" => "添加-log", "edit" => "修改-log", "delete" => "删除-log"), "category" => array("text" => "商品分类", "view" => "浏览", "add" => "添加-log", "edit" => "修改-log", "delete" => "删除-log"), "dispatch" => array("text" => "配送方式", "view" => "浏览", "add" => "添加-log", "edit" => "修改-log", "delete" => "删除-log"), "adv" => array("text" => "幻灯片", "view" => "浏览", "add" => "添加-log", "edit" => "修改-log", "delete" => "删除-log"), "notice" => array("text" => "公告", "view" => "浏览", "add" => "添加-log", "edit" => "修改-log", "delete" => "删除-log"), "comment" => array("text" => "评价", "view" => "浏览", "add" => "添加评论-log", "edit" => "回复-log", "delete" => "删除-log"), "refundaddress" => array("text" => "退货地址", "view" => "浏览", "add" => "添加-log", "edit" => "修改-log", "delete" => "删除-log"))), "member" => array("text" => "会员管理", "child" => array("member" => array("text" => "会员", "view" => "浏览", "edit" => "修改-log", "delete" => "删除-log", "export" => "导出-log"), "group" => array("text" => "会员组", "view" => "浏览", "add" => "添加-log", "edit" => "修改-log", "delete" => "删除-log"), "level" => array("text" => "会员等级", "view" => "浏览", "add" => "添加-log", "edit" => "修改-log", "delete" => "删除-log"))), "order" => array("text" => "订单管理", "child" => array("view" => array("text" => "浏览", "status_1" => "浏览关闭订单", "status0" => "浏览待付款订单", "status1" => "浏览已付款订单", "status2" => "浏览已发货订单", "status3" => "浏览完成的订单", "status4" => "浏览退货申请订单", "status5" => "浏览已退货订单"), "op" => array("text" => "操作", "pay" => "确认付款-log", "send" => "发货-log", "sendcancel" => "取消发货-log", "finish" => "确认收货(快递单)-log", "verify" => "确认核销(核销单)-log", "fetch" => "确认取货(自提单)-log", "close" => "关闭订单-log", "refund" => "退货处理-log", "export" => "导出订单-log", "changeprice" => "订单改价-log", "changeaddress" => "修改订单地址-log"))), "finance" => array("text" => "财务管理", "child" => array("recharge" => array("text" => "充值", "view" => "浏览", "credit1" => "充值积分-log", "credit2" => "充值余额-log", "refund" => "充值退款-log", "export" => "导出充值记录-log"), "withdraw" => array("text" => "提现", "view" => "浏览", "withdraw" => "提现-log", "export" => "导出提现记录-log"), "downloadbill" => array("text" => "下载对账单"))), "statistics" => array("text" => "数据统计", "child" => array("view" => array("text" => "浏览权限", "sale" => "销售指标", "sale_analysis" => "销售统计", "order" => "订单统计", "goods" => "商品销售统计", "goods_rank" => "商品销售排行", "goods_trans" => "商品销售转化率", "member_cost" => "会员消费排行", "member_increase" => "会员增长趋势"), "export" => array("text" => "导出", "sale" => "导出销售统计-log", "order" => "导出订单统计-log", "goods" => "导出商品销售统计-log", "goods_rank" => "导出商品销售排行-log", "goods_trans" => "商品销售转化率-log", "member_cost" => "会员消费排行-log"))), "sysset" => array("text" => "系统设置", "child" => array("view" => array("text" => "浏览", "shop" => "商城设置", "follow" => "引导及分享设置", "notice" => "模板消息设置", "trade" => "交易设置", "pay" => "支付方式设置", "template" => "模板设置", "member" => "会员设置", "category" => "分类层级设置", "contact" => "联系方式设置"), "save" => array("text" => "修改", "shop" => "修改商城设置-log", "follow" => "修改引导及分享设置-log", "notice" => "修改模板消息设置-log", "trade" => "修改交易设置-log", "pay" => "修改支付方式设置-log", "template" => "模板设置-log", "member" => "会员设置-log", "category" => "分类层级设置-log", "contact" => "联系方式设置-log"))));
            $val1 = m("plugin")->getAll();
            foreach ($val1 as $val3) {
                $val4 = p($val3["identity"]);
                if ($val4) {
                    if (method_exists($val4, "perms")) {
                        $val8 = $val4->perms();
                        $val0 = array_merge($val0, $val8);
                    }
                }
            }
            return $val0;
        }
        public function isopen($val14 = '')
        {
            if (empty($val14)) {
                return false;
            }
            $val1 = m("plugin")->getAll();
            foreach ($val1 as $val3) {
                if ($val3["identity"] == strtolower($val14)) {
                    if (empty($val3["status"])) {
                        return false;
                    }
                }
            }
            return true;
        }
        public function check_edit($val22 = '', $val23 = array())
        {
            if (empty($val22)) {
                return false;
            }
            if (!$this->check_perm($val22)) {
                return false;
            }
            if (empty($val23["id"])) {
                $val27 = $val22 . ".add";
                if (!$this->check($val27)) {
                    return false;
                }
                return true;
            } else {
                $val30 = $val22 . ".edit";
                if (!$this->check($val30)) {
                    return false;
                }
                return true;
            }
        }
        public function check_perm($val33 = '')
        {
            global $_W;
            $val35 = true;
            if (empty($val33)) {
                return false;
            }
            if (!strexists($val33, "&") && !strexists($val33, "|")) {
                $val35 = $this->check($val33);
            } else {
                if (strexists($val33, "&")) {
                    $val42 = explode("&", $val33);
                    foreach ($val42 as $val45) {
                        $val35 = $this->check($val45);
                        if (!$val35) {
                            break;
                        }
                    }
                } else {
                    if (strexists($val33, "|")) {
                        $val42 = explode("|", $val33);
                        foreach ($val42 as $val45) {
                            $val35 = $this->check($val45);
                            if ($val35) {
                                break;
                            }
                        }
                    }
                }
            }
            return $val35;
        }
        private function check($val22 = '')
        {
            global $_W, $_GPC;
            if ($_W["role"] == "manager" || $_W["role"] == "founder") {
                return true;
            }
            $val63 = $_W["uid"];
            if (empty($val22)) {
                return false;
            }
            $val66 = pdo_fetch("select u.status as userstatus,r.status as rolestatus,u.perms as userperms,r.perms as roleperms,u.roleid from " . tablename("ewei_shop_perm_user") . " u " . " left join " . tablename("ewei_shop_perm_role") . " r on u.roleid = r.id " . " where uid=:uid limit 1 ", array(":uid" => $val63));
            if (empty($val66) || empty($val66["userstatus"])) {
                return false;
            }
            if (!empty($val66["role"]) && empty($val66["rolestatus"])) {
                return true;
            }
            $val72 = explode(",", $val66["roleperms"]);
            $val74 = explode(",", $val66["userperms"]);
            $val0 = array_merge($val72, $val74);
            if (empty($val0)) {
                return false;
            }
            $val80 = explode(".", $val22);
            if (!in_array($val80[0], $val0)) {
                return false;
            }
            if (isset($val80[1]) && !in_array($val80[0] . "." . $val80[1], $val0)) {
                return false;
            }
            if (isset($val80[2]) && !in_array($val80[0] . "." . $val80[1] . "." . $val80[2], $val0)) {
                return false;
            }
            return true;
        }
        function check_plugin($val14 = '')
        {
            global $_W, $_GPC;
            $val96 = m("cache")->getString("permset", "global");
            if (empty($val96)) {
                return true;
            }
            if ($_W["role"] == "founder") {
                return true;
            }
            $val99 = $this->isopen($val14);
            if (!$val99) {
                return false;
            }
            $val102 = true;
            $val103 = pdo_fetchcolumn("SELECT acid FROM " . tablename("account_wechats") . " WHERE `uniacid`=:uniacid LIMIT 1", array(":uniacid" => $_W["uniacid"]));
            $val105 = pdo_fetch("select  plugins from " . tablename("ewei_shop_perm_plugin") . " where acid=:acid limit 1", array(":acid" => $val103));
            if (!empty($val105)) {
                $val108 = explode(",", $val105["plugins"]);
                if (!in_array($val14, $val108)) {
                    $val102 = false;
                }
            } else {
                load()->model("account");
                $val113 = uni_owned($_W["founder"]);
                if (in_array($_W["uniacid"], array_keys($val113))) {
                    $val102 = true;
                } else {
                    $val102 = false;
                }
            }
            if (!$val102) {
                return false;
            }
            return $this->check($val14);
        }
        public function getLogName($val121 = '', $val122 = null)
        {
            if (!$val122) {
                $val122 = $this->getLogTypes();
            }
            foreach ($val122 as $val127) {
                if ($val127["value"] == $val121) {
                    return $val127["text"];
                }
            }
            return '';
        }
        public function getLogTypes()
        {
            $val131 = array();
            $val0 = $this->allPerms();
            foreach ($val0 as $val135 => $val136) {
                if (isset($val136["child"])) {
                    foreach ($val136["child"] as $val139 => $val140) {
                        foreach ($val140 as $val142 => $val143) {
                            if (strexists($val143, "-log")) {
                                $val145 = str_replace("-log", "", $val136["text"] . "-" . $val140["text"] . "-" . $val143);
                                if ($val142 == "text") {
                                    $val145 = str_replace("-log", "", $val136["text"] . "-" . $val140["text"]);
                                }
                                $val131[] = array("text" => $val145, "value" => str_replace(".text", "", $val135 . "." . $val139 . "." . $val142));
                            }
                        }
                    }
                } else {
                    foreach ($val136 as $val142 => $val143) {
                        if (strexists($val143, "-log")) {
                            $val145 = str_replace("-log", "", $val136["text"] . "-" . $val143);
                            if ($val142 == "text") {
                                $val145 = str_replace("-log", "", $val136["text"]);
                            }
                            $val131[] = array("text" => $val145, "value" => str_replace(".text", "", $val135 . "." . $val142));
                        }
                    }
                }
            }
            return $val131;
        }
        public function log($val121 = '', $val174 = '')
        {
            global $_W;
            static $val177;
            if (!$val177) {
                $val177 = $this->getLogTypes();
            }
            $val180 = array("uniacid" => $_W["uniacid"], "uid" => $_W["uid"], "name" => $this->getLogName($val121, $val177), "type" => $val121, "op" => $val174, "ip" => CLIENT_IP, "createtime" => time());
            pdo_insert("ewei_shop_perm_log", $val180);
        }
        public function perms()
        {
            return array("perm" => array("text" => $this->getName(), "isplugin" => true, "child" => array("set" => array("text" => "基础设置"), "role" => array("text" => "角色", "view" => "浏览", "add" => "添加-log", "edit" => "修改-log", "delete" => "删除-log"), "user" => array("text" => "操作员", "view" => "浏览", "add" => "添加-log", "edit" => "修改-log", "delete" => "删除-log"), "log" => array("text" => "操作日志", "view" => "浏览", "delete" => "删除-log", "clear" => "清除-log"))));
        }
    }
}