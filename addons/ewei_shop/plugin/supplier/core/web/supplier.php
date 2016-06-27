<?php
global $_W, $_GPC;
$operation   = empty($_GPC['op']) ? 'display' : $_GPC['op'];
if ($operation == 'display') {
    $roleid = pdo_fetchcolumn('select id from ' . tablename('ewei_shop_perm_role') . ' where status1=1');
    $where = '';
    if(empty($_GPC['uid'])){
        $where .= ' and uniacid=' . $_W['uniacid'];
    }else{
        $where .= ' and uid="' . $_GPC['uid'] . '" and uniacid=' . $_W['uniacid'];
    }

    $list = pdo_fetchall('select * from ' . tablename('ewei_shop_perm_user') . ' where roleid='. $roleid . " " .$where);
    
    $total = count($list);
	
} else if ($operation == 'detail') {
    $uid = intval($_GPC['uid']);
    //todo,uid要加引号或者intval
	$supplierinfo = pdo_fetch('select * from ' . tablename('ewei_shop_perm_user') . ' where uid="' . $uid . '" and uniacid=' . $_W['uniacid']);
	if(!empty($supplierinfo['openid'])){
        $saler = m('member')->getInfo($supplierinfo['openid']);
    }
    $totalmoney = pdo_fetchcolumn(' select ifnull(sum(g.costprice*og.total),0) from ' . tablename('ewei_shop_order_goods') . ' og left join ' . tablename('ewei_shop_order') . ' o on o.id=og.orderid left join ' . tablename('ewei_shop_goods') . ' g on g.id=og.goodsid where og.supplier_uid=:supplier_uid and og.uniacid=:uniacid AND o.`status` = 3',array(
                ':supplier_uid' => $uid,
                ':uniacid' => $_W['uniacid']
            ));
    $totalmoneyok = pdo_fetchcolumn(' select ifnull(sum(g.costprice*og.total),0) from ' . tablename('ewei_shop_order_goods') . ' og left join ' . tablename('ewei_shop_order') . ' o on o.id=og.orderid left join ' . tablename('ewei_shop_goods') . ' g on g.id=og.goodsid where og.supplier_uid=:supplier_uid and og.supplier_apply_status=1 and og.uniacid=:uniacid AND o.`status` = 3',array(
                ':supplier_uid' => $uid,
                ':uniacid' => $_W['uniacid']
            ));
    if(checksubmit('submit')){
    	$data = is_array($_GPC['data']) ? $_GPC['data'] : array();
    	pdo_update('ewei_shop_perm_user', $data, array(
            'uid' => $uid
        ));
        message('保存成功!', $this->createPluginWebUrl('supplier/supplier'), 'success');
    }
} 
load()->func('tpl');
include $this->template('supplier');
