<?php
require_once '../inc/common.php';
require_once 'db/ba_base.php';
require_once 'db/ba_bind.php';
require_once  'db/ba_log_bind.php';

header("cache-control:no-cache,must-revalidate");
header("Content-Type:application/json;charset=utf-8");

/*
========================== 获取代理商绑定信息 ==========================
GET参数
  token               用户TOKEN
返回
  security_level      安全等级
  rows                安全信息组
    bind_type           绑定类型
    bind_name           绑定名称
    bind_info           绑定内容
    ctime               绑定时间
说明
*/

php_begin();
$args = array('token');
chk_empty_args('GET', $args);

// 用户token
$token = get_arg_str('GET', 'token',128);
$ba_id = check_token($token);
// 通过us_id获取安全等级
$security_level = get_ba_base_info($ba_id)['security_level'];
// 通过us_id获取用户基本信息
$rows = get_ba_bind_info_by_token($ba_id);

$count = count($rows);
// 获取ba_log_bind的信息
$ret_photo  = get_ba_log_bind_info_for_idPhoto($ba_id);
$ret_name   = get_ba_log_bind_info_for_idName($ba_id);
$ret_id  = get_ba_log_bind_info_for_idNum($ba_id);
foreach ($rows as $row){
    if($row['bind_name'] == 'idNum'){
        $ret_id = '';
    }
    if($row['bind_name'] == 'idPhoto'){
        $ret_photo='';
    }
    if($row['bind_name'] == 'name'){
        $ret_name = '';
    }
}
if(!empty($ret_photo)){
    $rows[$count+1] = $ret_photo;
}

if(!empty($ret_name)){
    $rows[$count+2] = $ret_name;
}

if(!empty($ret_id)){
    $rows[$count+3] = $ret_id;
}
// 返回数据做成
$rtn_ary = array();
$rtn_ary['errcode'] = '0';
$rtn_ary['errmsg'] = '';
$rtn_ary['rows'] = $rows;
$rtn_ary['security_level'] = $security_level;
$rtn_str = json_encode($rtn_ary);
php_end($rtn_str);
