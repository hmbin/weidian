<?php
@eval('//www.phpjiami.com 专属VIP会员加密!');
global $_W, $_GPC;
//QQ:834633039
ca('creditshop.notice.view');
$set = $this->getSet();
if (checksubmit('submit')) {
    ca('creditshop.notice.save');
    $set['tm'] = is_array($_GPC['tm']) ? $_GPC['tm'] : array();
    if (is_array($_GPC['openids'])) {
        $set['tm']['openids'] = implode(",", $_GPC['openids']);
    }
    $this->updateSet($set);
    plog('creditshop.notice.save', '修改积分商城通知设置');
    message('设置保存成功!', referer(), 'success');
}
$salers = array();
if (isset($set['tm']['openids'])) {
    if (!empty($set['tm']['openids'])) {
        $openids = array();
        $strsopenids = explode(",", $set['tm']['openids']);
        foreach ($strsopenids as $openid) {
            $openids[] = "'" . $openid . "'";
        }
        $salers = pdo_fetchall("select id,nickname,avatar,openid from " . tablename('ewei_shop_member') . ' where openid in (' . implode(",", $openids) . ") and uniacid={$_W['uniacid']}");
    }
}
load()->func('tpl');
include $this->template('notice');