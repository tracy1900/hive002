<?php
//======================================
// 函数: 获取微信信息表中最后一个标识符
// 参数: 无
// 返回: $row        最新信息数组
//======================================
function get_bot_last_mark()
{
  $db = new DB_COM();
  $sql = "SELECT bot_mark FROM bot_base ORDER BY bot_mark DESC LIMIT 1 ";
  $db -> query($sql);
  $row = $db -> fetchRow();
  return $row;
}
//======================================
// 函数: 注册微信用户
// 参数: $data
//返回： true               成功
//        false             失败
//======================================
function ins_bind_bot_info($data)
{
    $db = new DB_COM();
    $sql = $db->sqlInsert("bot_base", $data);
    $q_id = $db->query($sql);
    if ($q_id == 0)
        return false;
    return true;
}
//======================================
// 函数: 收集群聊信息
// 参数: $data
//返回： true               成功
//        false             失败
//======================================
function ins_bot_mes_info($data)
{
    $db = new DB_COM();
    $sql = $db->sqlInsert("bot_message", $data);
    $q_id = $db->query($sql);
    if ($q_id == 0)
        return false;
    return true;
}


?>
