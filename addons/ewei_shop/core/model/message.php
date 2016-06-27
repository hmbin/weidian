<?php
if (!defined("IN_IA")) {
    exit("Access Denied");
}
class Ewei_DShop_Message
{
    public function sendTplNotice($val0, $val1, $val2, $val3 = '', $val4 = null)
    {
        if (!$val4) {
            $val4 = m("common")->getAccount();
        }
        if (!$val4) {
            return;
        }
        return $val4->sendTplNotice($val0, $val1, $val2, $val3);
    }
    public function sendCustomNotice($val12, $val13, $val3 = '', $val4 = null)
    {
        if (!$val4) {
            $val4 = m("common")->getAccount();
        }
        if (!$val4) {
            return;
        }
        $val19 = "";
        if (is_array($val13)) {
            foreach ($val13 as $val22 => $val23) {
                if (!empty($val23["title"])) {
                    $val19 .= $val23["title"] . ":" . $val23["value"] . "\r\n";
                } else {
                    $val19 .= $val23["value"] . "\r\n";
                    if ($val22 == 0) {
                        $val19 .= "\r\n";
                    }
                }
            }
        } else {
            $val19 = $val13;
        }
        if (!empty($val3)) {
            $val19 .= "<a href='{$val3}'>点击查看详情</a>";
        }
        return $val4->sendCustomNotice(array("touser" => $val12, "msgtype" => "text", "text" => array("content" => urlencode($val19))));
    }
    public function sendImage($val12, $val41)
    {
        $val4 = m("common")->getAccount();
        return $val4->sendCustomNotice(array("touser" => $val12, "msgtype" => "image", "image" => array("media_id" => $val41)));
    }
    public function sendNews($val12, $val47, $val4 = null)
    {
        if (!$val4) {
            $val4 = m("common")->getAccount();
        }
        return $val4->sendCustomNotice(array("touser" => $val12, "msgtype" => "news", "news" => array("articles" => $val47)));
    }
}