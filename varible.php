<?php

$update = json_decode(file_get_contents('php://input'));

$message = $update->message;

$chat_id = $message->chat->id;

$from_id = $message->from->id;

$text = $update->message->text;

$video = $update->message->video;

$photo = $update->message->photo;

$fileid = $update->message->file_id;

$first_name = $update->message->from->first_name;

$last_name = $update->message->from->last_name;

$from_id = $update->message->from->id;

$username = $message->from->username;

$message_id = $message->message_id;

if(isset($update->callback_query)){

$data = $update->callback_query->data;

$data_id = $update->callback_query->id;

$messageid = $update->callback_query->message->message_id;

$chat_id = $update->callback_query->message->chat->id;

$chatid = $update->callback_query->message->chat->id;

$fromid = $update->callback_query->from->id;

$from_id = $update->callback_query->from->id;

$type = $update->callback_query->chat->type;

$message_id = $update->callback_query->message->message_id;

$first_name = $update->callback_query->from->first_name;

$last_name = $update->callback_query->from->last_name;

$username = $update->callback_query->from->username;

}

//$text = "🛍 | 10 گیگ";
//$coin = 1000000;
//$chat_id = 5529010823;
//$from_id = 5529010823;


// $join_e = json_decode(file_get_contents("https://api.telegram.org/bot".API_KEY."/getChatMember?chat_id=@$channel2&user_id=$from_id"));

// $join_2 = $join_e->result->status;


if (!file_exists('data'))
mkdir("data");

if (!file_exists('data/servers'))
mkdir("data/servers");

if (!file_exists('data/servers/v2ray'))
mkdir("data/servers/v2ray");

if (!file_exists('data/servers/ssh'))
mkdir("data/servers/ssh");

if (!file_exists('data/servers/openvpn'))
mkdir("data/servers/openvpn");

$ban = file_get_contents("data/ban.txt");

$bot = file_get_contents("data/bot.txt");

$channel1 = file_get_contents("data/channel.txt");

$panel_1 = file_get_contents("data/servers/v2ray/panel_1.txt");
$panel_2 = file_get_contents("data/servers/v2ray/panel_2.txt");
$panel_3 = file_get_contents("data/servers/v2ray/panel_3.txt");
$panel_4 = file_get_contents("data/servers/v2ray/panel_4.txt");
$panel_5 = file_get_contents("data/servers/v2ray/panel_5.txt");
$panel_6 = file_get_contents("data/servers/v2ray/panel_6.txt");
$panel_7 = file_get_contents("data/servers/v2ray/panel_7.txt");
$panel_8 = file_get_contents("data/servers/v2ray/panel_8.txt");

$s1v2raypanel_1 = file_get_contents("data/servers/s1v2ray/s1panel_1.txt");
$s1v2raypanel_2 = file_get_contents("data/servers/s1v2ray/s1panel_2.txt");
$s1v2raypanel_3 = file_get_contents("data/servers/s1v2ray/s1panel_3.txt");
$s1v2raypanel_4 = file_get_contents("data/servers/s1v2ray/s1panel_4.txt");
$s1v2raypanel_5 = file_get_contents("data/servers/s1v2ray/s1panel_5.txt");
$s1v2raypanel_6 = file_get_contents("data/servers/s1v2ray/s1panel_6.txt");
$s1v2raypanel_7 = file_get_contents("data/servers/s1v2ray/s1panel_7.txt");
$s1v2raypanel_8 = file_get_contents("data/servers/s1v2ray/s1panel_8.txt");



$openpanel_1 = file_get_contents("data/servers/openvpn/openpanel_1.txt");
$openpanel_2 = file_get_contents("data/servers/openvpn/openpanel_2.txt");
$openpanel_3 = file_get_contents("data/servers/openvpn/openpanel_3.txt");
$openpanel_4 = file_get_contents("data/servers/openvpn/openpanel_4.txt");
$openpanel_5 = file_get_contents("data/servers/openvpn/openpanel_5.txt");
$openpanel_6 = file_get_contents("data/servers/openvpn/openpanel_6.txt");
$openpanel_7 = file_get_contents("data/servers/openvpn/openpanel_7.txt");
$openpanel_8 = file_get_contents("data/servers/openvpn/openpanel_8.txt");


$state = file_get_contents("data/$from_id/state.txt");

$Member = file_get_contents("Member.txt");

$coin = file_get_contents("data/$from_id/coin.txt");
$buys = file_get_contents("data/$from_id/buys.txt");
$Code = file_get_contents("data/$from_id/Code.txt");
$logs = file_get_contents("data/$from_id/logs.txt");
$charge = file_get_contents("data/$from_id/charge.txt");

$userlog=file_get_contents("data/userlogs.txt");

$Money_Code = file_get_contents("data/$from_id/Money_Code.txt");