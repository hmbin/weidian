<?php
global $_W, $_GPC;
//QQ:834633039
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
$category = m('plugin')->getCategory();
foreach ($category as $ck => &$cv) {
    $plugins = array();
    $catplugins = pdo_fetchall('select * from ' . tablename('ewei_shop_plugin') . ' where category=:category order by displayorder asc', array(':category' => $ck));
    foreach ($catplugins as $cp) {
        if ($cp['identity'] == 'system') {
            if (!$_W['isfounder']) {
                continue;
            }
        }
        $plugins[] = $cp;
    }
    $cv['plugins'] = $plugins;
}
unset($cv);
include $this->template('web/plugins/list');
exit;