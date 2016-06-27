<?php
if (!defined("IN_IA")) {
    exit("Access Denied");
}
if (!class_exists("ArticleModel")) {
    class ArticleModel extends PluginModel {
        public function doShare($val0, $val1, $val2) {
            global $_W, $_GPC;
            if (empty($val0) || empty($val1) || empty($val2) || $val1 == $val2) {
                return;
            }
            $val10 = m("member")->getMember($val1);
            $val12 = m("member")->getMember($val2);
            if (empty($val12) || empty($val10)) {
                return;
            }
            $val16 = m("common")->getSysset("shop");
            $val17 = intval($val0["article_rule_credit"]);
            $val19 = floatval($val0["article_rule_money"]);
            $val21 = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename("ewei_shop_article_share") . " WHERE aid=:aid and click_user=:click_user and uniacid=:uniacid ", array(
                ":aid" => $val0["id"],
                ":click_user" => $val2,
                ":uniacid" => $_W["uniacid"]
            ));
            if (!empty($val21)) {
                $val17 = intval($val0["article_rule_credit2"]);
                $val19 = floatval($val0["article_rule_money2"]);
            }
            if (!empty($val0["article_hasendtime"]) && time() > $val0["article_endtime"]) {
                return;
            }
            $val32 = $val0["article_readtime"];
            if ($val32 <= 0) {
                $val32 = 4;
            }
            $val36 = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename("ewei_shop_article_share") . " WHERE aid=:aid and share_user=:share_user and click_user=:click_user and uniacid=:uniacid ", array(
                ":aid" => $val0["id"],
                ":share_user" => $val1,
                ":click_user" => $val2,
                ":uniacid" => $_W["uniacid"]
            ));
            if ($val36 >= $val32) {
                return;
            }
            $val43 = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename("ewei_shop_article_share") . " WHERE aid=:aid and share_user=:share_user and uniacid=:uniacid ", array(
                ":aid" => $val0["id"],
                ":share_user" => $val1,
                ":uniacid" => $_W["uniacid"]
            ));
            if ($val43 >= $val0["article_rule_allnum"]) {
                $val17 = 0;
                $val19 = 0;
            } else {
                $val51 = mktime(0, 0, 0, date("m") , date("d") , date("Y"));
                $val52 = mktime(0, 0, 0, date("m") , date("d") + 1, date("Y")) - 1;
                $val53 = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename("ewei_shop_article_share") . " WHERE aid=:aid and share_user=:share_user and click_date>:day_start and click_date<:day_end and uniacid=:uniacid ", array(
                    ":aid" => $val0["id"],
                    ":share_user" => $val1,
                    ":day_start" => $val51,
                    ":day_end" => $val52,
                    ":uniacid" => $_W["uniacid"]
                ));
                if ($val53 >= $val0["article_rule_daynum"]) {
                    $val17 = 0;
                    $val19 = 0;
                }
            }
            $val63 = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename("ewei_shop_article_share") . " WHERE aid=:aid and share_user=:click_user and click_user=:share_user and uniacid=:uniacid ", array(
                ":aid" => $val0["id"],
                ":share_user" => $val1,
                ":click_user" => $val2,
                ":uniacid" => $_W["uniacid"]
            ));
            if (!empty($val63)) {
                return;
            }
            if ($val0["article_rule_credittotal"] > 0 || $val0["article_rule_moneytotal"] > 0) {
                $val71 = 0;
                $val72 = 0;
                $val73 = pdo_fetchcolumn("select count(distinct click_user) from " . tablename("ewei_shop_article_share") . " where aid=:aid and uniacid=:uniacid limit 1", array(
                    ":aid" => $val0["id"],
                    ":uniacid" => $_W["uniacid"]
                ));
                $val76 = pdo_fetchcolumn("select count(*) from " . tablename("ewei_shop_article_share") . " where aid=:aid and uniacid=:uniacid limit 1", array(
                    ":aid" => $val0["id"],
                    ":uniacid" => $_W["uniacid"]
                ));
                $val79 = $val76 - $val73;
                if ($val0["article_rule_credittotal"] > 0) {
                    $val71 = $val0["article_rule_credittotal"] - ($val73 + $val0["article_readnum_v"]) * $val0["article_rule_creditm"] - $val79 * $val0["article_rule_creditm2"];
                }
                if ($val0["article_rule_moneytotal"] > 0) {
                    $val72 = $val0["article_rule_moneytotal"] - ($val73 + $val0["article_readnum_v"]) * $val0["article_rule_moneym"] - $val79 * $val0["article_rule_moneym2"];
                }
                $val71 <= 0 && $val71 = 0;
                $val72 <= 0 && $val72 = 0;
                if ($val71 <= 0) {
                    $val17 = 0;
                }
                if ($val72 <= 0) {
                    $val19 = 0;
                }
            }
            $val106 = array(
                "aid" => $val0["id"],
                "share_user" => $val1,
                "click_user" => $val2,
                "click_date" => time() ,
                "add_credit" => $val17,
                "add_money" => $val19,
                "uniacid" => $_W["uniacid"]
            );
            pdo_insert("ewei_shop_article_share", $val106);
            if ($val17 > 0) {
                m("member")->setCredit($val10["openid"], "credit1", $val17, array(
                    0,
                    $val16["name"] . " 文章营销奖励积分"
                ));
            }
            if ($val19 > 0) {
                m("member")->setCredit($val10["openid"], "credit2", $val19, array(
                    0,
                    $val16["name"] . " 文章营销奖励余额"
                ));
            }
            if ($val17 > 0 || $val19 > 0) {
                $val124 = pdo_fetch("SELECT * FROM " . tablename("ewei_shop_article_sys") . " WHERE uniacid=:uniacid limit 1 ", array(
                    ":uniacid" => $_W["uniacid"]
                ));
                $val126 = $_W["siteroot"] . "app/index.php?i=" . $_W["uniacid"] . "&c=entry&m=ewei_shop&do=member";
                $val129 = '';
                if ($val17 > 0) {
                    $val129.= $val17 . "个积分、";
                }
                if ($val19 > 0) {
                    $val129.= $val19 . "元余额";
                }
                $val136 = array(
                    "first" => array(
                        "value" => "您的奖励已到帐！",
                        "color" => "#4a5077"
                    ) ,
                    "keyword1" => array(
                        "title" => "任务名称",
                        "value" => "分享得奖励",
                        "color" => "#4a5077"
                    ) ,
                    "keyword2" => array(
                        "title" => "通知类型",
                        "value" => "用户通过您的分享进入文章《" . $val0["article_title"] . "》，系统奖励您" . $val129 . "。",
                        "color" => "#4a5077"
                    ) ,
                    "remark" => array(
                        "value" => "奖励已发放成功，请到会员中心查看。",
                        "color" => "#4a5077"
                    )
                );
                if (!empty($val124["article_message"])) {
                    m("message")->sendTplNotice($val10["openid"], $val124["article_message"], $val136, $val126);
                } else {
                    m("message")->sendCustomNotice($val10["openid"], $val136, $val126);
                }
            }
        }
        function mid_replace($val147) {
            global $_GPC;
            preg_match_all('/href\=["|\'](.*?)["|\']/is', $val147, $val150);
            foreach ($val150[1] as $val152 => $val153) {
                $val154 = $this->href_replace($val153);
                $val147 = str_replace($val150[0][$val152], "href=\"{$val154}\"", $val147);
            }
            return $val147;
        }
        function href_replace($val153) {
            global $_GPC;
            $val154 = $val153;
            if (strexists($val153, "ewei_shop") && !strexists($val153, "&mid")) {
                if (strexists($val153, "?")) {
                    $val154 = $val153 . "&mid=" . intval($_GPC["mid"]);
                } else {
                    $val154 = $val153 . "?mid=" . intval($_GPC["mid"]);
                }
            }
            return $val154;
        }
        function perms() {
            return array(
                "article" => array(
                    "text" => $this->getName() ,
                    "isplugin" => true,
                    "child" => array(
                        "cate" => array(
                            "text" => "分类设置",
                            "addcate" => "添加分类-log",
                            "editcate" => "编辑分类-log",
                            "delcate" => "删除分类-log"
                        ) ,
                        "page" => array(
                            "text" => "文章设置",
                            "add" => "添加文章-log",
                            "edit" => "修改文章-log",
                            "delete" => "删除文章-log",
                            "showdata" => "查看数据统计",
                            "otherset" => "其他设置",
                            "report" => "举报记录"
                        )
                    )
                )
            );
        }
    }
}
