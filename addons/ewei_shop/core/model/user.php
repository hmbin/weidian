<?php
if (!defined("IN_IA")) {
    exit("Access Denied");
}
class Ewei_DShop_User
{
    private $sessionid;
    public function __construct()
    {
        global $_W;
        $this->sessionid = "__cookie_ewei_shop_201507200000_{$_W['uniacid']}";
    }
    function getOpenid()
    {
        $val4 = $this->getInfo(false, true);
        return $val4["openid"];
    }
    function getPerOpenid()
    {
        global $_W, $_GPC;
        $val9 = 24 * 3600 * 3;
        session_set_cookie_params($val9);
        @session_start();
        $val11 = "__cookie_ewei_shop_openid_{$_W["uniacid"]}";
        $val13 = base64_decode($_COOKIE[$val11]);
        if (!empty($val13)) {
            return $val13;
        }
        load()->func("communication");
        $val18 = $_W["account"]["key"];
        $val20 = $_W["account"]["secret"];
        $val22 = "";
        $val23 = $_GPC["code"];
        $val25 = $_W["siteroot"] . "app/index.php?" . $_SERVER["QUERY_STRING"];
        if (empty($val23)) {
            $val29 = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $val18 . "&redirect_uri=" . urlencode($val25) . "&response_type=code&scope=snsapi_base&state=123#wechat_redirect";
            header("location: " . $val29);
            exit;
        } else {
            $val33 = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $val18 . "&secret=" . $val20 . "&code=" . $val23 . "&grant_type=authorization_code";
            $val37 = ihttp_get($val33);
            $val39 = @json_decode($val37["content"], true);
            if (!empty($val39) && is_array($val39) && $val39["errmsg"] == "invalid code") {
                $val29 = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $val18 . "&redirect_uri=" . urlencode($val25) . "&response_type=code&scope=snsapi_base&state=123#wechat_redirect";
                header("location: " . $val29);
                exit;
            }
            if (is_array($val39) && !empty($val39["openid"])) {
                $val22 = $val39["access_token"];
                $val13 = $val39["openid"];
                setcookie($val11, base64_encode($val13));
            } else {
                $val56 = explode("&", $_SERVER["QUERY_STRING"]);
                $val58 = array();
                foreach ($val56 as $val60) {
                    if (!strexists($val60, "code=") && !strexists($val60, "state=") && !strexists($val60, "from=") && !strexists($val60, "isappinstalled=")) {
                        $val58[] = $val60;
                    }
                }
                $val67 = $_W["siteroot"] . "app/index.php?" . implode("&", $val58);
                $val29 = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $val18 . "&redirect_uri=" . urlencode($val67) . "&response_type=code&scope=snsapi_base&state=123#wechat_redirect";
                header("location: " . $val29);
                exit;
            }
        }
        return $val13;
    }
    function getInfo($val75 = false, $val76 = false)
    {
        global $_W, $_GPC;
        $val4 = array();
        if (EWEI_SHOP_DEBUG) {
            $val4 = array("openid" => "oT-ihv9XGkJbX9owJiLZcZPAJcog", "nickname" => "狸小狐", "headimgurl" => "https://ss0.bdstatic.com/5aV1bjqh_Q23odCf/static/superman/img/logo/bd_logo1_31bdc765.png", "province" => "山东", "city" => "青岛");
        } else {
            load()->model("mc");
            if (empty($_GPC["directopenid"])) {
                $val4 = mc_oauth_userinfo();
            } else {
                $val4 = array("openid" => $this->getPerOpenid());
            }
            $val85 = true;
            if ($_W["container"] != "wechat") {
                if ($_GPC["do"] == "order" && $_GPC["p"] == "pay") {
                    $val85 = false;
                }
                if ($_GPC["do"] == "member" && $_GPC["p"] == "recharge") {
                    $val85 = false;
                }
                if ($_GPC["do"] == "plugin" && $_GPC["p"] == "article" && $_GPC["preview"] == "1") {
                    $val85 = false;
                }
            }
            if (empty($val4["openid"]) && $val85) {
                die("<!DOCTYPE html>\r\n                <html>\r\n                    <head>\r\n                        <meta name='viewport' content='width=device-width, initial-scale=1, user-scalable=0'>\r\n                        <title>抱歉，出错了</title><meta charset='utf-8'><meta name='viewport' content='width=device-width, initial-scale=1, user-scalable=0'><link rel='stylesheet' type='text/css' href='https://res.wx.qq.com/connect/zh_CN/htmledition/style/wap_err1a9853.css'>\r\n                    </head>\r\n                    <body>\r\n                    <div class='page_msg'><div class='inner'><span class='msg_icon_wrp'><i class='icon80_smile'></i></span><div class='msg_content'><h4>请在微信客户端打开链接</h4></div></div></div>\r\n                    </body>\r\n                </html>");
            }
        }
        if ($val75) {
            return urlencode(base64_encode(json_encode($val4)));
        }
        return $val4;
    }
    function oauth_info()
    {
        global $_W, $_GPC;
        if ($_W["container"] != "wechat") {
            if ($_GPC["do"] == "order" && $_GPC["p"] == "pay") {
                return array();
            }
            if ($_GPC["do"] == "member" && $_GPC["p"] == "recharge") {
                return array();
            }
        }
        $val9 = 24 * 3600 * 3;
        session_set_cookie_params($val9);
        @session_start();
        $val111 = "__cookie_ewei_shop_201507100000_{$_W['uniacid']}";
        $val113 = json_decode(base64_decode($_SESSION[$val111]), true);
        $val13 = is_array($val113) ? $val113["openid"] : '';
        $val119 = is_array($val113) ? $val113["openid"] : '';
        if (!empty($val13)) {
            return $val113;
        }
        load()->func("communication");
        $val18 = $_W["account"]["key"];
        $val20 = $_W["account"]["secret"];
        $val22 = "";
        $val23 = $_GPC["code"];
        $val25 = $_W["siteroot"] . "app/index.php?" . $_SERVER["QUERY_STRING"];
        if (empty($val23)) {
            $val29 = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $val18 . "&redirect_uri=" . urlencode($val25) . "&response_type=code&scope=snsapi_userinfo&state=123#wechat_redirect";
            header("location: " . $val29);
            exit;
        } else {
            $val33 = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $val18 . "&secret=" . $val20 . "&code=" . $val23 . "&grant_type=authorization_code";
            $val37 = ihttp_get($val33);
            $val39 = @json_decode($val37["content"], true);
            if (!empty($val39) && is_array($val39) && $val39["errmsg"] == "invalid code") {
                $val29 = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $val18 . "&redirect_uri=" . urlencode($val25) . "&response_type=code&scope=snsapi_userinfo&state=123#wechat_redirect";
                header("location: " . $val29);
                exit;
            }
            if (empty($val39) || !is_array($val39) || empty($val39["access_token"]) || empty($val39["openid"])) {
                die("获取token失败,请重新进入!");
            } else {
                $val22 = $val39["access_token"];
                $val13 = $val39["openid"];
            }
        }
        $val162 = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $val22 . "&openid=" . $val13 . "&lang=zh_CN";
        $val37 = ihttp_get($val162);
        $val4 = @json_decode($val37["content"], true);
        if (isset($val4["nickname"])) {
            $_SESSION[$val111] = base64_encode(json_encode($val4));
            return $val4;
        } else {
            die("获取用户信息失败，请重新进入!");
        }
    }
    function followed($val13 = '')
    {
        global $_W;
        $val176 = !empty($val13);
        if ($val176) {
            $val179 = pdo_fetch("select follow from " . tablename("mc_mapping_fans") . " where openid=:openid and uniacid=:uniacid limit 1", array(":openid" => $val13, ":uniacid" => $_W["uniacid"]));
            $val176 = $val179["follow"] == 1;
        }
        return $val176;
    }
}