<?php
require_once '../inc/common.php';
require_once '../inc/judge_format.php';
require_once 'db/ba_base.php';
require_once 'db/ba_bind.php';
require_once '../plugin/email/send_email.php';
require_once  'db/ba_log_bind.php';

header("cache-control:no-cache,must-revalidate");
header("Content-Type:application/json;charset=utf-8");

/*
========================== 请求邮箱验证 ==========================
GET参数
  email           Email地址
返回
  errcode= 0       请求成功
说明
  返回验证码  随机的六位数验证码
*/

php_begin();
$args = array('email');
chk_empty_args('GET', $args);

// email地址
$email = get_arg_str('GET', 'email', 255);
// 判断是否为邮箱地址
$is_email = isEmail($email);
if(!$is_email){
    exit_error('100','The input format is incorrect');
}
//获取当前时间戳
$timestamp = time();
$variable = 'email';
//加盐加密
$salt = rand(100000, 999999);
// 判断邮箱是否已存在
$row = get_ba_id_by_variable($variable,$email);

// 邮件地址已经存在
if($row['ba_id']){
    //是否注册验证完成
    switch ($row['bind_flag'])
    {
        case 1:
            // exit_error('110','Registered users please login directly!');
            break;
        case 9:
            exit_error('105','Registered users please login directly!');
            break;
    }
}
$url = Config::CONFORM_URL;
//发送绑定验证信息
//判断验证码发送数量是否超过最大限制
$email_code_num_limit = ba_phone_code_limit_check($email);
if($email_code_num_limit>4)
    exit_error('108','no times for send code');

//获取最新一条发送记录
$email_code_last_time = get_ba_log_bind_by_variable('email',$email);
//判断是否在限制时间范围内
if($email_code_last_time['limt_time'] > time())
    exit_error('116',$email_code_last_time['limt_time'] - time());
// $timestamp +=15*60;
$title = '邮箱验证';
// $des = new Des();
$body = "您的验证码是:".$salt ."，如果非本人操作无需理会！";
$ret = send_email($name='', $email, $title, $body);

$time_limit = time() + 60 ;
$data = array();
$data['ba_id']  = get_guid();
$data['bind_name']  = 'email';
$data['bind_info']  = $email;
$data['count_error'] = 0;
$data['limt_time']  = $time_limit;
$data['bind_type']  = 'text';
$data['bind_salt']  = $salt;
$res = ins_ba_verification_code($data);
if($res) {
    exit_ok('Please verify email as soon as possible!');
}
exit_error('124', 'Create failed! Please try again!');
