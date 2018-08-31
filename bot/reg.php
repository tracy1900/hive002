<?php
require_once '../inc/common.php';
require_once 'db/bot_bind.php';
header("cache-control:no-cache,must-revalidate");
header("Content-Type:application/json;charset=utf-8");

/*
========================== 微信用户注册 ==========================
GET参数
  token           用户TOKEN
  nikename        string类型
  hash            HASH内容
  pass_word_hash  密码HASH

返回
  errcode = 0     请求成功

说明
  HASH值绑定
*/

php_begin();

$args = array('bot_nikename');
chk_empty_args('GET', $args);

//用户信息唯一值
$data['bot_id'] = get_guid();
$data['bot_nikename'] = get_arg_str('GET', 'bot_nikename');
//获取最后一个remark
$bot_mark = get_bot_last_mark();
if(!$bot_mark) {
    $bot_mark='100';
}else{
    $bot_mark=$bot_mark['bot_mark']+1;
}
$data['bot_mark'] = $bot_mark;
$data['bot_create_time'] = time();
//注册
$result = ins_bind_bot_info($data);
if (!$result){
    exit_error('190','注册失败');
}
exit_ok();




?>
