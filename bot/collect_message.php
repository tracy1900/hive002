<?php
require_once '../inc/common.php';
require_once 'db/bot_bind.php';
header("cache-control:no-cache,must-revalidate");
header("Content-Type:application/json;charset=utf-8");

/*
========================== 微信收集群聊信息 ==========================
GET参数
  nickname        string类型
  content          string
  send_time       datatime

返回
  errcode = 0     请求成功

说明
  HASH值绑定
*/

php_begin();

$args = array('nickname','content','send_time');
chk_empty_args('GET', $args);

//信息唯一值
$data['bot_message_id'] = get_guid();
$data['bot_nickname'] = get_arg_str('GET','nickname');
$data['bot_content'] = get_arg_str('GET', 'content');
$data['bot_send_time'] = get_arg_str('GET', 'send_time');
$data['bot_create_time'] = time();
//注册
$result = ins_bot_mes_info($data);
if (!$result){
    exit_error('190','注册失败');
}
exit_ok();




?>
