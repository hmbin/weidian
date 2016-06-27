<?php
if (!defined("IN_IA")) {
    exit("Access Denied");
}
class Ewei_DShop_Plugin
{
    public function getSet($val0 = '', $val1 = '', $val2 = 0)
    {
        global $_W, $_GPC;
        if (empty($val2)) {
            $val2 = $_W["uniacid"];
        }
        $val8 = m("cache")->getArray("sysset", $val2);
        if (empty($val8)) {
            $val8 = pdo_fetch("select * from " . tablename("ewei_shop_sysset") . " where uniacid=:uniacid limit 1", array(":uniacid" => $val2));
        }
        if (empty($val8)) {
            return array();
        }
        $val14 = unserialize($val8["sets"]);
        if (empty($val1)) {
            return $val14;
        }
        return $val14[$val1];
    }
    public function exists($val20 = '')
    {
        $val21 = pdo_fetchall("select * from " . tablename("ewei_shop_plugin") . " where identity=:identyty limit  1", array(":identity" => $val20));
        if (empty($val21)) {
            return false;
        }
        return true;
    }
    public function getAll()
    {
        global $_W;
        $val25 = m("cache")->getArray("plugins", "global");
        if (empty($val25)) {
            $val25 = pdo_fetchall("select * from " . tablename("ewei_shop_plugin") . " order by displayorder asc");
            m("cache")->set("plugins", $val25, "global");
        }
        return $val25;
    }
    public function getCategory()
    {
        return array("biz" => array("name" => "业务类"), "sale" => array("name" => "营销类"), "tool" => array("name" => "工具类"), "help" => array("name" => "辅助类"));
    }
}