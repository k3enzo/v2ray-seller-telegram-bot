<?php

$start = json_encode([
    'keyboard'=>[
	[['text'=>"🔐 | OPEN VPN - Sstp - l2tp - pptp"]],
        [ ['text'=>"🔐 | تک سرورv2ray"] , ['text'=>"🔐 | چند سروره v2ray"] ] ,
        [ ['text'=>"💳 | افزایش موجودی"] , ['text'=>"👥 | اطلاعات حساب"] ] ,
	[['text'=>"🔐 | گزارش 20اکانت خریداری شده"]],
        [ ['text'=>"❗️| راهنما"] , ['text'=>"💌 | کد هدیه"] ]
	   ],'resize_keyboard'=>true
]);

$v2ray = json_encode([
    'keyboard'=>[
        [ ['text'=>"🛍 | 60 گیگ"], ['text'=>"🛍 | 10 گیگ"] ] ,
        [ ['text'=>"🛍 | 80 گیگ"] , ['text'=>"🛍 | 20 گیگ"] ] ,
        [ ['text'=>"🛍 | 100 گیگ"] , ['text'=>"🛍 | 30 گیگ"] ] ,
        [ ['text'=>"🛍 | نامحدود"] , ['text'=>"🛍 | 40 گیگ"] ] ,
        [ ['text'=>"🔙"] ] 
    ],'resize_keyboard'=>true
]);

$s1v2ray = json_encode([
    'keyboard'=>[
	
        [ ['text'=>"🛍| 60 گیگ"] , ['text'=>"🛍| 10 گیگ"] ] ,
        [ ['text'=>"🛍| 70 گیگ"] , ['text'=>"🛍| 20 گیگ"] ] ,
        [ ['text'=>"🛍| 100 گیگ"] , ['text'=>"🛍| 30 گیگ"] ] ,
        [ ['text'=>"🛍| نامحدود"] , ['text'=>"🛍| 40 گیگ"] ] ,
        [ ['text'=>"🔙"] ] 
    ],'resize_keyboard'=>true
]);

$openvpn = json_encode([
    'keyboard'=>[
	[ ['text'=>"شارژ اکانت openvpn"], ['text'=>"بزودی مدیریت اکانت"] ],
	[ ['text'=>"🛍 |60 گیگ"]  , ['text'=>"🛍 |10 گیگ"] ] ,
        [ ['text'=>"🛍 |80 گیگ"]  , ['text'=>"🛍 |20 گیگ"] ] ,
        [ ['text'=>"🛍 |100 گیگ"] , ['text'=>"🛍 |30 گیگ"] ] ,
        [ ['text'=>"🛍 |نامحدود"]   , ['text'=>"🛍 |40 گیگ"]  ] ,
        [ ['text'=>"❗️|OPEN VPN راهنما"] ], 
	[ ['text'=>"دانلود مستقیم برنامه"] , ['text'=>"دانلود مستقیم پروفایل"] ],
	[ ['text'=>"🔙"] ] 
    ],'resize_keyboard'=>true
]);

$openvpnch= json_encode([
    'keyboard'=>[
								[ ['text'=>"شارژ اکانت 10گیگ"]	,	['text'=>"شارژ اکانت 20گیگ"] ] ,
								[ ['text'=>"شارژ اکانت 30گیگ"]	,	['text'=>"شارژ اکانت 40گیگ"] ] ,
								[ ['text'=>"شارژ اکانت 50گیگ"]	,	['text'=>"شارژ اکانت 60گیگ"] ] ,
								[ ['text'=>"شارژ اکانت 70گیگ"]	,	['text'=>"شارژ اکانت 80گیگ"] ] ,
								[ ['text'=>"شارژ اکانت 100گیگ"]	,	['text'=>"شارژ اکانت نامحدود"] ] ,
								[ ['text'=>"🔙"] ]
    ],'resize_keyboard'=>true
]);

$back = json_encode([
    'keyboard'=>[
        [ ['text'=>"🔙"] ]
				    ],'resize_keyboard'=>true
]);

$back_panel = json_encode([
    'keyboard'=>[
        [ ['text'=>"🔙 برگشت"] ]

    ],'resize_keyboard'=>true
]);

$panel = json_encode([
    'keyboard'=>[
        [ ['text'=>"v2rayآمار"] ,['text'=>"openvpnآمار"],['text'=>"آمارتک سرور"]] ,
        [ ['text'=>"فوروارد همگانی"] , ['text'=>"پیام همگانی"] ] ,
        [ ['text'=>"اطلاعات کاربر"] , ['text'=>"کد هدیه"] ] ,
        [ ['text'=>"افزایش موجودی"] , ['text'=>"کسر موجودی"] ] ,
        [ ['text'=>"بن کاربر"] , ['text'=>"انبن کاربر"] ] ,
        [ ['text'=>"افزودن سرویس"] , ['text'=>"تنظیم چنل"] ] ,
        [ ['text'=>"خاموش"] , ['text'=>"روشن"] ] ,
        [ ['text'=>"🔙"] ] 
					    ],'resize_keyboard'=>true
]);

$newpanel = json_encode([
    'keyboard'=>[
        [ ['text'=>"30 گیگ"],['text'=>"20 گیگ"] ],
        [ ['text'=>"60 گیگ"],['text'=>"40 گیگ"] ],
        [ ['text'=>"نامحدود"],['text'=>"100 گیگ"] ],
        [ ['text'=>"🔙 برگشت"] ]
				    ] ]);

$remove = json_encode(['KeyboardRemove'=>[],'remove_keyboard'=>true]);