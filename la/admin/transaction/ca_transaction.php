<?php
require_once "../../../inc/common.php";
require_once  "db/us_ca_recharge_request.php";
require_once  "db/us_ca_withdraw_request.php";
require_once "db/la_admin.php";

header("cache-control:no-cache,must-revalidate");
header("Content-Type:application/json;charset=utf-8");

/*
========================== 获取ca交易i列表 ==========================
GET参数

返回
rows            信息数组
      recharge_ca   用户ca提现记录数组
      qa_id          请求ID
      us_id          用户ID
      ca_id          代理商ID
      ca_account_id  代理商账号ID（Hash）
      lgl_amount     法定货币金额
      base_amount    充值资产金额
      tx_type        交易类型
      tx_detail      交易明细（JSON）
      tx_fee         交易手续费
      tx_time        请求时间戳
      qa_flag        订单状态
      tx_hash        订单hash
     withdraw_ca   用户ca充值记录数组
      qa_id         请求ID
      us_id         用户ID
      ca_id         代理商ID
      us_account_id 用户账号ID（Hash）
      lgl_amount    法定定货币金额
      base_amount   提现资产金额
      tx_hash       交易HASH
      tx_type       交易类型
      tx_detail     交易明细（JSON）
      tx_fee        交易手续费
      tx_time       请求时间戳
      qa_flag       订单状态1:已处理，2拒绝，0：未处理
说明
*/

php_begin();
$args = array('token');
chk_empty_args('GET', $args);

// 用户token
$token = get_arg_str('GET', 'token', 128);

$key = Config::TOKEN_KEY;
// 获取token并解密
$des = new Des();
$decryption_code = $des -> decrypt($token, $key);
$now_time = time();
$code_conf =  explode(',',$decryption_code);
// 获取token中的需求信息
$user = $code_conf[0];
$timestamp = $code_conf[1];
if($timestamp < $now_time){
    exit_error('114','Token timeout please retrieve!');
}
//判断la是否存在
$row = get_la_by_user($user);
if(!$row){
    exit_error('120','用户不存在');
}
// 获取ca的交易记录
$recharge_rows = get_ca_recharge_log_balance();
$withdraw_rows = get_ca_withdraw_log_balance();
$row_recharge = array();
$row_withdraw = array();
$rows =array();
foreach ($recharge_rows as $recharge_row){
    $recharge_row['tx_time'] = date("Y-m-d H:i:s",$recharge_row['tx_time']);
    $row_recharge[]=$recharge_row;
}
foreach ($withdraw_rows as $withdraw_row){
    $withdraw_row['tx_time'] = date("Y-m-d H:i:s",$withdraw_row['tx_time']);
    $row_withdraw[]=$withdraw_row;
}
$rows['recharge'] = $row_recharge;
$rows['withdraw'] = $row_withdraw;

$rtn_ary = array();
$rtn_ary['errcode'] = '0';
$rtn_ary['errmsg'] = '';
$rtn_ary['rows'] = $rows;
$rtn_str = json_encode($rtn_ary);
php_end($rtn_str);
