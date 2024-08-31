<?php

//require_once 'vendor/autoload.php';

//use GuzzleHttp\Client;


ob_start();
//$load = sys_getloadavg();
$telegram_ip_ranges = [
    ['lower' => '149.154.160.0', 'upper' => '149.154.175.255'],
    ['lower' => '91.108.4.0', 'upper' => '91.108.7.255'],
];
$ip_dec = (float) sprintf("%u", ip2long($_SERVER['REMOTE_ADDR']));
$ok=false;
foreach ($telegram_ip_ranges as $telegram_ip_range) {
    if (!$ok) {
        $lower_dec = (float) sprintf("%u", ip2long($telegram_ip_range['lower']));
        $upper_dec = (float) sprintf("%u", ip2long($telegram_ip_range['upper']));
        if($ip_dec >= $lower_dec and $ip_dec <= $upper_dec) {
            $ok=true;
        }
    }
}



ini_set('error_logs','on');
error_reporting(1);
include('config.php');
include('data/banner.php');
include('function.php');
include('keyboard.php');
include('varible.php');
//include_once 'api/class/v2ray.php';
include_once 'api/class/Dbase.php';
include_once 'api/class/xuiConnect.php';
include_once 'api/v2ray.php';
date_default_timezone_set('Asia/Tehran');
$time = date('H:i:s');
$date = date('Y/m/d');


function setPanelContent($file,$replace)
{
    $panelContent = file_get_contents($file);
    return file_put_contents($file,$panelContent."\n".$replace);
}

if (!empty($chat_id)) {
    $v2Class = new VPNServer(1);
    $v2Class->setTelegramUser($chat_id, $username ?? $from_id);

    $v2Class2 = new VPNServer(2);
    $v2Class2->setTelegramUser($chat_id, $username ?? $from_id);

}
if(strpos($ban,"$from_id")!==false){
    exit();
}

if($bot == "off" and $chat_id != $Dev){
    bot('sendmessage',[
        'chat_id'=>$chat_id,
        'text'=>"ربات خاموش میباشد."
    ]);
    exit();
}

$join_a = json_decode(file_get_contents("https://api.telegram.org/bot".API_KEY."/getChatMember?chat_id=@$channel1&user_id=$from_id"));
$join_1 = $join_a->result->status;
if($channel1 == true and $join_1 != 'member'  &&  $join_1 != 'creator' && $join_1 != 'administrator'){

    bot('sendMessage', [
        'chat_id' => $chat_id,
        'text'=>"
کاربر عزیز شما عضو ربات نیستید و امکان استفاده از ربات را ندارید ⚠️
         
⭕️ لطفا در کانال زیر عضو شوید 
        
سپس به ربات برگشته و مجدد امتحان کنید ✔️",
                'parse_mode'=>"html",
                'reply_markup'=>json_encode([
                    'inline_keyboard'=>[
                        [['text'=>"< bests_v2ray >",'url'=>"https://t.me/bests_v2ray/$channel1"]]
                    ]
                ])
                ]);

        return false;
}



if ($text == "/start"){
    if (!file_exists("data/$from_id/state.txt")) {
        mkdir("data/$from_id");
        file_put_contents("data/$from_id/state.txt","none");
        file_put_contents("data/$from_id/coin.txt","0");
        file_put_contents("data/$from_id/buys.txt","0");
        file_put_contents("data/$from_id/logs.txt","a");
	file_put_contents("data/$from_id/charge.txt","a");
       	$myfile2 = fopen("Member.txt", "a") or die("Unable to open file!");
        fwrite($myfile2, "$from_id\n");
        fclose($myfile2);
    }
    file_put_contents("data/$from_id/state.txt","none");
    bot('sendmessage',[
        'chat_id'=>$chat_id,
        'text'=>"
🌵 [ به ربات خوش اومدی ]
        "
        ,'reply_markup'=>$start
    ]);
}

elseif ($text == "🔙"){
    file_put_contents("data/$from_id/state.txt","none");
    bot('sendmessage',[
        'chat_id'=>$chat_id,
        'text'=>"
$banner

    
    "
        ,'reply_markup'=>$start
    ]);
}

elseif ($text == "🔙 برگشت" and $chat_id == $Dev){
    file_put_contents("data/$from_id/state.txt","none");
    bot('sendmessage',[
        'chat_id'=>$chat_id,
        'text'=>"
به پنل برگشتیم
        "
        ,'reply_markup'=>$panel
    ]);
}
//-----------------------------------------------
if ($text == "🔐 | گزارش 20اکانت خریداری شده") {
    $file = "data/$from_id/logs.txt"; // مسیر فایل مورد نظر خود را قرار دهید
    $lines = [10];

    if (file_exists($file)) {
        $lines = array_slice(file($file), -20); // خواندن 10 خط آخر از فایل
    } else {
        echo "فایل وجود ندارد.";
    }

    // ارسال خطوط به تلگرام
    $message = implode("\n", $lines); // تبدیل آرایه خطوط به یک رشته با خطوط جداشده توسط "\n"

    bot('sendmessage', [
        'chat_id' => $chat_id,
        'text' => $message,
    ]);
}
//-----------------------------------------------
elseif ($text == "🔐 | چند سروره v2ray"){
    bot('sendmessage',[
        'chat_id'=>$chat_id,
        'text'=>"
🌵 [ به بخش محصولات خوش اومدی ]
$smv2banner
           "
        ,'reply_markup'=>$v2ray
    ]);
}

elseif ($text == "🛍 | 10 گیگ"){

    if ( $coin <= $money_panel1-1 ) {
        bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
موجودی کافی نیست لطفا از قسمت افزایش موجودی اقدام کنید
قیمت کانفیگ $money_panel1 هزار تومان
            "
            ,'reply_markup'=>$panels
        ]);
        exit();

    }else{

        file_put_contents("data/$from_id/coin.txt",$coin - $money_panel1);
        file_put_contents("data/$from_id/buys.txt",$buys + 1);
	    $remaincoin=file_get_contents("data/$from_id/coin.txt");

        $v2rayConfig = $v2Class->createUser(10,30,'vmess','ws');
        setPanelContent("data/servers/v2ray/panel_1.txt",$v2rayConfig);

        $config = explode("\n",$panel_1);

        $c = $config[0];

        $class = str_replace("$c\n",null,$panel_1);

        file_put_contents("data/servers/v2ray/panel_1.txt",$class);

        $id_config = explode("+",$c);

        $id = rand(11111,999999999);


        if (!empty($id_config[1])){
            $link = file_get_contents($id_config[1]);
            $configs = base64_decode($link);
            preg_match_all("/(.*?)\n(.*?)/",$configs,$configs);
            for($i=0;$i<=count($configs[0])-1;$i++){
                bot('sendphoto',[
                    'chat_id'=>$chat_id,
                    'photo'=>"https://public-api.qr-code-generator.com/v1/create/free?image_format=PNG&image_width=300&download=1&foreground_color=%23000000&frame_color=%23000000&frame_name=no-frame&qr_code_logo=&qr_code_pattern=&qr_code_text=".urlencode($configs[0][$i]),
                    'caption'=>$configs[0][$i]
                ]);
            }
        }else{
            bot('sendphoto',[
                'chat_id'=>$chat_id,
                'photo'=>"https://public-api.qr-code-generator.com/v1/create/free?image_format=PNG&image_width=300&download=1&foreground_color=%23000000&frame_color=%23000000&frame_name=no-frame&qr_code_logo=&qr_code_pattern=&qr_code_text=".urlencode($v2rayConfig),
                'caption'=>$v2rayConfig
            ]);
        }


        bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
کد های شما ساخته شدند
ایدی شما : <pre>$id</pre>
مانده اعتبار شما $coin
ایدی شما همان نام کاربری و کلمه عبور شما می باشد
که میتوانید از سایت ذیل جهت ریز مصرف  گزارش گیری نمائید
http://m1.vaxon1.click/#/dashboard
لطفا ایدی خودرا جهت پیگیری مشکلات و یا شارژ مجدد نزد خود نگه دارید
            ",'parse_mode'=>"html"
            ,'reply_markup'=>$start
        ]);
 bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
مانده اعتبار | $remaincoin
   ",'parse_mode'=>"html"
            ,'reply_markup'=>$start
        ]);

        bot('sendmessage',[
            'chat_id'=>$Dev,
            'text'=>"
کانفیگ خریداری شد

عددی خریدار : $from_id
یوزرنیم خریدار : @$username

ایدی کانفیگ : <pre>$id</pre>
            ",'parse_mode'=>"html"
        ]);
        $logfile2 = fopen("data/$from_id/logs.txt", "a") or die("Unable to open file!");
        fwrite($logfile2,"$date,$time,$money_panel1,$id\n");
        fclose($logfile2);
       	$adminlog = fopen("data/userslogs.txt", "a") or die("Unable to open file!");
	fwrite($adminlog,"$from_id,$date,$time,$money_panel1,$c\n");
        fclose($adminlog);
	exit();


    }

}


elseif ($text == "🛍 | 20 گیگ"){


    if ( $coin <= $money_panel2-1 ) {

        bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
موجودی کافی نیست.
قیمت کانفیگ : $money_panel2
            "
            ,'reply_markup'=>$v2ray
        ]);
        exit();

    }else{

        file_put_contents("data/$from_id/coin.txt",$coin - $money_panel2);
        file_put_contents("data/$from_id/buys.txt",$buys + 1);
	$remaincoin=file_get_contents("data/$from_id/coin.txt");

        $v2rayConfig = $v2Class->createUser(20,30,'vmess','ws');
        setPanelContent("data/servers/v2ray/panel_2.txt",$v2rayConfig);
        $config = explode("\n",$panel_2);

        $c = $config[0];

        $class = str_replace("$c\n",null,$panel_2);
        file_put_contents("data/servers/v2ray/panel_2.txt",$class);

        $id_config = explode("+",$c);

        $id = rand(11111,999999999);


        if (!empty($id_config[1])){
            $link = file_get_contents($id_config[1]);
            $configs = base64_decode($link);
            preg_match_all("/(.*?)\n(.*?)/",$configs,$configs);
            for($i=0;$i<=count($configs[0])-1;$i++){
                bot('sendphoto',[
                    'chat_id'=>$chat_id,
                    'photo'=>"https://public-api.qr-code-generator.com/v1/create/free?image_format=PNG&image_width=300&download=1&foreground_color=%23000000&frame_color=%23000000&frame_name=no-frame&qr_code_logo=&qr_code_pattern=&qr_code_text=".urlencode($configs[0][$i]),
                    'caption'=>$configs[0][$i]
                ]);
            }
        }else{
            bot('sendphoto',[
                'chat_id'=>$chat_id,
                'photo'=>"https://public-api.qr-code-generator.com/v1/create/free?image_format=PNG&image_width=300&download=1&foreground_color=%23000000&frame_color=%23000000&frame_name=no-frame&qr_code_logo=&qr_code_pattern=&qr_code_text=".urlencode($v2rayConfig),
                'caption'=>$v2rayConfig
            ]);
        }

        bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
کد های شما ساخته شدند
ایدی شما : <pre>$id</pre> 
ایدی شما همان نام کاربری و کلمه عبور شما می باشد
که میتوانید از سایت ذیل جهت ریز مصرف  گزارش گیری نمائید
http://m1.vaxon1.click/#/dashboard
لطفا ایدی خودرا جهت پیگیری مشکلات و یا شارژ مجدد نزد خود نگه دارید
            ",'parse_mode'=>"html"
            ,'reply_markup'=>$start
        ]);
 bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
مانده اعتبار | $remaincoin
   ",'parse_mode'=>"html"
            ,'reply_markup'=>$start
        ]);

        bot('sendmessage',[
            'chat_id'=>$Dev,
            'text'=>"
کانفیگ خریداری شد

شناسه عددی خریدار : $from_id
نام کاربری خریدار : @$username

ایدی کانفیگ : <pre>$id</pre>
            ",'parse_mode'=>"html"
        ]);
        $logfile2 = fopen("data/$from_id/logs.txt", "a") or die("Unable to open file!");
        fwrite($logfile2,"$date,$time,$money_panel2,$c\n");
        fclose($logfile2);
       	$adminlog = fopen("data/userslogs.txt", "a") or die("Unable to open file!");
	fwrite($adminlog,"$from_id,$date,$time,$money_panel2,$c\n");
        fclose($adminlog);
	exit();


    }

}
elseif ($text == "🛍 | 30 گیگ"){


    if ( $coin <= $money_panel3-1 ) {

        bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
موجودی کافی نیست.
قیمت کانفیگ : $money_panel3
            "
            ,'reply_markup'=>$v2ray
        ]);
        exit();

    }else{

        file_put_contents("data/$from_id/coin.txt",$coin - $money_panel3);
        file_put_contents("data/$from_id/buys.txt",$buys + 1);
	$remaincoin=file_get_contents("data/$from_id/coin.txt");

        $v2rayConfig = $v2Class->createUser(30,30,'vmess','ws');

        setPanelContent("data/servers/v2ray/panel_3.txt",$v2rayConfig);

        $config = explode("\n",$panel_3);

        $c = $config[0];

        $class = str_replace("$c\n",null,$panel_3);
        file_put_contents("data/servers/v2ray/panel_3.txt",$class);

        $id_config = explode("+",$c);

        $id = rand(11111,999999999);

        if (!empty($id_config[1])){
            $link = file_get_contents($id_config[1]);
            $configs = base64_decode($link);
            preg_match_all("/(.*?)\n(.*?)/",$configs,$configs);
            for($i=0;$i<=count($configs[0])-1;$i++){
                bot('sendphoto',[
                    'chat_id'=>$chat_id,
                    'photo'=>"https://public-api.qr-code-generator.com/v1/create/free?image_format=PNG&image_width=300&download=1&foreground_color=%23000000&frame_color=%23000000&frame_name=no-frame&qr_code_logo=&qr_code_pattern=&qr_code_text=".urlencode($configs[0][$i]),
                    'caption'=>$configs[0][$i]
                ]);
            }
        }else{
            bot('sendphoto',[
                'chat_id'=>$chat_id,
                'photo'=>"https://public-api.qr-code-generator.com/v1/create/free?image_format=PNG&image_width=300&download=1&foreground_color=%23000000&frame_color=%23000000&frame_name=no-frame&qr_code_logo=&qr_code_pattern=&qr_code_text=".urlencode($v2rayConfig),
                'caption'=>$v2rayConfig
            ]);
        }

        bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
کد های شما ساخته شدند
ایدی شما : <pre>$id</pre> 
ایدی شما همان نام کاربری و کلمه عبور شما می باشد
که میتوانید از سایت ذیل جهت ریز مصرف  گزارش گیری نمائید
http://m1.vaxon1.click/#/dashboard
لطفا ایدی خودرا جهت پیگیری مشکلات و یا شارژ مجدد نزد خود نگه دارید
            ",'parse_mode'=>"html"
            ,'reply_markup'=>$start
        ]);
 bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
مانده اعتبار | $remaincoin
   ",'parse_mode'=>"html"
            ,'reply_markup'=>$start
        ]);

        bot('sendmessage',[
            'chat_id'=>$Dev,
            'text'=>"
کانفیگ خریداری شد

شناسه خریدار : $from_id
نام کاربری خریدار : @$username

ایدی کانفیگ : <pre>$id</pre>
            ",'parse_mode'=>"html"
        ]);
        $logfile2 = fopen("data/$from_id/logs.txt", "a") or die("Unable to open file!");
        fwrite($logfile2,"$date,$time,$money_panel3,$c\n");
        fclose($logfile2);
       	$adminlog = fopen("data/userslogs.txt", "a") or die("Unable to open file!");
	fwrite($adminlog,"$from_id,$date,$time,$money_panel3,$c\n");
        fclose($adminlog);
	exit();


    }

}

elseif ($text == "🛍 | 40 گیگ"){

    if ( $coin <= $money_panel4-1 ) {

        bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
موجودی کافی نیست.
قیمت کانفیگ : $money_panel4
            "
            ,'reply_markup'=>$v2ray
        ]);
        exit();

    }else{

        file_put_contents("data/$from_id/coin.txt",$coin - $money_panel4);
        file_put_contents("data/$from_id/buys.txt",$buys + 1);
	$remaincoin=file_get_contents("data/$from_id/coin.txt");

        $v2rayConfig = $v2Class->createUser(40,30,'vmess','ws');

        setPanelContent("data/servers/v2ray/panel_4.txt",$v2rayConfig);

        $config = explode("\n",$panel_4);

        $c = $config[0];

        $class = str_replace("$c\n",null,$panel_4);
        file_put_contents("data/servers/v2ray/panel_4.txt",$class);

        $id_config = explode("+",$c);

        $id = rand(11111,999999999);


        if (!empty($id_config[1])){
            $link = file_get_contents($id_config[1]);
            $configs = base64_decode($link);
            preg_match_all("/(.*?)\n(.*?)/",$configs,$configs);
            for($i=0;$i<=count($configs[0])-1;$i++){
                bot('sendphoto',[
                    'chat_id'=>$chat_id,
                    'photo'=>"https://public-api.qr-code-generator.com/v1/create/free?image_format=PNG&image_width=300&download=1&foreground_color=%23000000&frame_color=%23000000&frame_name=no-frame&qr_code_logo=&qr_code_pattern=&qr_code_text=".urlencode($configs[0][$i]),
                    'caption'=>$configs[0][$i]
                ]);
            }
        }else{
            bot('sendphoto',[
                'chat_id'=>$chat_id,
                'photo'=>"https://public-api.qr-code-generator.com/v1/create/free?image_format=PNG&image_width=300&download=1&foreground_color=%23000000&frame_color=%23000000&frame_name=no-frame&qr_code_logo=&qr_code_pattern=&qr_code_text=".urlencode($v2rayConfig),
                'caption'=>$v2rayConfig
            ]);
        }

        bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
کد های شما ساخته شدند
ایدی شما : <pre>$id</pre> 
ایدی شما همان نام کاربری و کلمه عبور شما می باشد
که میتوانید از سایت ذیل جهت ریز مصرف  گزارش گیری نمائید
http://m1.vaxon1.click/#/dashboard
لطفا ایدی خودرا جهت پیگیری مشکلات و یا شارژ مجدد نزد خود نگه دارید
            ",'parse_mode'=>"html"
            ,'reply_markup'=>$start
        ]);
 bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
مانده اعتبار | $remaincoin
   ",'parse_mode'=>"html"
            ,'reply_markup'=>$start
        ]);

        bot('sendmessage',[
            'chat_id'=>$Dev,
            'text'=>"
کانفیگ خریداری شد

عددی خریدار : $from_id
یوزرنیم خریدار : @$username

ایدی کانفیگ : <pre>$id</pre>
            ",'parse_mode'=>"html"
        ]);
        $logfile2 = fopen("data/$from_id/logs.txt", "a") or die("Unable to open file!");
        fwrite($logfile2,"$date,$time,$money_panel4,$c\n");
        fclose($logfile2);
       	$adminlog = fopen("data/userslogs.txt", "a") or die("Unable to open file!");
	fwrite($adminlog,"$from_id,$date,$time,$money_panel4,$c\n");
        fclose($adminlog);
	exit();

    }

}


elseif ($text == "🛍 | 60 گیگ"){

    if ( $coin <= $money_panel5-1 ) {

        bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
موجودی کافی نیست.
قیمت کانفیگ : $money_panel5
            "
            ,'reply_markup'=>$v2ray
        ]);
        exit();

    }else{

        file_put_contents("data/$from_id/coin.txt",$coin - $money_panel5);
        file_put_contents("data/$from_id/buys.txt",$buys + 1);
	$remaincoin=file_get_contents("data/$from_id/coin.txt");

        $v2rayConfig = $v2Class->createUser(60,30,'vmess','ws');

        setPanelContent("data/servers/v2ray/panel_5.txt",$v2rayConfig);


        $config = explode("\n",$panel_5);

        $c = $config[0];

                $class = str_replace("$c\n",null,$panel_5);
        file_put_contents("data/servers/v2ray/panel_5.txt",$class);


        $id_config = explode("+",$c);

        $id = rand(11111,999999999);


        if (!empty($id_config[1])){
            $link = file_get_contents($id_config[1]);
            $configs = base64_decode($link);
            preg_match_all("/(.*?)\n(.*?)/",$configs,$configs);
            for($i=0;$i<=count($configs[0])-1;$i++){
                bot('sendphoto',[
                    'chat_id'=>$chat_id,
                    'photo'=>"https://public-api.qr-code-generator.com/v1/create/free?image_format=PNG&image_width=300&download=1&foreground_color=%23000000&frame_color=%23000000&frame_name=no-frame&qr_code_logo=&qr_code_pattern=&qr_code_text=".urlencode($configs[0][$i]),
                    'caption'=>$configs[0][$i]
                ]);
            }
        }else{
            bot('sendphoto',[
                'chat_id'=>$chat_id,
                'photo'=>"https://public-api.qr-code-generator.com/v1/create/free?image_format=PNG&image_width=300&download=1&foreground_color=%23000000&frame_color=%23000000&frame_name=no-frame&qr_code_logo=&qr_code_pattern=&qr_code_text=".urlencode($v2rayConfig),
                'caption'=>$v2rayConfig
            ]);
        }

        bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
کد های شما ساخته شدند
ایدی شما : <pre>$id</pre> 
ایدی شما همان نام کاربری و کلمه عبور شما می باشد
که میتوانید از سایت ذیل جهت ریز مصرف  گزارش گیری نمائید
http://m1.vaxon1.click/#/dashboard
لطفا ایدی خودرا جهت پیگیری مشکلات و یا شارژ مجدد نزد خود نگه دارید
            ",'parse_mode'=>"html"
            ,'reply_markup'=>$start
        ]);
 bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
مانده اعتبار | $remaincoin
   ",'parse_mode'=>"html"
            ,'reply_markup'=>$start
        ]);

        bot('sendmessage',[
            'chat_id'=>$Dev,
            'text'=>"
کانفیگ خریداری شد

عددی خریدار : $from_id
یوزرنیم خریدار : @$username

ایدی کانفیگ : <pre>$id</pre>
            ",'parse_mode'=>"html"
        ]);
        $logfile2 = fopen("data/$from_id/logs.txt", "a") or die("Unable to open file!");
        fwrite($logfile2,"$date,$time,$money_panel5,$c\n");
        fclose($logfile2);
       	$adminlog = fopen("data/userslogs.txt", "a") or die("Unable to open file!");
	fwrite($adminlog,"$from_id,$date,$time,$money_panel5,$c\n");
        fclose($adminlog);
	exit();

    }

}
elseif ($text == "🛍 | 80 گیگ"){

    if ( $coin <= $money_panel6-1 ) {

        bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
موجودی کافی نیست.
قیمت کانفیگ : $money_panel6
            "
            ,'reply_markup'=>$v2ray
        ]);
        exit();

    }else{

        file_put_contents("data/$from_id/coin.txt",$coin - $money_panel6);
        file_put_contents("data/$from_id/buys.txt",$buys + 1);
	$remaincoin=file_get_contents("data/$from_id/coin.txt");

        $v2rayConfig = $v2Class->createUser(80,30,'vmess','ws');

        setPanelContent("data/servers/v2ray/panel_6.txt",$v2rayConfig);

        $config = explode("\n",$panel_6);

        $c = $config[0];

        $class = str_replace("$c\n",null,$panel_6);
        file_put_contents("data/servers/v2ray/panel_6.txt",$class);

        $id_config = explode("+",$c);

        $id = rand(11111,999999999);


        if (!empty($id_config[1])){
            $link = file_get_contents($id_config[1]);
            $configs = base64_decode($link);
            preg_match_all("/(.*?)\n(.*?)/",$configs,$configs);
            for($i=0;$i<=count($configs[0])-1;$i++){
                bot('sendphoto',[
                    'chat_id'=>$chat_id,
                    'photo'=>"https://public-api.qr-code-generator.com/v1/create/free?image_format=PNG&image_width=300&download=1&foreground_color=%23000000&frame_color=%23000000&frame_name=no-frame&qr_code_logo=&qr_code_pattern=&qr_code_text=".urlencode($configs[0][$i]),
                    'caption'=>$configs[0][$i]
                ]);
            }
        }else{
            bot('sendphoto',[
                'chat_id'=>$chat_id,
                'photo'=>"https://public-api.qr-code-generator.com/v1/create/free?image_format=PNG&image_width=300&download=1&foreground_color=%23000000&frame_color=%23000000&frame_name=no-frame&qr_code_logo=&qr_code_pattern=&qr_code_text=".urlencode($v2rayConfig),
                'caption'=>$v2rayConfig
            ]);
        }

        bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
کد های شما ساخته شدند
ایدی شما : <pre>$id</pre> 
ایدی شما همان نام کاربری و کلمه عبور شما می باشد
که میتوانید از سایت ذیل جهت ریز مصرف  گزارش گیری نمائید
http://m1.vaxon1.click/#/dashboard
لطفا ایدی خودرا جهت پیگیری مشکلات و یا شارژ مجدد نزد خود نگه دارید
            ",'parse_mode'=>"html"
            ,'reply_markup'=>$start
        ]);
 bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
مانده اعتبار | $remaincoin
   ",'parse_mode'=>"html"
            ,'reply_markup'=>$start
        ]);

        bot('sendmessage',[
            'chat_id'=>$Dev,
            'text'=>"
کانفیگ خریداری شد

عددی خریدار : $from_id
یوزرنیم خریدار : @$username

ایدی کانفیگ : <pre>$id</pre>
            ",'parse_mode'=>"html"
        ]);
        $logfile2 = fopen("data/$from_id/logs.txt", "a") or die("Unable to open file!");
        fwrite($logfile2,"$date,$time,$money_panel6,$c\n");
        fclose($logfile2);
       	$adminlog = fopen("data/userslogs.txt", "a") or die("Unable to open file!");
	fwrite($adminlog,"$from_id,$date,$time,$money_panel6,$c\n");
        fclose($adminlog);
	exit();

    }

}


elseif ($text == "🛍 | 100 گیگ"){

    if ( $coin <= $money_panel7-1 ) {

        bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
موجودی کافی نیست.
قیمت کانفیگ : $money_panel7
            "
            ,'reply_markup'=>$v2ray
        ]);
        exit();

    }else{

        file_put_contents("data/$from_id/coin.txt",$coin - $money_panel7);
        file_put_contents("data/$from_id/buys.txt",$buys + 1);
	$remaincoin=file_get_contents("data/$from_id/coin.txt");

        $v2rayConfig = $v2Class->createUser(100,30,'vmess','ws');

        setPanelContent("data/servers/v2ray/panel_7.txt",$v2rayConfig);

        $config = explode("\n",$panel_7);

        $c = $config[0];

        $class = str_replace("$c\n",null,$panel_7);
        file_put_contents("data/servers/v2ray/panel_7.txt",$class);

        $id_config = explode("+",$c);

        $id = rand(11111,999999999);


        if (!empty($id_config[1])){
            $link = file_get_contents($id_config[1]);
            $configs = base64_decode($link);
            preg_match_all("/(.*?)\n(.*?)/",$configs,$configs);
            for($i=0;$i<=count($configs[0])-1;$i++){
                bot('sendphoto',[
                    'chat_id'=>$chat_id,
                    'photo'=>"https://public-api.qr-code-generator.com/v1/create/free?image_format=PNG&image_width=300&download=1&foreground_color=%23000000&frame_color=%23000000&frame_name=no-frame&qr_code_logo=&qr_code_pattern=&qr_code_text=".urlencode($configs[0][$i]),
                    'caption'=>$configs[0][$i]
                ]);
            }
        }else{
            bot('sendphoto',[
                'chat_id'=>$chat_id,
                'photo'=>"https://public-api.qr-code-generator.com/v1/create/free?image_format=PNG&image_width=300&download=1&foreground_color=%23000000&frame_color=%23000000&frame_name=no-frame&qr_code_logo=&qr_code_pattern=&qr_code_text=".urlencode($v2rayConfig),
                'caption'=>$v2rayConfig
            ]);
        }

        bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
کد های شما ساخته شدند
ایدی شما : <pre>$id</pre> 
ایدی شما همان نام کاربری و کلمه عبور شما می باشد
که میتوانید از سایت ذیل جهت ریز مصرف  گزارش گیری نمائید
http://m1.vaxon1.click/#/dashboard
لطفا ایدی خودرا جهت پیگیری مشکلات و یا شارژ مجدد نزد خود نگه دارید
            ",'parse_mode'=>"html"
            ,'reply_markup'=>$start
        ]);
 bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
مانده اعتبار | $remaincoin
   ",'parse_mode'=>"html"
            ,'reply_markup'=>$start
        ]);

        bot('sendmessage',[
            'chat_id'=>$Dev,
            'text'=>"
کانفیگ خریداری شد

عددی خریدار : $from_id
یوزرنیم خریدار : @$username

ایدی کانفیگ : <pre>$id</pre>
            ",'parse_mode'=>"html"
        ]);
        $logfile2 = fopen("data/$from_id/logs.txt", "a") or die("Unable to open file!");
        fwrite($logfile2,"$date,$time,$money_panel7,$c\n");
        fclose($logfile2);
       	$adminlog = fopen("data/userslogs.txt", "a") or die("Unable to open file!");
	fwrite($adminlog,"$from_id,$date,$time,$money_panel7,$c\n");
        fclose($adminlog);
	exit();
    }

}


elseif ($text == "🛍 | نامحدود"){


    if ( $coin <= $money_panel8-1 ) {

        bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
موجودی کافی نیست.
قیمت کانفیگ : $money_panel8
            "
            ,'reply_markup'=>$v2ray
        ]);
        exit();

    }else{

        file_put_contents("data/$from_id/coin.txt",$coin - $money_panel8);
        file_put_contents("data/$from_id/buys.txt",$buys + 1);
	$remaincoin=file_get_contents("data/$from_id/coin.txt");

        $v2rayConfig = $v2Class->createUser(200,30,'vmess','ws');

        setPanelContent("data/servers/v2ray/panel_8.txt",$v2rayConfig);


        $config = explode("\n",$panel_8);
        $c = $config[0];

        $class = str_replace("$c\n",null,$panel_8);
        file_put_contents("data/servers/v2ray/panel_8.txt",$class);

        $id_config = explode("+",$c);

        $id = rand(11111,999999999);


        if (!empty($id_config[1])){
            $link = file_get_contents($id_config[1]);
            $configs = base64_decode($link);
            preg_match_all("/(.*?)\n(.*?)/",$configs,$configs);
            for($i=0;$i<=count($configs[0])-1;$i++){
                bot('sendphoto',[
                    'chat_id'=>$chat_id,
                    'photo'=>"https://public-api.qr-code-generator.com/v1/create/free?image_format=PNG&image_width=300&download=1&foreground_color=%23000000&frame_color=%23000000&frame_name=no-frame&qr_code_logo=&qr_code_pattern=&qr_code_text=".urlencode($configs[0][$i]),
                    'caption'=>$configs[0][$i]
                ]);
            }
        }else{
            bot('sendphoto',[
                'chat_id'=>$chat_id,
                'photo'=>"https://public-api.qr-code-generator.com/v1/create/free?image_format=PNG&image_width=300&download=1&foreground_color=%23000000&frame_color=%23000000&frame_name=no-frame&qr_code_logo=&qr_code_pattern=&qr_code_text=".urlencode($v2rayConfig),
                'caption'=>$v2rayConfig
            ]);
        }

        bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
کد های شما ساخته شدند
ایدی شما : <pre>$id</pre> 
ایدی شما همان نام کاربری و کلمه عبور شما می باشد
که میتوانید از سایت ذیل جهت ریز مصرف  گزارش گیری نمائید
http://m1.vaxon1.click/#/dashboard
لطفا ایدی خودرا جهت پیگیری مشکلات و یا شارژ مجدد نزد خود نگه دارید
            ",'parse_mode'=>"html"
            ,'reply_markup'=>$start
        ]);
 bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
مانده اعتبار | $remaincoin
   ",'parse_mode'=>"html"
            ,'reply_markup'=>$start
        ]);

        bot('sendmessage',[
            'chat_id'=>$Dev,
            'text'=>"
کانفیگ خریداری شد

عددی خریدار : $from_id
یوزرنیم خریدار : @$username

ایدی کانفیگ : <pre>$id</pre>
            ",'parse_mode'=>"html"
        ]);
        $logfile2 = fopen("data/$from_id/logs.txt", "a") or die("Unable to open file!");
        fwrite($logfile2,"$date,$time,$money_panel8,$c\n");
        fclose($logfile2);
       	$adminlog = fopen("data/userslogs.txt", "a") or die("Unable to open file!");
	fwrite($adminlog,"$from_id,$date,$time,$money_panel8,$c\n");
        fclose($adminlog);
	exit();
    }

}
//v2ray 1 server
elseif ($text == "🔐 | تک سرورv2ray"){
    bot('sendmessage',[
        'chat_id'=>$chat_id,
        'text'=>"
🌵 [ به بخش محصولات خوش اومدی ]
$s1v2banner
           "
        ,'reply_markup'=>$s1v2ray
    ]);
}
elseif ($text == "🛍| 10 گیگ"){

    if ( $coin <= $s1v2raymoney_panel1-1 ) {

        bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
موجودی کافی نیست.
قیمت کانفیگ : $s1v2raymoney_panel1
            "
            ,'reply_markup'=>$s1v2ray
        ]);
        exit();

    }else{

        file_put_contents("data/$from_id/coin.txt",$coin - $s1v2raymoney_panel1);
        file_put_contents("data/$from_id/buys.txt",$buys + 1);
	$remaincoin=file_get_contents("data/$from_id/coin.txt");

        $v2rayConfig = $v2Class2->createUser(10,30,'vmess','ws');

        setPanelContent("data/servers/s1v2ray/s1panel_1.txt",$v2rayConfig);

        $config = explode("\n",$s1v2raypanel_1);
        $c = $config[0];

        $class = str_replace("$c\n",null,$s1v2raypanel_1);
        file_put_contents("data/servers/s1v2ray/s1panel_1.txt",$class);
	$id_config = ($c);


        $id = rand(11111,999999999);


        if (!empty($id_config[1])){
            $link = file_get_contents($id_config[1]);
            $configs = base64_decode($link);
            preg_match_all("/(.*?)\n(.*?)/",$configs,$configs);
            for($i=0;$i<=count($configs[0])-1;$i++){
                bot('sendphoto',[
                    'chat_id'=>$chat_id,
                    'photo'=>"https://public-api.qr-code-generator.com/v1/create/free?image_format=PNG&image_width=300&download=1&foreground_color=%23000000&frame_color=%23000000&frame_name=no-frame&qr_code_logo=&qr_code_pattern=&qr_code_text=".urlencode($configs[0][$i]),
                    'caption'=>$configs[0][$i]
                ]);
            }
        }else{
            bot('sendphoto',[
                'chat_id'=>$chat_id,
                'photo'=>"https://public-api.qr-code-generator.com/v1/create/free?image_format=PNG&image_width=300&download=1&foreground_color=%23000000&frame_color=%23000000&frame_name=no-frame&qr_code_logo=&qr_code_pattern=&qr_code_text=".urlencode($v2rayConfig),
                'caption'=>$v2rayConfig
            ]);
        }
       bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
لطفا منو و یا کانال راهنما را مطالعه نمائید
https://t.me/learningv2ray
لطفا یوزرنیم خودرا جهت پیگیری مشکلات و یا شارژ مجدد نزد خود نگه دارید :
 <pre>$c</pre>
   ",'parse_mode'=>"html"
            ,'reply_markup'=>$start
        ]);
 bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
مانده اعتبار | $remaincoin
   ",'parse_mode'=>"html"
            ,'reply_markup'=>$start
        ]);

  bot('sendmessage',[
            'chat_id'=>$Dev,
            'text'=>"
کانفیگ خریداری شد
شناسه خریدار : $from_id
نام کاربری خریدار : @$username
ایدی کانفیگ : <pre>$c</pre>
        ",'parse_mode'=>"html"
        ]);
        $logfile2 = fopen("data/$from_id/logs.txt", "a") or die("Unable to open file!");
        fwrite($logfile2,"$date,$time,$s1v2raymoney_panel1,$c\n");
        fclose($logfile2);
       	$adminlog = fopen("data/userslogs.txt", "a") or die("Unable to open file!");
		fwrite($adminlog,"$from_id,$date,$time,$s1v2raymoney_panel1,$c\n");
        fclose($adminlog);
	exit();
    }

}
elseif ($text == "🛍| 20 گیگ"){

    if ( $coin <= $s1v2raymoney_panel2-1 ) {

        bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
موجودی کافی نیست.
قیمت کانفیگ : $s1v2raymoney_panel2
            "
            ,'reply_markup'=>$s1v2ray
        ]);
        exit();

    }else{

        file_put_contents("data/$from_id/coin.txt",$coin - $s1v2raymoney_panel2);
        file_put_contents("data/$from_id/buys.txt",$buys + 1);
	$remaincoin=file_get_contents("data/$from_id/coin.txt");

        $v2rayConfig = $v2Class2->createUser(20,30,'vmess','ws');

        setPanelContent("data/servers/s1v2ray/s1panel_2.txt",$v2rayConfig);

        $config = explode("\n",$s1v2raypanel_2);
        $c = $config[0];

        $class = str_replace("$c\n",null,$s1v2raypanel_2);
        file_put_contents("data/servers/s1v2ray/s1panel_2.txt",$class);
	    $id_config = ($c);


        $id = rand(11111,999999999);


        if (!empty($id_config[1])){
            $link = file_get_contents($id_config[1]);
            $configs = base64_decode($link);
            preg_match_all("/(.*?)\n(.*?)/",$configs,$configs);
            for($i=0;$i<=count($configs[0])-1;$i++){
                bot('sendphoto',[
                    'chat_id'=>$chat_id,
                    'photo'=>"https://public-api.qr-code-generator.com/v1/create/free?image_format=PNG&image_width=300&download=1&foreground_color=%23000000&frame_color=%23000000&frame_name=no-frame&qr_code_logo=&qr_code_pattern=&qr_code_text=".urlencode($configs[0][$i]),
                    'caption'=>$configs[0][$i]
                ]);
            }
        }else{
            bot('sendphoto',[
                'chat_id'=>$chat_id,
                'photo'=>"https://public-api.qr-code-generator.com/v1/create/free?image_format=PNG&image_width=300&download=1&foreground_color=%23000000&frame_color=%23000000&frame_name=no-frame&qr_code_logo=&qr_code_pattern=&qr_code_text=".urlencode($v2rayConfig),
                'caption'=>$v2rayConfig
            ]);
        }
       bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
لطفا منو و یا کانال راهنما را مطالعه نمائید
https://t.me/learningv2ray
لطفا یوزرنیم خودرا جهت پیگیری مشکلات و یا شارژ مجدد نزد خود نگه دارید :
 <pre>$c</pre>
            ",'parse_mode'=>"html"
            ,'reply_markup'=>$start
        ]);
 bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
مانده اعتبار | $remaincoin
   ",'parse_mode'=>"html"
            ,'reply_markup'=>$start
        ]);

        bot('sendmessage',[
            'chat_id'=>$Dev,
            'text'=>"
کانفیگ خریداری شد
شناسه خریدار : $from_id
نام کاربری خریدار : @$username
ایدی کانفیگ : <pre>$c</pre>
        ",'parse_mode'=>"html"
        ]);
        $logfile2 = fopen("data/$from_id/logs.txt", "a") or die("Unable to open file!");
        fwrite($logfile2,"$date,$time,$s1v2raymoney_panel2,$c\n");
        fclose($logfile2);
       	$adminlog = fopen("data/userslogs.txt", "a") or die("Unable to open file!");
		fwrite($adminlog,"$from_id,$date,$time,$s1v2raymoney_panel2,$c\n");
        fclose($adminlog);
	exit();
    }

}

elseif ($text == "🛍| 30 گیگ"){

    if ( $coin <= $s1v2raymoney_panel3-1 ) {

        bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
موجودی کافی نیست.
قیمت کانفیگ : $s1v2raymoney_panel3
            "
            ,'reply_markup'=>$s1v2ray
        ]);
        exit();

    }else{

        file_put_contents("data/$from_id/coin.txt",$coin - $s1v2raymoney_panel3);
        file_put_contents("data/$from_id/buys.txt",$buys + 1);
	$remaincoin=file_get_contents("data/$from_id/coin.txt");

        $v2rayConfig = $v2Class2->createUser(30,30,'vmess','ws');
        setPanelContent("data/servers/s1v2ray/s1panel_3.txt",$v2rayConfig);

        $config = explode("\n",$s1v2raypanel_3);
        $c = $config[0];

        $class = str_replace("$c\n",null,$s1v2raypanel_3);
        file_put_contents("data/servers/s1v2ray/s1panel_3.txt",$class);
	    $id_config = ($c);


        $id = rand(11111,999999999);

        if (!empty($id_config[1])){
            $link = file_get_contents($id_config[1]);
            $configs = base64_decode($link);
            preg_match_all("/(.*?)\n(.*?)/",$configs,$configs);
            for($i=0;$i<=count($configs[0])-1;$i++){
                bot('sendphoto',[
                    'chat_id'=>$chat_id,
                    'photo'=>"https://public-api.qr-code-generator.com/v1/create/free?image_format=PNG&image_width=300&download=1&foreground_color=%23000000&frame_color=%23000000&frame_name=no-frame&qr_code_logo=&qr_code_pattern=&qr_code_text=".urlencode($configs[0][$i]),
                    'caption'=>$configs[0][$i]
                ]);
            }
        }else{
            bot('sendphoto',[
                'chat_id'=>$chat_id,
                'photo'=>"https://public-api.qr-code-generator.com/v1/create/free?image_format=PNG&image_width=300&download=1&foreground_color=%23000000&frame_color=%23000000&frame_name=no-frame&qr_code_logo=&qr_code_pattern=&qr_code_text=".urlencode($v2rayConfig),
                'caption'=>$v2rayConfig
            ]);
        }
       bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
لطفا منو و یا کانال راهنما را مطالعه نمائید
https://t.me/learningv2ray

 <pre>$c</pre>
            ",'parse_mode'=>"html"
            ,'reply_markup'=>$start
        ]);
 bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
مانده اعتبار | $remaincoin
   ",'parse_mode'=>"html"
            ,'reply_markup'=>$start
        ]);

        bot('sendmessage',[
            'chat_id'=>$Dev,
            'text'=>"
کانفیگ خریداری شد
شناسه خریدار : $from_id
نام کاربری خریدار : @$username
ایدی کانفیگ : <pre>$c</pre>
        ",'parse_mode'=>"html"
        ]);
        $logfile2 = fopen("data/$from_id/logs.txt", "a") or die("Unable to open file!");
        fwrite($logfile2,"$date,$time,$s1v2raymoney_panel3,$c\n");
        fclose($logfile2);
       	$adminlog = fopen("data/userslogs.txt", "a") or die("Unable to open file!");
		fwrite($adminlog,"$from_id,$date,$time,$s1v2raymoney_panel3,$c\n");
        fclose($adminlog);
	exit();
    }

}
elseif ($text == "🛍| 40 گیگ"){

    if ( $coin <= $s1v2raymoney_panel4-1 ) {

        bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
موجودی کافی نیست.
قیمت کانفیگ : $s1v2raymoney_panel4
            "
            ,'reply_markup'=>$s1v2ray
        ]);
        exit();

    }else{

        file_put_contents("data/$from_id/coin.txt",$coin - $s1v2raymoney_panel4);
        file_put_contents("data/$from_id/buys.txt",$buys + 1);
	$remaincoin=file_get_contents("data/$from_id/coin.txt");

        $v2rayConfig = $v2Class2->createUser(40,30,'vmess','ws');
        setPanelContent("data/servers/s1v2ray/s1panel_4.txt",$v2rayConfig);

        $config = explode("\n",$s1v2raypanel_4);
        $c = $config[0];

        $class = str_replace("$c\n",null,$s1v2raypanel_4);
        file_put_contents("data/servers/s1v2ray/s1panel_4.txt",$class);
	    $id_config = ($c);

        $id = rand(11111,999999999);

        if (!empty($id_config[1])){
            $link = file_get_contents($id_config[1]);
            $configs = base64_decode($link);
            preg_match_all("/(.*?)\n(.*?)/",$configs,$configs);
            for($i=0;$i<=count($configs[0])-1;$i++){
                bot('sendphoto',[
                    'chat_id'=>$chat_id,
                    'photo'=>"https://public-api.qr-code-generator.com/v1/create/free?image_format=PNG&image_width=300&download=1&foreground_color=%23000000&frame_color=%23000000&frame_name=no-frame&qr_code_logo=&qr_code_pattern=&qr_code_text=".urlencode($configs[0][$i]),
                    'caption'=>$configs[0][$i]
                ]);
            }
        }else{
            bot('sendphoto',[
                'chat_id'=>$chat_id,
                'photo'=>"https://public-api.qr-code-generator.com/v1/create/free?image_format=PNG&image_width=300&download=1&foreground_color=%23000000&frame_color=%23000000&frame_name=no-frame&qr_code_logo=&qr_code_pattern=&qr_code_text=".urlencode($v2rayConfig),
                'caption'=>$v2rayConfig
            ]);
        }
       bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
لطفا منو و یا کانال راهنما را مطالعه نمائید
https://t.me/learningv2ray

 <pre>$c</pre>
            ",'parse_mode'=>"html"
            ,'reply_markup'=>$start
        ]);
 bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
مانده اعتبار | $remaincoin
   ",'parse_mode'=>"html"
            ,'reply_markup'=>$start
        ]);

        bot('sendmessage',[
            'chat_id'=>$Dev,
            'text'=>"
کانفیگ خریداری شد
شناسه خریدار : $from_id
نام کاربری خریدار : @$username
ایدی کانفیگ : <pre>$c</pre>
        ",'parse_mode'=>"html"
        ]);
        $logfile2 = fopen("data/$from_id/logs.txt", "a") or die("Unable to open file!");
        fwrite($logfile2,"$date,$time,$s1v2raymoney_panel4,$c\n");
        fclose($logfile2);
       	$adminlog = fopen("data/userslogs.txt", "a") or die("Unable to open file!");
	fwrite($adminlog,"$from_id,$date,$time,$s1v2raymoney_panel4,$c\n");
        fclose($adminlog);
	exit();
    }

}


elseif ($text == "🛍| 60 گیگ"){
    if ( $coin <= $s1v2raymoney_panel5-1 ) {

        bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
موجودی کافی نیست.
قیمت کانفیگ : $s1v2raymoney_panel5
            "
            ,'reply_markup'=>$s1v2ray
        ]);
        exit();

    }else{

        file_put_contents("data/$from_id/coin.txt",$coin - $s1v2raymoney_panel5);
        file_put_contents("data/$from_id/buys.txt",$buys + 1);
	    $remaincoin=file_get_contents("data/$from_id/coin.txt");

        $v2rayConfig = $v2Class2->createUser(60,30,'vmess','ws');
        setPanelContent("data/servers/s1v2ray/s1panel_5.txt",$v2rayConfig);

        $config = explode("\n",$s1v2raypanel_5);
        $c = $config[0];

        $class = str_replace("$c\n",null,$s1v2raypanel_5);
        file_put_contents("data/servers/s1v2ray/s1panel_5.txt",$class);
	    $id_config = ($c);


        $id = rand(11111,999999999);

        if (!empty($id_config[1])){
            $link = file_get_contents($id_config[1]);
            $configs = base64_decode($link);
            preg_match_all("/(.*?)\n(.*?)/",$configs,$configs);
            for($i=0;$i<=count($configs[0])-1;$i++){
                bot('sendphoto',[
                    'chat_id'=>$chat_id,
                    'photo'=>"https://public-api.qr-code-generator.com/v1/create/free?image_format=PNG&image_width=300&download=1&foreground_color=%23000000&frame_color=%23000000&frame_name=no-frame&qr_code_logo=&qr_code_pattern=&qr_code_text=".urlencode($configs[0][$i]),
                    'caption'=>$configs[0][$i]
                ]);
            }
        }else{
            bot('sendphoto',[
                'chat_id'=>$chat_id,
                'photo'=>"https://public-api.qr-code-generator.com/v1/create/free?image_format=PNG&image_width=300&download=1&foreground_color=%23000000&frame_color=%23000000&frame_name=no-frame&qr_code_logo=&qr_code_pattern=&qr_code_text=".urlencode($v2rayConfig),
                'caption'=>$v2rayConfig
            ]);
        }

       bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
لطفا منو و یا کانال راهنما را مطالعه نمائید
https://t.me/learningv2ray

 <pre>$c</pre>
            ",'parse_mode'=>"html"
            ,'reply_markup'=>$start
        ]);
 bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
مانده اعتبار | $remaincoin
   ",'parse_mode'=>"html"
            ,'reply_markup'=>$start
        ]);

        bot('sendmessage',[
            'chat_id'=>$Dev,
            'text'=>"
کانفیگ خریداری شد
شناسه خریدار : $from_id
نام کاربری خریدار : @$username
ایدی کانفیگ : <pre>$c</pre>
        ",'parse_mode'=>"html"
        ]);
        $logfile2 = fopen("data/$from_id/logs.txt", "a") or die("Unable to open file!");
        fwrite($logfile2,"$date,$time,$s1v2raymoney_panel5,$c\n");
        fclose($logfile2);
       	$adminlog = fopen("data/userslogs.txt", "a") or die("Unable to open file!");
		fwrite($adminlog,"$from_id,$date,$time,$s1v2raymoney_panel5,$c\n");
        fclose($adminlog);
	exit();
    }

}

elseif ($text == "🛍| 70 گیگ"){

    if ( $coin <= $s1v2raymoney_panel6-1 ) {

        bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
موجودی کافی نیست.
قیمت کانفیگ : $s1v2raymoney_panel6
            "
            ,'reply_markup'=>$s1v2ray
        ]);
        exit();

    }else{

        file_put_contents("data/$from_id/coin.txt",$coin - $s1v2raymoney_panel6);
        file_put_contents("data/$from_id/buys.txt",$buys + 1);
	    $remaincoin=file_get_contents("data/$from_id/coin.txt");

        $v2rayConfig = $v2Class2->createUser(70,30,'vmess','ws');
        setPanelContent("data/servers/s1v2ray/s1panel_6.txt",$v2rayConfig);

        $config = explode("\n",$s1v2raypanel_6);
        $c = $config[0];

        $class = str_replace("$c\n",null,$s1v2raypanel_6);
        file_put_contents("data/servers/s1v2ray/s1panel_6.txt",$class);
	    $id_config = ($c);

        $id = rand(11111,999999999);

        if (!empty($id_config[1])){
            $link = file_get_contents($id_config[1]);
            $configs = base64_decode($link);
            preg_match_all("/(.*?)\n(.*?)/",$configs,$configs);
            for($i=0;$i<=count($configs[0])-1;$i++){
                bot('sendphoto',[
                    'chat_id'=>$chat_id,
                    'photo'=>"https://public-api.qr-code-generator.com/v1/create/free?image_format=PNG&image_width=300&download=1&foreground_color=%23000000&frame_color=%23000000&frame_name=no-frame&qr_code_logo=&qr_code_pattern=&qr_code_text=".urlencode($configs[0][$i]),
                    'caption'=>$configs[0][$i]
                ]);
            }
        }else{
            bot('sendphoto',[
                'chat_id'=>$chat_id,
                'photo'=>"https://public-api.qr-code-generator.com/v1/create/free?image_format=PNG&image_width=300&download=1&foreground_color=%23000000&frame_color=%23000000&frame_name=no-frame&qr_code_logo=&qr_code_pattern=&qr_code_text=".urlencode($v2rayConfig),
                'caption'=>$v2rayConfig
            ]);
        }
       bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
لطفا منو و یا کانال راهنما را مطالعه نمائید
https://t.me/learningv2ray

 <pre>$c</pre>
            ",'parse_mode'=>"html"
            ,'reply_markup'=>$start
        ]);
 bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
مانده اعتبار | $remaincoin
   ",'parse_mode'=>"html"
            ,'reply_markup'=>$start
        ]);

        bot('sendmessage',[
            'chat_id'=>$Dev,
            'text'=>"
کانفیگ خریداری شد
شناسه خریدار : $from_id
نام کاربری خریدار : @$username
ایدی کانفیگ : <pre>$c</pre>
        ",'parse_mode'=>"html"
        ]);
        $logfile2 = fopen("data/$from_id/logs.txt", "a") or die("Unable to open file!");
        fwrite($logfile2,"$date,$time,$s1v2raymoney_panel6,$c\n");
        fclose($logfile2);
       	$adminlog = fopen("data/userslogs.txt", "a") or die("Unable to open file!");
	fwrite($adminlog,"$from_id,$date,$time,$s1v2raymoney_panel6,$c\n");
        fclose($adminlog);
	exit();
    }

}

elseif ($text == "🛍| 100 گیگ"){

    if ( $coin <= $s1v2raymoney_panel7-1 ) {

        bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
موجودی کافی نیست.
قیمت کانفیگ : $s1v2raymoney_panel7
            "
            ,'reply_markup'=>$s1v2ray
        ]);
        exit();

    }else{

        file_put_contents("data/$from_id/coin.txt",$coin - $s1v2raymoney_panel7);
        file_put_contents("data/$from_id/buys.txt",$buys + 1);
	    $remaincoin=file_get_contents("data/$from_id/coin.txt");

        $v2rayConfig = $v2Class2->createUser(100,30,'vmess','ws');
        setPanelContent("data/servers/s1v2ray/s1panel_7.txt",$v2rayConfig);

        $config = explode("\n",$s1v2raypanel_7);
        $c = $config[0];

        $class = str_replace("$c\n",null,$s1v2raypanel_7);
        file_put_contents("data/servers/s1v2ray/s1panel_7.txt",$class);
	$id_config = ($c);

        $id = rand(11111,999999999);

        if (!empty($id_config[1])){
            $link = file_get_contents($id_config[1]);
            $configs = base64_decode($link);
            preg_match_all("/(.*?)\n(.*?)/",$configs,$configs);
            for($i=0;$i<=count($configs[0])-1;$i++){
                bot('sendphoto',[
                    'chat_id'=>$chat_id,
                    'photo'=>"https://public-api.qr-code-generator.com/v1/create/free?image_format=PNG&image_width=300&download=1&foreground_color=%23000000&frame_color=%23000000&frame_name=no-frame&qr_code_logo=&qr_code_pattern=&qr_code_text=".urlencode($configs[0][$i]),
                    'caption'=>$configs[0][$i]
                ]);
            }
        }else{
            bot('sendphoto',[
                'chat_id'=>$chat_id,
                'photo'=>"https://public-api.qr-code-generator.com/v1/create/free?image_format=PNG&image_width=300&download=1&foreground_color=%23000000&frame_color=%23000000&frame_name=no-frame&qr_code_logo=&qr_code_pattern=&qr_code_text=".urlencode($v2rayConfig),
                'caption'=>$v2rayConfig
            ]);
        }

       bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
لطفا منو و یا کانال راهنما را مطالعه نمائید
https://t.me/learningv2ray

 <pre>$c</pre>
            ",'parse_mode'=>"html"
            ,'reply_markup'=>$start
        ]);
 bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
مانده اعتبار | $remaincoin
   ",'parse_mode'=>"html"
            ,'reply_markup'=>$start
        ]);

        bot('sendmessage',[
            'chat_id'=>$Dev,
            'text'=>"
کانفیگ خریداری شد
شناسه خریدار : $from_id
نام کاربری خریدار : @$username
ایدی کانفیگ : <pre>$c</pre>
        ",'parse_mode'=>"html"
        ]);
        $logfile2 = fopen("data/$from_id/logs.txt", "a") or die("Unable to open file!");
        fwrite($logfile2,"$date,$time,$s1v2raymoney_panel7,$c\n");
        fclose($logfile2);
       	$adminlog = fopen("data/userslogs.txt", "a") or die("Unable to open file!");
	fwrite($adminlog,"$from_id,$date,$time,$s1v2raymoney_panel7,$c\n");
        fclose($adminlog);
	exit();
    }

}

elseif ($text == "🛍| نامحدود"){

    if ( $coin <= $s1v2raymoney_panel8-1 ) {

        bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
موجودی کافی نیست.
قیمت کانفیگ : $s1v2raymoney_panel8
            "
            ,'reply_markup'=>$s1v2ray
        ]);
        exit();

    }else{

        file_put_contents("data/$from_id/coin.txt",$coin - $s1v2raymoney_panel8);
        file_put_contents("data/$from_id/buys.txt",$buys + 1);
	$remaincoin=file_get_contents("data/$from_id/coin.txt");

        $v2rayConfig = $v2Class2->createUser(250,30,'vmess','ws');
        setPanelContent("data/servers/s1v2ray/s1panel_8.txt",$v2rayConfig);

        $config = explode("\n",$s1v2raypanel_8);
        $c = $config[0];

        $class = str_replace("$c\n",null,$s1v2raypanel_8);
        file_put_contents("data/servers/s1v2ray/s1panel_8.txt",$class);
	    $id_config = ($c);

        $id = rand(11111,999999999);

        if (!empty($id_config[1])){
            $link = file_get_contents($id_config[1]);
            $configs = base64_decode($link);
            preg_match_all("/(.*?)\n(.*?)/",$configs,$configs);
            for($i=0;$i<=count($configs[0])-1;$i++){
                bot('sendphoto',[
                    'chat_id'=>$chat_id,
                    'photo'=>"https://public-api.qr-code-generator.com/v1/create/free?image_format=PNG&image_width=300&download=1&foreground_color=%23000000&frame_color=%23000000&frame_name=no-frame&qr_code_logo=&qr_code_pattern=&qr_code_text=".urlencode($configs[0][$i]),
                    'caption'=>$configs[0][$i]
                ]);
            }
        }else{
            bot('sendphoto',[
                'chat_id'=>$chat_id,
                'photo'=>"https://public-api.qr-code-generator.com/v1/create/free?image_format=PNG&image_width=300&download=1&foreground_color=%23000000&frame_color=%23000000&frame_name=no-frame&qr_code_logo=&qr_code_pattern=&qr_code_text=".urlencode($v2rayConfig),
                'caption'=>$v2rayConfig
            ]);
        }
       bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
لطفا منو و یا کانال راهنما را مطالعه نمائید
https://t.me/learningv2ray

 <pre>$c</pre>
            ",'parse_mode'=>"html"
            ,'reply_markup'=>$start
        ]);
 bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
مانده اعتبار | $remaincoin
   ",'parse_mode'=>"html"
            ,'reply_markup'=>$start
        ]);

        bot('sendmessage',[
            'chat_id'=>$Dev,
            'text'=>"
کانفیگ خریداری شد
شناسه خریدار : $from_id
یوزرنیم خریدار : @$username
ایدی کانفیگ : <pre>$c</pre>
        ",'parse_mode'=>"html"
        ]);
        $logfile2 = fopen("data/$from_id/logs.txt", "a") or die("Unable to open file!");
        fwrite($logfile2,"$date,$time,$s1v2raymoney_panel8,$c\n");
        fclose($logfile2);
       	$adminlog = fopen("data/userslogs.txt", "a") or die("Unable to open file!");
	fwrite($adminlog,"$from_id,$date,$time,$s1v2raymoney_panel8,$c\n");
        fclose($adminlog);
	exit();
    }

}

//open vpn
elseif ($text == "🔐 | OPEN VPN - Sstp - l2tp - pptp"){
    bot('sendmessage',[
        'chat_id'=>$chat_id,
        'text'=>"
$openbanner           "
        ,'reply_markup'=>$openvpn
    ]);
}

elseif ($text == "🛍 |10 گیگ"){

    if ( $openpanel_1 == null or $openpanel_1 == "" or $openpanel_1 == " " or $openpanel_1 == "\n"){
        bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
کانفیگ موجود نیست.
            "
            ,'reply_markup'=>$openvpn
        ]);
        exit();
    }

    if ( $coin <= $openmoney_panel1-1 ) {

        bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
موجودی کافی نیست لطفا از قسمت افزایش موجودی اقدام کنید
قیمت کانفیگ $openmoney_panel1 هزار تومان
            "
            ,'reply_markup'=>$openvpn
        ]);
        exit();

    }else{

        file_put_contents("data/$from_id/coin.txt",$coin - $openmoney_panel1);
        file_put_contents("data/$from_id/buys.txt",$buys + 1);
	$remaincoin=file_get_contents("data/$from_id/coin.txt");

        $config = explode("\n",$openpanel_1);
        $c = $config[0];
        $class = str_replace("$c\n",null,$openpanel_1);
        file_put_contents("data/servers/openvpn/openpanel_1.txt",$class);
        $id_config = explode("=",$c);
        $user = $id_config[0];
	$pass = $id_config[1];

/*
        $id_config = ($c);

        $id = $id_config;

        $link = file_get_contents($id_config);

$configs = base64_decode($c);
        preg_match_all("/(.*?)\n(.*?)/",$configs,$configs);

       {
            bot('sendphoto',[
                'chat_id'=>$chat_id,
                'photo'=>"https://public-api.qr-code-generator.com/v1/create/free?image_format=PNG&image_width=300&download=1&foreground_color=%23000000&frame_color=%23000000&frame_name=no-frame&qr_code_logo=&qr_code_pattern=&qr_code_text=".urlencode($c),
                'caption'=>$c
            ]);
        }
*/
        bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
لطفا منو و یا کانال راهنما را مطالعه نمائید
https://t.me/learningv2ray
با یوزر نیم  و پسورد خود میتوانید 
 از سایت ذیل جهت ریز مصرف  گزارش گیری نمائید
http://qr2.voaxn1.xyz:2985/um/user/
لطفا یوزرنیم خودرا جهت پیگیری مشکلات و یا شارژ مجدد نزد خود نگه دارید
نام کاربری : <pre>$user</pre>
کلمه عبور: <pre>$pass</pre>
            ",'parse_mode'=>"html"
            ,'reply_markup'=>$start
        ]);
 bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
مانده اعتبار | $remaincoin
   ",'parse_mode'=>"html"
            ,'reply_markup'=>$start
        ]);

        bot('sendmessage',[
            'chat_id'=>$Dev,
            'text'=>"
کانفیگ خریداری شد
عددی خریدار : $from_id
یوزرنیم خریدار : @$username
ایدی کانفیگ : <pre>$c</pre>
            ",'parse_mode'=>"html"
        ]);
        $logfile2 = fopen("data/$from_id/logs.txt", "a") or die("Unable to open file!");
        fwrite($logfile2,"$date,$time,$openmoney_panel1,$c\n");
        fclose($logfile2);
       	$adminlog = fopen("data/userslogs.txt", "a") or die("Unable to open file!");
	fwrite($adminlog,"$from_id,$date,$time,$openmoney_panel1,$c\n");
        fclose($adminlog);
	exit();
    }

}
elseif ($text == "🛍 |20 گیگ"){

    if ( $openpanel_2 == null or $openpanel_2 == "" or $openpanel_2 == " " or $openpanel_2 == "\n"){
        bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
کانفیگ موجود نیست.
            "
            ,'reply_markup'=>$openvpn
        ]);
        exit();
    }

    if ( $coin <= $openmoney_panel2-1 ) {

        bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
موجودی کافی نیست.
قیمت کانفیگ : $openmoney_panel2
            "
            ,'reply_markup'=>$openvpn
        ]);
        exit();

    }else{

        file_put_contents("data/$from_id/coin.txt",$coin - $openmoney_panel2);
        file_put_contents("data/$from_id/buys.txt",$buys + 1);
	$remaincoin=file_get_contents("data/$from_id/coin.txt");

        $config = explode("\n",$openpanel_2);

        $c = $config[0];

        $class = str_replace("$c\n",null,$openpanel_2);
        file_put_contents("data/servers/openvpn/openpanel_2.txt",$class);
        $id_config = explode("=",$c);
        $user = $id_config[0];
	$pass = $id_config[1];

/*
        $id = $id_config;

        $link = file_get_contents($id_config);

$configs = base64_decode($c);
        preg_match_all("/(.*?)\n(.*?)/",$configs,$configs);

       {
            bot('sendphoto',[
                'chat_id'=>$chat_id,
                'photo'=>"https://public-api.qr-code-generator.com/v1/create/free?image_format=PNG&image_width=300&download=1&foreground_color=%23000000&frame_color=%23000000&frame_name=no-frame&qr_code_logo=&qr_code_pattern=&qr_code_text=".urlencode($c),
                'caption'=>$c
            ]);
        }

*/

        bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
لطفا منو و یا کانال راهنما را مطالعه نمائید
https://t.me/learningv2ray
با یوزر نیم  و پسورد خود میتوانید 
 از سایت ذیل جهت ریز مصرف  گزارش گیری نمائید
http://qr2.voaxn1.xyz:2985/um/user/
لطفا یوزرنیم خودرا جهت پیگیری مشکلات و یا شارژ مجدد نزد خود نگه دارید
نام کاربری : <pre>$user</pre>
کلمه عبور: <pre>$pass</pre>
            ",'parse_mode'=>"html"
            ,'reply_markup'=>$start
        ]);
 bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
مانده اعتبار | $remaincoin
   ",'parse_mode'=>"html"
            ,'reply_markup'=>$start
        ]);

        bot('sendmessage',[
            'chat_id'=>$Dev,
            'text'=>"
کانفیگ خریداری شد
عددی خریدار : $from_id
یوزرنیم خریدار : @$username
ایدی کانفیگ : <pre>$c</pre>
            ",'parse_mode'=>"html"
        ]);
        $logfile2 = fopen("data/$from_id/logs.txt", "a") or die("Unable to open file!");
        fwrite($logfile2,"$date,$time,$openmoney_panel2,$c\n");
        fclose($logfile2);
       	$adminlog = fopen("data/userslogs.txt", "a") or die("Unable to open file!");
	fwrite($adminlog,"$from_id,$date,$time,$openmoney_panel2,$c\n");
        fclose($adminlog);
	exit();

    }

}
elseif ($text == "🛍 |30 گیگ"){

    if ( $openpanel_3 == null or $openpanel_3 == "" or $openpanel_3 == " " or $openpanel_3 == "\n"){
        bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
کانفیگ موجود نیست.
            "
            ,'reply_markup'=>$openvpn
        ]);
        exit();
    }

    if ( $coin <= $openmoney_panel3-1 ) {

        bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
موجودی کافی نیست.
قیمت کانفیگ : $openmoney_panel3
            "
            ,'reply_markup'=>$openvpn
        ]);
        exit();

    }else{

        file_put_contents("data/$from_id/coin.txt",$coin - $openmoney_panel3);
        file_put_contents("data/$from_id/buys.txt",$buys + 1);
	$remaincoin=file_get_contents("data/$from_id/coin.txt");

        $config = explode("\n",$openpanel_3);

        $c = $config[0];

        $class = str_replace("$c\n",null,$openpanel_3);
        file_put_contents("data/servers/openvpn/openpanel_3.txt",$class);
        $id_config = explode("=",$c);
        $user = $id_config[0];
	$pass = $id_config[1];

/*
        $id_config = ($c);

        $id = $id_config;

        $link = file_get_contents($id_config);

$configs = base64_decode($c);
        preg_match_all("/(.*?)\n(.*?)/",$configs,$configs);

       {
            bot('sendphoto',[
                'chat_id'=>$chat_id,
                'photo'=>"https://public-api.qr-code-generator.com/v1/create/free?image_format=PNG&image_width=300&download=1&foreground_color=%23000000&frame_color=%23000000&frame_name=no-frame&qr_code_logo=&qr_code_pattern=&qr_code_text=".urlencode($c),
                'caption'=>$c
            ]);
        }

*/
        bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
لطفا منو و یا کانال راهنما را مطالعه نمائید
https://t.me/learningv2ray
با یوزر نیم  و پسورد خود میتوانید 
 از سایت ذیل جهت ریز مصرف  گزارش گیری نمائید
http://qr2.voaxn1.xyz:2985/um/user/
لطفا یوزرنیم خودرا جهت پیگیری مشکلات و یا شارژ مجدد نزد خود نگه دارید
نام کاربری : <pre>$user</pre>
کلمه عبور: <pre>$pass</pre>
            ",'parse_mode'=>"html"
            ,'reply_markup'=>$start
        ]);
 bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
مانده اعتبار | $remaincoin
   ",'parse_mode'=>"html"
            ,'reply_markup'=>$start
        ]);

        bot('sendmessage',[
            'chat_id'=>$Dev,
            'text'=>"
کانفیگ خریداری شد
عددی خریدار : $from_id
یوزرنیم خریدار : @$username
ایدی کانفیگ : <pre>$c</pre>
            ",'parse_mode'=>"html"
        ]);
        $logfile2 = fopen("data/$from_id/logs.txt", "a") or die("Unable to open file!");
        fwrite($logfile2,"$date,$time,$openmoney_panel3,$c\n");
        fclose($logfile2);
       	$adminlog = fopen("data/userslogs.txt", "a") or die("Unable to open file!");
	fwrite($adminlog,"$from_id,$date,$time,$openmoney_panel3,$c\n");
        fclose($adminlog);
	exit();

    }

}

elseif ($text == "🛍 |40 گیگ"){

    if ( $openpanel_4 == null or $openpanel_4 == "" or $openpanel_4 == " " or $openpanel_4 == "\n"){
        bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
کانفیگ موجود نیست.
            "
            ,'reply_markup'=>$openvpn
        ]);
        exit();
    }

    if ( $coin <= $openmoney_panel4-1 ) {

        bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
موجودی کافی نیست.
قیمت کانفیگ : $openmoney_panel4
            "
            ,'reply_markup'=>$openvpn
        ]);
        exit();

    }else{

        file_put_contents("data/$from_id/coin.txt",$coin - $openmoney_panel4);
        file_put_contents("data/$from_id/buys.txt",$buys + 1);
	$remaincoin=file_get_contents("data/$from_id/coin.txt");

        $config = explode("\n",$openpanel_4);

        $c = $config[0];

        $class = str_replace("$c\n",null,$openpanel_4);
        file_put_contents("data/servers/openvpn/openpanel_4.txt",$class);

        $id_config = explode("=",$c);
        $user = $id_config[0];
	$pass = $id_config[1];

/*
        $id = $id_config;

        $link = file_get_contents($id_config);

$configs = base64_decode($c);
        preg_match_all("/(.*?)\n(.*?)/",$configs,$configs);

       {
            bot('sendphoto',[
                'chat_id'=>$chat_id,
                'photo'=>"https://public-api.qr-code-generator.com/v1/create/free?image_format=PNG&image_width=300&download=1&foreground_color=%23000000&frame_color=%23000000&frame_name=no-frame&qr_code_logo=&qr_code_pattern=&qr_code_text=".urlencode($c),
                'caption'=>$c
            ]);
        }

*/
        bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
لطفا منو و یا کانال راهنما را مطالعه نمائید
https://t.me/learningv2ray
با یوزر نیم  و پسورد خود میتوانید 
 از سایت ذیل جهت ریز مصرف  گزارش گیری نمائید
http://qr2.voaxn1.xyz:2985/um/user/
لطفا یوزرنیم خودرا جهت پیگیری مشکلات و یا شارژ مجدد نزد خود نگه دارید
نام کاربری : <pre>$user</pre>
کلمه عبور: <pre>$pass</pre>
            ",'parse_mode'=>"html"
            ,'reply_markup'=>$start
        ]);
 bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
مانده اعتبار | $remaincoin
   ",'parse_mode'=>"html"
            ,'reply_markup'=>$start
        ]);

        bot('sendmessage',[
            'chat_id'=>$Dev,
            'text'=>"
کانفیگ خریداری شد
عددی خریدار : $from_id
یوزرنیم خریدار : @$username
ایدی کانفیگ : <pre>$c</pre>
            ",'parse_mode'=>"html"
        ]);
        $logfile2 = fopen("data/$from_id/logs.txt", "a") or die("Unable to open file!");
        fwrite($logfile2,"$date,$time,$openmoney_panel4,$c\n");
        fclose($logfile2);
       	$adminlog = fopen("data/userslogs.txt", "a") or die("Unable to open file!");
	fwrite($adminlog,"$from_id,$date,$time,$openmoney_panel4,$c\n");
        fclose($adminlog);
	exit();

    }

}


elseif ($text == "🛍 |60 گیگ"){

    if ( $openpanel_5 == null or $openpanel_5 == "" or $openpanel_5 == " " or $openpanel_5 == "\n"){
        bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
کانفیگ موجود نیست.
            "
            ,'reply_markup'=>$openvpn
        ]);
        exit();
    }

    if ( $coin <= $openmoney_panel5-1 ) {

        bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
موجودی کافی نیست.
قیمت کانفیگ : $openmoney_panel5
            "
            ,'reply_markup'=>$openvpn
        ]);
        exit();

    }else{

        file_put_contents("data/$from_id/coin.txt",$coin - $openmoney_panel5);
        file_put_contents("data/$from_id/buys.txt",$buys + 1);
	$remaincoin=file_get_contents("data/$from_id/coin.txt");

        $config = explode("\n",$openpanel_5);

        $c = $config[0];

        $class = str_replace("$c\n",null,$openpanel_5);
        file_put_contents("data/servers/openvpn/openpanel_5.txt",$class);
        $id_config = explode("=",$c);
        $user = $id_config[0];
	$pass = $id_config[1];
/*
        $id = $id_config;

        $link = file_get_contents($id_config);

$configs = base64_decode($c);
        preg_match_all("/(.*?)\n(.*?)/",$configs,$configs);

       {
            bot('sendphoto',[
                'chat_id'=>$chat_id,
                'photo'=>"https://public-api.qr-code-generator.com/v1/create/free?image_format=PNG&image_width=300&download=1&foreground_color=%23000000&frame_color=%23000000&frame_name=no-frame&qr_code_logo=&qr_code_pattern=&qr_code_text=".urlencode($c),
                'caption'=>$c
            ]);
        }
*/

        bot('sendmessage',[
            'chat_id'=>$chat_id,
             'text'=>"
لطفا منو و یا کانال راهنما را مطالعه نمائید
https://t.me/learningv2ray
با یوزر نیم  و پسورد خود میتوانید 
 از سایت ذیل جهت ریز مصرف  گزارش گیری نمائید
http://qr2.voaxn1.xyz:2985/um/user/
لطفا یوزرنیم خودرا جهت پیگیری مشکلات و یا شارژ مجدد نزد خود نگه دارید
نام کاربری : <pre>$user</pre>
کلمه عبور: <pre>$pass</pre>
            ",'parse_mode'=>"html"
            ,'reply_markup'=>$start
        ]);
 bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
مانده اعتبار | $remaincoin
   ",'parse_mode'=>"html"
            ,'reply_markup'=>$start
        ]);

        bot('sendmessage',[
            'chat_id'=>$Dev,
            'text'=>"
کانفیگ خریداری شد
عددی خریدار : $from_id
یوزرنیم خریدار : @$username
ایدی کانفیگ : <pre>$c</pre>
            ",'parse_mode'=>"html"
        ]);
        $logfile2 = fopen("data/$from_id/logs.txt", "a") or die("Unable to open file!");
        fwrite($logfile2,"$date,$time,$openmoney_panel5,$c\n");
        fclose($logfile2);
       	$adminlog = fopen("data/userslogs.txt", "a") or die("Unable to open file!");
	fwrite($adminlog,"$from_id,$date,$time,$openmoney_panel5,$c\n");
        fclose($adminlog);
	exit();

    }

}

elseif ($text == "🛍 |70 گیگ"){

    if ( $openpanel_6 == null or $openpanel_6 == "" or $openpanel_6 == " " or $openpanel_6 == "\n"){
        bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
کانفیگ موجود نیست.
            "
            ,'reply_markup'=>$openvpn
        ]);
        exit();
    }

    if ( $coin <= $openmoney_panel6-1 ) {

        bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
موجودی کافی نیست.
قیمت کانفیگ : $openmoney_panel6
            "
            ,'reply_markup'=>$openvpn
        ]);
        exit();

    }else{

        file_put_contents("data/$from_id/coin.txt",$coin - $openmoney_panel6);
        file_put_contents("data/$from_id/buys.txt",$buys + 1);
	$remaincoin=file_get_contents("data/$from_id/coin.txt");

        $config = explode("\n",$openpanel_6);

        $c = $config[0];

        $class = str_replace("$c\n",null,$openpanel_6);
        file_put_contents("data/servers/openvpn/openpanel_6.txt",$class);
        $id_config = explode("=",$c);
        $user = $id_config[0];
	$pass = $id_config[1];
/*
        $id = $id_config;

        $link = file_get_contents($id_config);

$configs = base64_decode($c);
        preg_match_all("/(.*?)\n(.*?)/",$configs,$configs);

       {
            bot('sendphoto',[
                'chat_id'=>$chat_id,
                'photo'=>"https://public-api.qr-code-generator.com/v1/create/free?image_format=PNG&image_width=300&download=1&foreground_color=%23000000&frame_color=%23000000&frame_name=no-frame&qr_code_logo=&qr_code_pattern=&qr_code_text=".urlencode($c),
                'caption'=>$c
            ]);
        }
*/

        bot('sendmessage',[
            'chat_id'=>$chat_id,
             'text'=>"
لطفا منو و یا کانال راهنما را مطالعه نمائید
https://t.me/learningv2ray
با یوزر نیم  و پسورد خود میتوانید 
 از سایت ذیل جهت ریز مصرف  گزارش گیری نمائید
http://qr2.voaxn1.xyz:2985/um/user/
لطفا یوزرنیم خودرا جهت پیگیری مشکلات و یا شارژ مجدد نزد خود نگه دارید
نام کاربری : <pre>$user</pre>
کلمه عبور: <pre>$pass</pre>
            ",'parse_mode'=>"html"
            ,'reply_markup'=>$start
        ]);
 bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
مانده اعتبار | $remaincoin
   ",'parse_mode'=>"html"
            ,'reply_markup'=>$start
        ]);

        bot('sendmessage',[
            'chat_id'=>$Dev,
            'text'=>"
کانفیگ خریداری شد
عددی خریدار : $from_id
یوزرنیم خریدار : @$username
ایدی کانفیگ : <pre>$c</pre>
            ",'parse_mode'=>"html"
        ]);
        $logfile2 = fopen("data/$from_id/logs.txt", "a") or die("Unable to open file!");
        fwrite($logfile2,"$date,$time,$openmoney_panel6,$c\n");
        fclose($logfile2);
       	$adminlog = fopen("data/userslogs.txt", "a") or die("Unable to open file!");
	fwrite($adminlog,"$from_id,$date,$time,$openmoney_panel6,$c\n");
        fclose($adminlog);
	exit();

    }

}

elseif ($text == "🛍 |100 گیگ"){

    if ( $openpanel_7 == null or $openpanel_7 == "" or $openpanel_7 == " " or $openpanel_7 == "\n"){
        bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
کانفیگ موجود نیست.
            "
            ,'reply_markup'=>$openvpn
        ]);
        exit();
    }

    if ( $coin <= $openmoney_panel7-1 ) {

        bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
موجودی کافی نیست.
قیمت کانفیگ : $openmoney_panel7
            "
            ,'reply_markup'=>$openvpn
        ]);
        exit();

    }else{

        file_put_contents("data/$from_id/coin.txt",$coin - $openmoney_panel7);
        file_put_contents("data/$from_id/buys.txt",$buys + 1);
	$remaincoin=file_get_contents("data/$from_id/coin.txt");

        $config = explode("\n",$openpanel_7);

        $c = $config[0];

        $class = str_replace("$c\n",null,$openpanel_7);
        file_put_contents("data/servers/openvpn/openpanel_7.txt",$class);
        $id_config = explode("=",$c);
        $user = $id_config[0];
	$pass = $id_config[1];
/*
        $id = $id_config[0];
        $link = file_get_contents($id_config);

$configs = base64_decode($c);
        preg_match_all("/(.*?)\n(.*?)/",$configs,$configs);

       {
            bot('sendphoto',[
                'chat_id'=>$chat_id,
                'photo'=>"https://public-api.qr-code-generator.com/v1/create/free?image_format=PNG&image_width=300&download=1&foreground_color=%23000000&frame_color=%23000000&frame_name=no-frame&qr_code_logo=&qr_code_pattern=&qr_code_text=".urlencode($c),
                'caption'=>$c
            ]);
        }
*/
        bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
لطفا منو و یا کانال راهنما را مطالعه نمائید
https://t.me/learningv2ray
با یوزر نیم  و پسورد خود میتوانید 
 از سایت ذیل جهت ریز مصرف  گزارش گیری نمائید
http://qr2.voaxn1.xyz:2985/um/user/
لطفا یوزرنیم خودرا جهت پیگیری مشکلات و یا شارژ مجدد نزد خود نگه دارید
نام کاربری : <pre>$user</pre>
کلمه عبور: <pre>$pass</pre>
            ",'parse_mode'=>"html"
            ,'reply_markup'=>$openvpn
        ]);
 bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
مانده اعتبار | $remaincoin
   ",'parse_mode'=>"html"
            ,'reply_markup'=>$start
        ]);

        bot('sendmessage',[
            'chat_id'=>$Dev,
            'text'=>"
کانفیگ خریداری شد
عددی خریدار : $from_id
یوزرنیم خریدار : @$username
ایدی کانفیگ : <pre>$c</pre>
            ",'parse_mode'=>"html"
        ]);
        $logfile2 = fopen("data/$from_id/logs.txt", "a") or die("Unable to open file!");
        fwrite($logfile2,"$date,$time,$openmoney_panel7,$c\n");
        fclose($logfile2);
       	$adminlog = fopen("data/userslogs.txt", "a") or die("Unable to open file!");
	fwrite($adminlog,"$from_id,$date,$time,$openmoney_panel7,$c\n");
        fclose($adminlog);
	exit();
    }

}


 elseif ($text == "🛍 |نامحدود"){

    if ( $openpanel_8 == null or $openpanel_8 == "" or $openpanel_8 == " " or $openpanel_8 == "\n"){
        bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
کانفیگ موجود نیست.
            "
            ,'reply_markup'=>$openvpn
        ]);
        exit();
    }

    if ( $coin <= $openmoney_panel8-1 ) {

        bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
موجودی کافی نیست.
قیمت کانفیگ : $openmoney_panel8
            "
            ,'reply_markup'=>$openvpn
        ]);
        exit();

    }else{

        file_put_contents("data/$from_id/coin.txt",$coin - $openmoney_panel8);
        file_put_contents("data/$from_id/buys.txt",$buys + 1);
	$remaincoin=file_get_contents("data/$from_id/coin.txt");

        $config = explode("\n",$openpanel_8);

        $c = $config[0];

        $class = str_replace("$c\n",null,$openpanel_8);
        file_put_contents("data/servers/openvpn/openpanel_8.txt",$class);
        $id_config = explode("=",$c);
        $user = $id_config[0];
	$pass = $id_config[1];
/*
        $id = $id_config[0];
        $link = file_get_contents($id_config);

$configs = base64_decode($c);
        preg_match_all("/(.*?)\n(.*?)/",$configs,$configs);

       {
            bot('sendphoto',[
                'chat_id'=>$chat_id,
                'photo'=>"https://public-api.qr-code-generator.com/v1/create/free?image_format=PNG&image_width=300&download=1&foreground_color=%23000000&frame_color=%23000000&frame_name=no-frame&qr_code_logo=&qr_code_pattern=&qr_code_text=".urlencode($c),
                'caption'=>$c
            ]);
        }
*/
        bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
لطفا منو و یا کانال راهنما را مطالعه نمائید
https://t.me/learningv2ray
با یوزر نیم  و پسورد خود میتوانید 
از سایت ذیل جهت ریز مصرف  گزارش گیری نمائید
http://qr2.voaxn1.xyz:2985/um/user/
لطفا یوزرنیم خودرا جهت پیگیری مشکلات و یا شارژ مجدد نزد خود نگه دارید
نام کاربری : <pre>$user</pre>
کلمه عبور: <pre>$pass</pre>
            ",'parse_mode'=>"html"
            ,'reply_markup'=>$openvpn
        ]);
 bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
مانده اعتبار | $remaincoin
   ",'parse_mode'=>"html"
            ,'reply_markup'=>$start
        ]);

        bot('sendmessage',[
            'chat_id'=>$Dev,
            'text'=>"
کانفیگ خریداری شد
عددی خریدار : $from_id
یوزرنیم خریدار : @$username
ایدی کانفیگ : <pre>$c</pre>
            ",'parse_mode'=>"html"
        ]);
        $logfile2 = fopen("data/$from_id/logs.txt", "a") or die("Unable to open file!");
        fwrite($logfile2,"$date,$time,$openmoney_panel8,$c\n");
        fclose($logfile2);
       	$adminlog = fopen("data/userslogs.txt", "a") or die("Unable to open file!");
	fwrite($adminlog,"$from_id,$date,$time,$openmoney_panel8,$c\n");
        fclose($adminlog);
	exit();
    }

}
elseif ($text == "👥 | اطلاعات حساب"){
    bot('sendmessage',[
        'chat_id'=>$chat_id,
        'text'=>"
اطلاعات حساب شما :

ایدی عددی : $from_id
موجودی : $coin
تعداد خرید : $buys

.
        "
    ]);
}


elseif ($text == "💳 | افزایش موجودی"){
    file_put_contents("data/$from_id/state.txt","buy_coin");
    bot('sendmessage',[
        'chat_id'=>$chat_id,
        'text'=>"
مبلغی که میخواهید شارژ کنید را به شماره کارت زیر انتقال دهید سپس رسید را بفرستید

$card

        ",'reply_markup'=>$back
    ]);
}

elseif ($state == "buy_coin" and $text != "🔙"){
    if ($photo) {
        file_put_contents("data/$from_id/state.txt","none");
        bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
رسید شما به مدیریت ارسال شد تا تایید ان صبور باشید
            ",'reply_markup'=>$start
        ]);

        bot('ForwardMessage',[
            'chat_id'=>$Dev,
            'from_chat_id'=>$from_id,
            'message_id'=>$message_id
            ]);

        bot('sendmessage',[
            'chat_id'=>$Dev,
            'text' =>"
ایدی عددی کاربر : <pre>$from_id</pre>
یوزرنیم کاربر : @$username
        ",'parse_mode'=>"html",
        'reply_markup'=>json_encode([
            'inline_keyboard'=>[
                [['text'=>"لغو" , 'callback_data'=>"cancell_$from_id"],['text'=>"تایید" , 'callback_data'=>"ok_$from_id"]]
            ]
        ])
            ]);

    }else{
        bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
خطا !
شما باید عکس رسید را بفرستید
            ",'reply_markup'=>$back
        ]);
    }

}

elseif ( strpos($data , "cancell_")!==false ){
    if ($chat_id == $Dev){

        bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
کنسل شد
            "
        ]);

        $id = explode('_',$data);

        bot('sendmessage',[
            'chat_id'=>$id[1],
            'text'=>"
رسید شما تایید نشد
            "
        ]);

    }else{
        exit();
    }
}


elseif ( strpos($data , "ok_")!==false ){
    if ($chat_id == $Dev){
        $id = explode('_',$data)[1];

        file_put_contents("data/$from_id/state.txt","id_$id");
        bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
مقدار موجودی رو بفرستید
            ",'reply_markup'=>$back
        ]);

    }else{
        exit();
    }
}

elseif ( strpos($state , "id_")!==false ){

    bot('sendmessage',[
        'chat_id'=>$chat_id,
        'text'=>"
ارسال شد
        ",'reply_markup'=>$panel
    ]);

    $id = explode('_',$state)[1];

    $coin = file_get_contents("data/$id/coin.txt");
    file_put_contents("data/$id/coin.txt",$coin+$text);


    bot('sendmessage',[
        'chat_id'=>$id,
        'text'=>"
رسید شما تایید شد و مبلغ $text به موجودی شما اضافه شد
        "
    ]);
    file_put_contents("data/$from_id/state.txt","none");
        $chargefile2 = fopen("data/$from_id/charge.txt", "a") or die("Unable to open file!");
        fwrite($chargefile2,"$date,$time,user+$text\n");
        fclose($chargefile2);
        exit();


}


elseif ($text == "❗️| راهنما"){
    bot('sendmessage',[
        'chat_id'=>$chat_id,
        'text'=>"
https://t.me/learningv2ray
        "
    ]);
}
elseif ($text == "❗️|OPEN VPN راهنما"){
    bot('sendmessage',[
        'chat_id'=>$chat_id,
        'text'=>"
ابتدا برنامه open vpn را از طریق گوگل پلی یا دانلود مستقیم بدون استفاده از فیلتر شکن از این لینک http://a4sos.ir/ov.apk برنامه رانصب کنید و سپس پروفایل برنامه را از این لینک http://a4sos.ir/wt.ovpn دریافت و بوسیله برنامه open vpn که قبلا نصب کرده اید اجرا کنید و یا وارد برنامه open vpn  شده و وارد قسمت upload file شده گزینه BROWSE  رو انتخاب کنید فایل wt.ovpn  را از فایلهای شخصی جستجو کنید وگزینه ok را انتخاب کنید، قسمت یوزرنیم و گزینه Save Password  را انتخاب کرده پسورد را وارد کنید، گزینه connect  را زده و از اتصال به شبکه جهانی بدون محدودیت لذت ببرید.
آدرس راهنمای کانال 
https://t.me/learningv2ray
 "
    ]);
}
elseif ($text == "دانلود مستقیم برنامه"){
    bot('sendmessage',[
        'chat_id'=>$chat_id,
        'text'=>"
https://t.me/learningv2ray/36
دانلود لینک مستقیم بدون استفاده از فیلتر شکن http://a4sos.ir/ov.apk
        "
    ]);
}

elseif ($text == "دانلود مستقیم پروفایل"){
    bot('sendmessage',[
        'chat_id'=>$chat_id,
        'text'=>"
https://t.me/learningv2ray/37
دانلود لینک مستقیم بدون استفاده از فیلتر شکن http://a4sos.ir/wt.ovpn
        "
    ]);
}


elseif ($text == "💌 | کد هدیه"){
    file_put_contents("data/$from_id/state.txt","Cod");
    bot('sendmessage',[
        'chat_id'=>$chat_id,
        'text'=>"
کد هدیه را بفرستید
        ",'reply_markup'=>$back
    ]);
}

elseif ($state == "Cod" and $text != "🔙"){
    if ( $Code == null or $Money_Code == null or $text != $Code ){
        file_put_contents("data/$from_id/state.txt","none");
        bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
کد هدیه اشتباه یا استفاده شده است
            ",'reply_markup'=>$start
        ]);
    }else{
        file_put_contents("data/$from_id/coin.txt",$coin+$Money_Code);
        file_put_contents("data/$from_id/state.txt","none");
        bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
کد هدیه درست است و مبلغ $Money_Code به موجودی شما اضافه شد
            ",'reply_markup'=>$start
        ]);

        bot('sendmessage',[
            'chat_id'=>$Dev,
            'text'=>"
کد هدیه ( $Code ) استفاده شد
            "
        ]);

        unlink("data/$from_id/Code.txt");
        unlink("data/$from_id/Money_Code.txt");

    }
}


elseif ($text == "/panel" or $text == "پنل"){
    if ($chat_id == $Dev){
        file_put_contents("data/$from_id/state.txt","none");
        bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"
به پنل مدیریت خوش اومدی
            "
            ,'reply_markup'=>$panel
        ]);
    }else{
        exit();
    }
}

elseif ($text == "openvpnآمار"){
    $user = file_get_contents("Member.txt");
    $panel1 = file_get_contents("data/servers/openvpn/openpanel_1.txt");
    $panel2 = file_get_contents("data/servers/openvpn/openpanel_2.txt");
    $panel3 = file_get_contents("data/servers/openvpn/openpanel_3.txt");
    $panel4 = file_get_contents("data/servers/openvpn/openpanel_4.txt");
    $panel5 = file_get_contents("data/servers/openvpn/openpanel_5.txt");
    $panel6 = file_get_contents("data/servers/openvpn/openpanel_6.txt");
    $panel7 = file_get_contents("data/servers/openvpn/openpanel_7.txt");
    $panel8 = file_get_contents("data/servers/openvpn/openpanel_8.txt");
    $member_id = explode("\n",$user);
    $member_id1 = explode("\n",$panel1);
    $member_id2 = explode("\n",$panel2);
    $member_id3 = explode("\n",$panel3);
    $member_id4 = explode("\n",$panel4);
    $member_id5 = explode("\n",$panel5);
    $member_id6 = explode("\n",$panel6);
    $member_id7 = explode("\n",$panel7);
    $member_id8 = explode("\n",$panel8);
    $member_count = count($member_id) -1;
    $member_count1 = count($member_id1) -1;
    $member_count2 = count($member_id2) -1;
    $member_count3 = count($member_id3) -1;
    $member_count4 = count($member_id4) -1;
    $member_count5 = count($member_id5) -1;
    $member_count6 = count($member_id6) -1;
    $member_count7 = count($member_id7) -1;
    $member_count8 = count($member_id8) -1;

    bot('sendmessage',[
        'chat_id'=>$chat_id,
        'text'=>"تعداد اعضا: $member_count\nتعداد کانفیگ پلن 1 : $member_count1\nتعداد کانفیگ پنل ۲ : $member_count2\nتعداد کانفیگ پنل ۳ : $member_count3\nتعداد کانفیگ پنل ۴ : $member_count4\nتعداد کانفیگ پنل ۵ : $member_count5\nتعداد کانفیگ پنل ۶ : $member_count6\n :تعداد کانفیگ پنل 7 : $member_count7\n :تعداد کانفیگ پنل8 : $member_count8\n :... ",
        'reply_markup'=>$panel    ]);
}
elseif ($text == "v2rayآمار"){
	   $user = file_get_contents("Member.txt");
	    $panel1 = file_get_contents("data/servers/v2ray/panel_1.txt");
 	    $panel2 = file_get_contents("data/servers/v2ray/panel_2.txt");
	    $panel3 = file_get_contents("data/servers/v2ray/panel_3.txt");
	    $panel4 = file_get_contents("data/servers/v2ray/panel_4.txt");
	    $panel5 = file_get_contents("data/servers/v2ray/panel_5.txt");
	    $panel6 = file_get_contents("data/servers/v2ray/panel_6.txt");
	    $panel7 = file_get_contents("data/servers/v2ray/panel_7.txt");
	    $panel8 = file_get_contents("data/servers/v2ray/panel_8.txt");
	    $member_id = explode("\n",$user);
	    $member_id1 = explode("\n",$panel1);
	    $member_id2 = explode("\n",$panel2);
	    $member_id3 = explode("\n",$panel3);
	    $member_id4 = explode("\n",$panel4);
	    $member_id5 = explode("\n",$panel5);
	    $member_id6 = explode("\n",$panel6);
	    $member_id7 = explode("\n",$panel7);
	    $member_id8 = explode("\n",$panel8);
	    $member_count = count($member_id) -1;
	    $member_count1 = count($member_id1) -1;
	    $member_count2 = count($member_id2) -1;
	    $member_count3 = count($member_id3) -1;
	    $member_count4 = count($member_id4) -1;
	    $member_count5 = count($member_id5) -1;
	    $member_count6 = count($member_id6) -1;
	    $member_count7 = count($member_id7) -1;
	    $member_count8 = count($member_id8) -1;
 bot('sendmessage',[
        'chat_id'=>$chat_id,
        'text'=>"تعداد اعضا: $member_count\nتعداد کانفیگ پلن 1 : $member_count1\nتعداد کانفیگ پنل ۲ : $member_count2\nتعداد کانفیگ پنل ۳ : $member_count3\nتعداد کانفیگ پنل ۴ : $member_count4\nتعداد کانفیگ پنل ۵ : $member_count5\nتعداد کانفیگ پنل ۶ : $member_count6\n :تعداد کانفیگ پنل 7 : $member_count7\n :تعداد کانفیگ پنل8 : $member_count8\n :... ",
        'reply_markup'=>$panel    ]);
}
elseif ($text == "آمارتک سرور"){
	    $user = file_get_contents("Member.txt");
	    $panel1 = file_get_contents("data/servers/s1v2ray/s1panel_1.txt");
 	    $panel2 = file_get_contents("data/servers/s1v2ray/s1panel_2.txt");
	    $panel3 = file_get_contents("data/servers/s1v2ray/s1panel_3.txt");
	    $panel4 = file_get_contents("data/servers/s1v2ray/s1panel_4.txt");
	    $panel5 = file_get_contents("data/servers/s1v2ray/s1panel_5.txt");
	    $panel6 = file_get_contents("data/servers/s1v2ray/s1panel_6.txt");
	    $panel7 = file_get_contents("data/servers/s1v2ray/s1panel_7.txt");
	    $panel8 = file_get_contents("data/servers/s1v2ray/s1panel_8.txt");
	    $member_id = explode("\n",$user);
	    $member_id1 = explode("\n",$panel1);
	    $member_id2 = explode("\n",$panel2);
	    $member_id3 = explode("\n",$panel3);
	    $member_id4 = explode("\n",$panel4);
	    $member_id5 = explode("\n",$panel5);
	    $member_id6 = explode("\n",$panel6);
	    $member_id7 = explode("\n",$panel7);
	    $member_id8 = explode("\n",$panel8);
	    $member_count = count($member_id) -1;
	    $member_count1 = count($member_id1) -1;
	    $member_count2 = count($member_id2) -1;
	    $member_count3 = count($member_id3) -1;
	    $member_count4 = count($member_id4) -1;
	    $member_count5 = count($member_id5) -1;
	    $member_count6 = count($member_id6) -1;
	    $member_count7 = count($member_id7) -1;
	    $member_count8 = count($member_id8) -1;
 bot('sendmessage',[
        'chat_id'=>$chat_id,
        'text'=>"تعداد اعضا: $member_count\nتعداد کانفیگ پلن 1 : $member_count1\nتعداد کانفیگ پنل ۲ : $member_count2\nتعداد کانفیگ پنل ۳ : $member_count3\nتعداد کانفیگ پنل ۴ : $member_count4\nتعداد کانفیگ پنل ۵ : $member_count5\nتعداد کانفیگ پنل ۶ : $member_count6\n :تعداد کانفیگ پنل 7 : $member_count7\n :تعداد کانفیگ پنل8 : $member_count8\n :... ",
        'reply_markup'=>$panel    ]);
}



elseif($text == "پیام همگانی" and $chat_id == $Dev){
    file_put_contents("data/$from_id/state.txt","pm");
    bot('sendMessage',[
    'chat_id'=>$chat_id,
    'text'=>"📨 | پیام خود را ارسال کنید !",
    'parse_mode'=>'html',
    'reply_markup'=>$back_panel
    ]);
    }
elseif($state == "pm" && $text !="🔙 برگشت" ){
file_put_contents("data/$from_id/state.txt","none");
bot('sendMessage',[
'chat_id'=>$chat_id,
'text'=>"📥 | پیام شما با موفقیت ارسال شد !",
'reply_markup'=>$panel
]);
$all_member = fopen( "Member.txt", "r");
while( !feof( $all_member)){
$user = fgets( $all_member);
bot('sendmessage',[
    'chat_id'=>$user,
    'text'=>$text
    ]);
}
}

elseif($text == "فوروارد همگانی" && $chat_id == $Dev){
    file_put_contents("data/$from_id/state.txt","for");
    bot('sendMessage',[
    'chat_id'=>$chat_id,
    'text'=>"📨 | پیام خود را فوروارد کنید !",
    'parse_mode'=>'html',
    'reply_markup'=>$back_panel
    ]);
    }
elseif($state == "for" && $text !="🔙 برگشت" ){
file_put_contents("data/$from_id/state.txt","none");
$all_member = fopen( "Member.txt", "r");
while( !feof( $all_member)){
$user = fgets( $all_member);
bot('ForwardMessage',[
    'chat_id'=>$user,
    'from_chat_id'=>$from_id,
    'message_id'=>$message_id
    ]);
}
bot('sendMessage',[
'chat_id'=>$chat_id,
'text'=>"📥 | پیام شما با موفقیت فوروارد شد !",
'reply_markup'=>$panel
]);
}


elseif($text == "اطلاعات کاربر" && $chat_id == $Dev){
    file_put_contents("data/$from_id/state.txt","infos");
    bot('sendMessage',[
    'chat_id'=>$chat_id,
    'text'=>"ایدی عددی کاربر را ارسال کنید",
    'parse_mode'=>'html',
    'reply_markup'=>$back_panel
    ]);
    }
elseif($state == "infos" && $text !="🔙 برگشت" ){
file_put_contents("data/$from_id/state.txt","none");

    $coin = file_get_contents("data/$text/coin.txt");
    $buys = file_get_contents("data/$text/buys.txt");

    bot('sendMessage',[
        'chat_id'=>$chat_id,
        'text'=>"
موجودی کاربر : $coin
تعداد خرید : $buys
        ",
        'reply_markup'=>$panel
        ]);

}





elseif ($text == "تنظیم چنل"){
    if($chat_id == $Dev){
        file_put_contents("data/$from_id/state.txt","set channel");
        bot('sendMessage',[
            'chat_id'=>$chat_id,
            'text'=>"ایدی چنل را بدون @ بفرستید
مثال : username",
            'reply_markup'=>$back_panel
            ]);
    }else{
        exit();
    }
}

elseif($state == "set channel" and $text != "🔙 برگشت"){
    file_put_contents("data/$from_id/state.txt","none");
    file_put_contents("data/channel.txt","$text");
    bot('sendMessage',[
        'chat_id'=>$chat_id,
        'text'=>"ست شد",
        'reply_markup'=>$panel
        ]);
}

elseif ($text == "افزایش موجودی"){
    if($chat_id == $Dev){
        file_put_contents("data/$from_id/state.txt","send_coin");
        bot('sendMessage',[
            'chat_id'=>$chat_id,
            'text'=>"ایدی عددی کاربر را ارسال کنید",
            'reply_markup'=>$back_panel
            ]);
    }else{
        exit();
    }
}

elseif ($state == "send_coin" and $text != "🔙 برگشت"){
    file_put_contents("data/$from_id/state.txt","send_coinn-$text");
    bot('sendMessage',[
        'chat_id'=>$chat_id,
        'text'=>"مقدار موجودی را بفرستید.",
        'reply_markup'=>$back_panel
        ]);
}

elseif (strpos($state , "send_coinn-") !== false){
    $user = explode("-",$state);
    $coin_user = file_get_contents("data/$user[1]/coin.txt");
    $new_coin = $coin_user + $text;
    file_put_contents("data/$user[1]/coin.txt",$new_coin);
    bot('sendMessage',[
        'chat_id'=>$chat_id,
        'text'=>"ارسال شد",
        'reply_markup'=>$panel
        ]);
    bot('sendMessage',[
        'chat_id'=>$user[1],
        'text'=>"
📣 اطلاعیه
کاربر گرامی مقدار $text موجودی به دستور مدیریت ربات به حساب شما اضافه گردید.
        "
        ]);
        file_put_contents("data/$from_id/state.txt","none");
	$chargefile2 = fopen("data/$from_id/charge.txt", "a") or die("Unable to open file!");
        fwrite($chargefile2,"$date,$time,manager+$text\n");
        fclose($chargefile2);
	exit();

}

elseif ($text == "کسر موجودی"){
    if($chat_id == $Dev){
        file_put_contents("data/$from_id/state.txt","deduction_coin");
        bot('sendMessage',[
            'chat_id'=>$chat_id,
            'text'=>"ایدی عددی کاربر را ارسال کنید",
            'reply_markup'=>$back_panel
            ]);
    }else{
        exit();
    }
}

elseif ($state == "deduction_coin" and $text != "🔙 برگشت"){
    file_put_contents("data/$from_id/state.txt","deduction_coinn-$text");
    bot('sendMessage',[
        'chat_id'=>$chat_id,
        'text'=>"مقدار موجودی را بفرستید.",
        'reply_markup'=>$back_panel
        ]);
}

elseif (strpos($state , "deduction_coinn-") !== false){
    $user = explode("-",$state);
    $coin_user = file_get_contents("data/$user[1]/coin.txt");
    $new_coin = $coin_user - $text;
    file_put_contents("data/$user[1]/coin.txt",$new_coin);
    bot('sendMessage',[
        'chat_id'=>$chat_id,
        'text'=>"کسر شد",
        'reply_markup'=>$panel
        ]);
    bot('sendMessage',[
        'chat_id'=>$user[1],
        'text'=>"
📣 اطلاعیه
کاربر گرامی مقدار $text موجودی به دستور مدیریت ربات از حساب شما کسر گردید.
        "
        ]);
        file_put_contents("data/$from_id/state.txt","none");
	$chargefile2 = fopen("data/$from_id/charge.txt", "a") or die("Unable to open file!");
        fwrite($chargefile2,"$date,$time,manager-$text\n");
        fclose($chargefile2);
	exit();
}




elseif ($text == "بن کاربر"){
    file_put_contents("data/$from_id/state.txt","ban");
    bot('sendMessage',[
        'chat_id'=>$chat_id,
        'text'=>"ایدی عددیشو بفرست",
        'reply_markup'=>$back_panel
        ]);
}

elseif ($state == "ban" and $text != "   برگشت"){
    file_put_contents("data/$from_id/state.txt","none");
    $myfile = fopen("data/ban.txt", "a") or die("Unable to open file!");
        fwrite($myfile, "$text\n");
        fclose($myfile);

    bot('sendMessage',[
        'chat_id'=>$chat_id,
        'text'=>"بن شد",
        'reply_markup'=>$panel
        ]);
    bot('sendMessage',[
        'chat_id'=>$text,
        'text'=>"شما از ربات بلاک شدید",
        'reply_markup'=>json_encode(['KeyboardRemove'=>[],'remove_keyboard'=>true])
        ]);
}

elseif ($text == "انبن کاربر"){
    file_put_contents("data/$from_id/state.txt","anban");
    bot('sendMessage',[
        'chat_id'=>$chat_id,
        'text'=>"ایدی عددیشو بفرست",
        'reply_markup'=>$back_panel
        ]);
}

elseif ($state == "anban" and $text != "↩️ برگشت"){
    file_put_contents("data/$from_id/state.txt","none");
    $newlist = str_replace($text, "", $ban);
    file_put_contents("data/ban.txt", $newlist);
    bot('sendMessage',[
        'chat_id'=>$chat_id,
        'text'=>"انبن شد",
        'reply_markup'=>$panel
        ]);
    bot('sendMessage',[
        'chat_id'=>$text,
        'text'=>"شما از ربات رفع بلاک شدید",
        'reply_markup'=>$start
        ]);
}

elseif ($text == "روشن"){
    file_put_contents("data/bot.txt","on");
    bot('sendMessage',[
        'chat_id'=>$chat_id,
        'text'=>"روشن شد",
        'reply_markup'=>$panel
        ]);
}
elseif ($text == "خاموش"){
    file_put_contents("data/bot.txt","off");
    bot('sendMessage',[
        'chat_id'=>$chat_id,
        'text'=>"خاموش شد",
        'reply_markup'=>$panel
        ]);

}


elseif ($text == "افزودن سرویس"){
    if ($chat_id == $Dev){

        bot('sendMessage',[
            'chat_id'=>$chat_id,
            'text'=>"کدوم پنل ؟",
            'reply_markup'=>$newpanel
            ]);

    }else{
        exit();
    }
}
elseif ($text == "20 گیگ" and $chat_id == $Dev){
        file_put_contents("data/$from_id/state.txt","newconfigon");
        bot('sendMessage',[
            'chat_id'=>$chat_id,
            'text'=>"کانفیگ را ارسال کنید",
            'reply_markup'=>$back_panel
            ]);
}

elseif ($state == "newconfigon" and $text != "🔙 برگشت"){
    $myfile2 = fopen("data/servers/panel_1.txt", "a") or die("Unable to open file!");
    fwrite($myfile2, "$text\n");
    fclose($myfile2);
    file_put_contents("data/$from_id/state.txt","none");
    bot('sendMessage',[
        'chat_id'=>$chat_id,
        'text'=>"ست شد",
        'reply_markup'=>$newpanel
        ]);
}


elseif ($text == "30 گیگ"){
    if ($chat_id == $Dev){
        file_put_contents("data/$from_id/state.txt","newpanel2");
        bot('sendMessage',[
            'chat_id'=>$chat_id,
            'text'=>"کانفیگ را ارسال کنید",
            'reply_markup'=>$back_panel
            ]);

    }else{
        exit();
    }
}

elseif ($state == "newpanel2" and $text != "🔙 برگشت"){
    $myfile2 = fopen("data/servers/panel_2.txt", "a") or die("Unable to open file!");
    fwrite($myfile2, "$text\n");
    fclose($myfile2);
    file_put_contents("data/$from_id/state.txt","none");
    bot('sendMessage',[
        'chat_id'=>$chat_id,
        'text'=>"ست شد",
        'reply_markup'=>$newpanel
        ]);
}

elseif ($text == "40 گیگ"){
    if ($chat_id == $Dev){
        file_put_contents("data/$from_id/state.txt","newpanel3");
        bot('sendMessage',[
            'chat_id'=>$chat_id,
            'text'=>"کانفیگ را ارسال کنید",
            'reply_markup'=>$back_panel
            ]);

    }else{
        exit();
    }
}

elseif ($state == "newpanel3" and $text != "🔙 برگشت"){
    $myfile2 = fopen("data/servers/panel_3.txt", "a") or die("Unable to open file!");
    fwrite($myfile2, "$text\n");
    fclose($myfile2);
    file_put_contents("data/$from_id/state.txt","none");
    bot('sendMessage',[
        'chat_id'=>$chat_id,
        'text'=>"ست شد",
        'reply_markup'=>$newpanel
        ]);
}


elseif ($text == "60 گیگ"){
    if ($chat_id == $Dev){
        file_put_contents("data/$from_id/state.txt","newpanel4");
        bot('sendMessage',[
            'chat_id'=>$chat_id,
            'text'=>"کانفیگ را ارسال کنید",
            'reply_markup'=>$back_panel
            ]);

    }else{
        exit();
    }
}

elseif ($state == "newpanel4" and $text != "🔙 برگشت"){
    $myfile2 = fopen("data/servers/panel_4.txt", "a") or die("Unable to open file!");
    fwrite($myfile2, "$text\n");
    fclose($myfile2);
    file_put_contents("data/$from_id/state.txt","none");
    bot('sendMessage',[
        'chat_id'=>$chat_id,
        'text'=>"ست شد",
        'reply_markup'=>$newpanel
        ]);
}

elseif ($text == "100 گیگ"){
    if ($chat_id == $Dev){
        file_put_contents("data/$from_id/state.txt","newpanel5");
        bot('sendMessage',[
            'chat_id'=>$chat_id,
            'text'=>"کانفیگ را ارسال کنید",
            'reply_markup'=>$back_panel
            ]);

    }else{
        exit();
    }
}

elseif ($state == "newpanel5" and $text != "🔙 برگشت"){
    $myfile2 = fopen("data/servers/panel_5.txt", "a") or die("Unable to open file!");
    fwrite($myfile2, "$text\n");
    fclose($myfile2);
    file_put_contents("data/$from_id/state.txt","none");
    bot('sendMessage',[
        'chat_id'=>$chat_id,
        'text'=>"ست شد",
        'reply_markup'=>$newpanel
        ]);
}

elseif ($text == "نامحدود"){
    if ($chat_id == $Dev){
        file_put_contents("data/$from_id/state.txt","newpanel6");
        bot('sendMessage',[
            'chat_id'=>$chat_id,
            'text'=>"کانفیگ را ارسال کنید",
            'reply_markup'=>$back_panel
            ]);

    }else{
        exit();
    }
}

elseif ($state == "newpanel6" and $text != "🔙 برگشت"){
    $myfile2 = fopen("data/servers/panel_6.txt", "a") or die("Unable to open file!");
    fwrite($myfile2, "$text\n");
    fclose($myfile2);
    file_put_contents("data/$from_id/state.txt","none");
    bot('sendMessage',[
        'chat_id'=>$chat_id,
        'text'=>"ست شد",
        'reply_markup'=>$newpanel
        ]);
}

elseif ($text == "کد هدیه"){
    if ($chat_id == $Dev){

        file_put_contents("data/$from_id/state.txt","i");
        bot('sendMessage',[
            'chat_id'=>$chat_id,
            'text'=>"ایدی عددی کاربر را بفرستید",
            'reply_markup'=>$back_panel
            ]);

    }else{
        exit();
    }
}

elseif ($state == "i" and $text != "🔙 برگشت"){
    file_put_contents("data/$from_id/state.txt","c_$text");
    bot('sendMessage',[
        'chat_id'=>$chat_id,
        'text'=>"کد هدیه را بفرستید",
        'reply_markup'=>$back_panel
        ]);
}

elseif (strpos($state,"c_")!==false and $text != "🔙 برگشت"){

    $id = explode("_",$state)[1];

    file_put_contents("data/$id/Code.txt",$text);

    file_put_contents("data/$from_id/state.txt","m_$id");
    bot('sendMessage',[
        'chat_id'=>$chat_id,
        'text'=>"مبلغ مورد نظر را بفرستید",
        'reply_markup'=>$back_panel
        ]);


}

elseif (strpos($state,"m_")!==false and $text != "🔙 برگشت"){

    $id = explode("_",$state)[1];

    file_put_contents("data/$id/Money_Code.txt",$text);

    file_put_contents("data/$from_id/state.txt","none");
    bot('sendMessage',[
        'chat_id'=>$chat_id,
        'text'=>"ست شد",
        'reply_markup'=>$panel
        ]);


}
//-----------------------------charge open vpn------------------------------------------
if ($text == "شارژ اکانت openvpn"){
   bot('sendmessage',[
        'chat_id'=>$chat_id,
'text' => "به بخش شارژ محصولات openvpn خوش اومدی
لطفا قبل از شارژاکانت، اعتبار خود را افزایش دهید
یکی از حجم های مورد نیاز خودرا انتخاب کنید
سپس یوزرنیم مورد نظر خودرا تایپ کنید و گزینه ارسال را بزنید.
           "
        ,'reply_markup'=>$openvpnch
    ]);
}
 if ($text == "شارژ اکانت 10گیگ" and $text != "🔙 برگشت" ){
		if ($coin <= $openmoneych_panel1 - 1){
 	   bot('sendmessage', [
	        'chat_id' => $chat_id,
		        'text' => "موجودی کافی نیست ازقسمت افزایش موجودی اقدام نمائید. قیمت کانفیگ : $openmoneych_panel1",
			        'reply_markup' => $start
    ]);

        goto End;
   }

file_put_contents("data/$from_id/state.txt", "openmoneych_panel1");
 if ($state == "openmoneych_panel1" ) {
	        file_put_contents("data/$from_id/state.txt", "none");
        $text1 = 'user-manager/user-profile/add profile=10GB user=';
        $host = 'qr2.voaxn1.xyz';
        $username = 'world';
        $password = '@@123456789';
        $port = 8080;

        if ($connection = ssh2_connect($host, $port)) {
            if (ssh2_auth_password($connection, $username, $password)) {
                $stream = ssh2_exec($connection, $text1 . $text);
                stream_set_blocking($stream, true);
                $output = stream_get_contents($stream);
                bot('sendMessage', [
                    'chat_id' => $chat_id,
	                    'text' => $output,
        	        ]);
                fclose($stream);
                $date = date('Y-m-d');
                $time = date('H:i:s');
                $logfile2 = fopen("erlogs.txt", "a") or die("Unable to open file!");
                fwrite($logfile2, "$date,$time,$text1$text,$output\n");
                fclose($logfile2);
                ssh2_disconnect($connection);
                if ($output == null or $output == "" or $output == " ") {
                    file_put_contents("data/$from_id/coin.txt", $coin - $openmoneych_panel1);
                    file_put_contents("data/$from_id/buys.txt", $buys + 1);
                    $remaincoin = file_get_contents("data/$from_id/coin.txt");
			bot('sendmessage',[
           		 'chat_id'=>$chat_id,
         		   'text'=>"
				اکانت شما شارژ شد	
				مانده اعتبار | $remaincoin
  				 ",'parse_mode'=>"html"
				        ,'reply_markup' => $start

            		   ]);

                    $logfile2 = fopen("data/$from_id/logs.txt", "a") or die("Unable to open file!");
                    fwrite($logfile2, "$date,$time,$openmoneych_panel1,$text,$c\n");
                    fclose($logfile2);

                    $adminlog = fopen("data/userslogs.txt", "a") or die("Unable to open file!");
                    fwrite($adminlog, "$from_id,$date,$time,$openmoneych_panel1,$text,$c\n");
                    fclose($adminlog);

                    exit();
               }
           }
       }
 End:  }
}

/*
if ($text == "شارژ اکانت 20گیگ" and $text != "🔙 برگشت" ){
	file_put_contents("data/$from_id/state.txt", "openmoneych_panel2");
	if ($coin <= $openmoneych_panel2 - 1)
 	   bot('sendmessage', [
	        'chat_id' => $chat_id,
		        'text' => "موجودی کافی نیست ازقسمت افزایش موجودی اقدام نمائید. قیمت کانفیگ : $openmoneych_panel2",
			        'reply_markup' => $start
    ]);
   {
        exit();
   }

} else {
    if ($state == "openmoneych_panel2" ) {
	        file_put_contents("data/$from_id/state.txt", "none");
        $text1 = 'user-manager/user-profile/add profile=20GB user=';
        $host = 'qr2.voaxn1.xyz';
        $username = 'world';
        $password = '@@123456789';
        $port = 8080;

        if ($connection = ssh2_connect($host, $port)) {
            if (ssh2_auth_password($connection, $username, $password)) {
                $stream = ssh2_exec($connection, $text1 . $text);
                stream_set_blocking($stream, true);
                $output = stream_get_contents($stream);
                bot('sendMessage', [
                    'chat_id' => $chat_id,
	                    'text' => $output,
        	        ]);
                fclose($stream);
                $date = date('Y-m-d');
                $time = date('H:i:s');
                $logfile2 = fopen("erlogs.txt", "a") or die("Unable to open file!");
                fwrite($logfile2, "$date,$time,$text1$text,$output\n");
                fclose($logfile2);
                ssh2_disconnect($connection);
                if ($output == null or $output == "" or $output == " ") {
                    file_put_contents("data/$from_id/coin.txt", $coin - $openmoneych_panel2);
                    file_put_contents("data/$from_id/buys.txt", $buys + 1);
                    $remaincoin = file_get_contents("data/$from_id/coin.txt");
			bot('sendmessage',[
           		 'chat_id'=>$chat_id,
         		   'text'=>"
				اکانت شما شارژ شد
				مانده اعتبار | $remaincoin
  				 ",'parse_mode'=>"html"
				        ,'reply_markup' => $start

            		   ]);

                    $logfile2 = fopen("data/$from_id/logs.txt", "a") or die("Unable to open file!");
                    fwrite($logfile2, "$date,$time,$openmoneych_panel2,$text,$c\n");
                    fclose($logfile2);

                    $adminlog = fopen("data/userslogs.txt", "a") or die("Unable to open file!");
                    fwrite($adminlog, "$from_id,$date,$time,$openmoneych_panel2,$text,$c\n");
                    fclose($adminlog);

                    exit();
               }
           }
       }
   }
}

if ($text == "شارژ اکانت 30گیگ" and $text != "🔙 برگشت" ){
	file_put_contents("data/$from_id/state.txt", "openmoneych_panel3");
	if ($coin <= $openmoneych_panel3 - 1)
 	   bot('sendmessage', [
	        'chat_id' => $chat_id,
		        'text' => "موجودی کافی نیست ازقسمت افزایش موجودی اقدام نمائید. قیمت کانفیگ : $openmoneych_panel3",
			        'reply_markup' => $start
    ]);
   {
        exit();
   }

} else {
    if ($state == "openmoneych_panel3" ) {
	        file_put_contents("data/$from_id/state.txt", "none");
        $text1 = 'user-manager/user-profile/add profile=30GB user=';
        $host = 'qr2.voaxn1.xyz';
        $username = 'world';
        $password = '@@123456789';
        $port = 8080;

        if ($connection = ssh2_connect($host, $port)) {
            if (ssh2_auth_password($connection, $username, $password)) {
                $stream = ssh2_exec($connection, $text1 . $text);
                stream_set_blocking($stream, true);
                $output = stream_get_contents($stream);
                bot('sendMessage', [
                    'chat_id' => $chat_id,
	                    'text' => $output,
        	        ]);
                fclose($stream);
                $date = date('Y-m-d');
                $time = date('H:i:s');
                $logfile2 = fopen("erlogs.txt", "a") or die("Unable to open file!");
                fwrite($logfile2, "$date,$time,$text1$text,$output\n");
                fclose($logfile2);
                ssh2_disconnect($connection);
                if ($output == null or $output == "" or $output == " ") {
                    file_put_contents("data/$from_id/coin.txt", $coin - $openmoneych_panel3);
                    file_put_contents("data/$from_id/buys.txt", $buys + 1);
                    $remaincoin = file_get_contents("data/$from_id/coin.txt");
			bot('sendmessage',[
           		 'chat_id'=>$chat_id,
         		   'text'=>"
				اکانت شما شارژ شد
				مانده اعتبار | $remaincoin
  				 ",'parse_mode'=>"html"
				        ,'reply_markup' => $start

            		   ]);

                    $logfile2 = fopen("data/$from_id/logs.txt", "a") or die("Unable to open file!");
                    fwrite($logfile2, "$date,$time,$openmoneych_panel3,$text,$c\n");
                    fclose($logfile2);

                    $adminlog = fopen("data/userslogs.txt", "a") or die("Unable to open file!");
                    fwrite($adminlog, "$from_id,$date,$time,$openmoneych_panel3,$text,$c\n");
                    fclose($adminlog);

                    exit();
               }
           }
       }
   }
}

if ($text == "شارژ اکانت 40گیگ" and $text != "🔙 برگشت" ){
	file_put_contents("data/$from_id/state.txt", "openmoneych_panel4");
	if ($coin <= $openmoneych_panel4 - 1)
 	   bot('sendmessage', [
	        'chat_id' => $chat_id,
		        'text' => "موجودی کافی نیست ازقسمت افزایش موجودی اقدام نمائید. قیمت کانفیگ : $openmoneych_panel4",
			        'reply_markup' => $start
    ]);
   {
        exit();
   }

} else {
    if ($state == "openmoneych_panel4" ) {
	        file_put_contents("data/$from_id/state.txt", "none");
        $text1 = 'user-manager/user-profile/add profile=40GB user=';
        $host = 'qr2.voaxn1.xyz';
        $username = 'world';
        $password = '@@123456789';
        $port = 8080;

        if ($connection = ssh2_connect($host, $port)) {
            if (ssh2_auth_password($connection, $username, $password)) {
                $stream = ssh2_exec($connection, $text1 . $text);
                stream_set_blocking($stream, true);
                $output = stream_get_contents($stream);
                bot('sendMessage', [
                    'chat_id' => $chat_id,
	                    'text' => $output,
        	        ]);
                fclose($stream);
                $date = date('Y-m-d');
                $time = date('H:i:s');
                $logfile2 = fopen("erlogs.txt", "a") or die("Unable to open file!");
                fwrite($logfile2, "$date,$time,$text1$text,$output\n");
                fclose($logfile2);
                ssh2_disconnect($connection);
                if ($output == null or $output == "" or $output == " ") {
                    file_put_contents("data/$from_id/coin.txt", $coin - $openmoneych_panel4);
                    file_put_contents("data/$from_id/buys.txt", $buys + 1);
                    $remaincoin = file_get_contents("data/$from_id/coin.txt");
			bot('sendmessage',[
           		 'chat_id'=>$chat_id,
         		   'text'=>"
				اکانت شما شارژ شد
				مانده اعتبار | $remaincoin
  				 ",'parse_mode'=>"html"
				        ,'reply_markup' => $start

            		   ]);

                    $logfile2 = fopen("data/$from_id/logs.txt", "a") or die("Unable to open file!");
                    fwrite($logfile2, "$date,$time,$openmoneych_panel4,$text,$c\n");
                    fclose($logfile2);

                    $adminlog = fopen("data/userslogs.txt", "a") or die("Unable to open file!");
                    fwrite($adminlog, "$from_id,$date,$time,$openmoneych_panel4,$text,$c\n");
                    fclose($adminlog);

                    exit();
               }
           }
       }
   }
}

if ($text == "شارژ اکانت 50گیگ" and $text != "🔙 برگشت" ){
	file_put_contents("data/$from_id/state.txt", "openmoneych_panel5");
	if ($coin <= $openmoneych_panel5 - 1)
 	   bot('sendmessage', [
	        'chat_id' => $chat_id,
		        'text' => "موجودی کافی نیست ازقسمت افزایش موجودی اقدام نمائید. قیمت کانفیگ : $openmoneych_panel5",
			        'reply_markup' => $start
    ]);
   {
        exit();
   }

} else {
    if ($state == "openmoneych_panel5" ) {
	        file_put_contents("data/$from_id/state.txt", "none");
        $text1 = 'user-manager/user-profile/add profile=50GB user=';
        $host = 'qr2.voaxn1.xyz';
        $username = 'world';
        $password = '@@123456789';
        $port = 8080;

        if ($connection = ssh2_connect($host, $port)) {
            if (ssh2_auth_password($connection, $username, $password)) {
                $stream = ssh2_exec($connection, $text1 . $text);
                stream_set_blocking($stream, true);
                $output = stream_get_contents($stream);
                bot('sendMessage', [
                    'chat_id' => $chat_id,
	                    'text' => $output,
        	        ]);
                fclose($stream);
                $date = date('Y-m-d');
                $time = date('H:i:s');
                $logfile2 = fopen("erlogs.txt", "a") or die("Unable to open file!");
                fwrite($logfile2, "$date,$time,$text1$text,$output\n");
                fclose($logfile2);
                ssh2_disconnect($connection);
                if ($output == null or $output == "" or $output == " ") {
                    file_put_contents("data/$from_id/coin.txt", $coin - $openmoneych_panel5);
                    file_put_contents("data/$from_id/buys.txt", $buys + 1);
                    $remaincoin = file_get_contents("data/$from_id/coin.txt");
			bot('sendmessage',[
           		 'chat_id'=>$chat_id,
         		   'text'=>"
				اکانت شما شارژ شد
				مانده اعتبار | $remaincoin
  				 ",'parse_mode'=>"html"
				        ,'reply_markup' => $start

            		   ]);

                    $logfile2 = fopen("data/$from_id/logs.txt", "a") or die("Unable to open file!");
                    fwrite($logfile2, "$date,$time,$openmoneych_panel5,$text,$c\n");
                    fclose($logfile2);

                    $adminlog = fopen("data/userslogs.txt", "a") or die("Unable to open file!");
                    fwrite($adminlog, "$from_id,$date,$time,$openmoneych_panel5,$text,$c\n");
                    fclose($adminlog);

                    exit();
               }
           }
       }
   }
}

if ($text == "شارژ اکانت 60گیگ" and $text != "🔙 برگشت" ){
	file_put_contents("data/$from_id/state.txt", "openmoneych_panel6");
	if ($coin <= $openmoneych_panel6 - 1)
 	   bot('sendmessage', [
	        'chat_id' => $chat_id,
		        'text' => "موجودی کافی نیست ازقسمت افزایش موجودی اقدام نمائید. قیمت کانفیگ : $openmoneych_panel6",
			        'reply_markup' => $start
    ]);
   {
        exit();
   }

} else {
    if ($state == "openmoneych_panel6" ) {
	        file_put_contents("data/$from_id/state.txt", "none");
        $text1 = 'user-manager/user-profile/add profile=60GB user=';
        $host = 'qr2.voaxn1.xyz';
        $username = 'world';
        $password = '@@123456789';
        $port = 8080;

        if ($connection = ssh2_connect($host, $port)) {
            if (ssh2_auth_password($connection, $username, $password)) {
                $stream = ssh2_exec($connection, $text1 . $text);
                stream_set_blocking($stream, true);
                $output = stream_get_contents($stream);
                bot('sendMessage', [
                    'chat_id' => $chat_id,
	                    'text' => $output,
        	        ]);
                fclose($stream);
                $date = date('Y-m-d');
                $time = date('H:i:s');
                $logfile2 = fopen("erlogs.txt", "a") or die("Unable to open file!");
                fwrite($logfile2, "$date,$time,$text1$text,$output\n");
                fclose($logfile2);
                ssh2_disconnect($connection);
                if ($output == null or $output == "" or $output == " ") {
                    file_put_contents("data/$from_id/coin.txt", $coin - $openmoneych_panel6);
                    file_put_contents("data/$from_id/buys.txt", $buys + 1);
                    $remaincoin = file_get_contents("data/$from_id/coin.txt");
			bot('sendmessage',[
           		 'chat_id'=>$chat_id,
         		   'text'=>"
				اکانت شما شارژ شد
				مانده اعتبار | $remaincoin
  				 ",'parse_mode'=>"html"
				        ,'reply_markup' => $start

            		   ]);

                    $logfile2 = fopen("data/$from_id/logs.txt", "a") or die("Unable to open file!");
                    fwrite($logfile2, "$date,$time,$openmoneych_panel6,$text,$c\n");
                    fclose($logfile2);

                    $adminlog = fopen("data/userslogs.txt", "a") or die("Unable to open file!");
                    fwrite($adminlog, "$from_id,$date,$time,$openmoneych_panel6,$text,$c\n");
                    fclose($adminlog);

                    exit();
               }
           }
       }
   }
}

if ($text == "شارژ اکانت 70گیگ" and $text != "🔙 برگشت" ){
	file_put_contents("data/$from_id/state.txt", "openmoneych_panel7");
	if ($coin <= $openmoneych_panel7 - 1)
 	   bot('sendmessage', [
	        'chat_id' => $chat_id,
		        'text' => "موجودی کافی نیست ازقسمت افزایش موجودی اقدام نمائید. قیمت کانفیگ : $openmoneych_panel7",
			        'reply_markup' => $start
    ]);
   {
        exit();
   }

} else {
    if ($state == "openmoneych_panel7" ) {
	        file_put_contents("data/$from_id/state.txt", "none");
        $text1 = 'user-manager/user-profile/add profile=70GB user=';
        $host = 'qr2.voaxn1.xyz';
        $username = 'world';
        $password = '@@123456789';
        $port = 8080;

        if ($connection = ssh2_connect($host, $port)) {
            if (ssh2_auth_password($connection, $username, $password)) {
                $stream = ssh2_exec($connection, $text1 . $text);
                stream_set_blocking($stream, true);
                $output = stream_get_contents($stream);
                bot('sendMessage', [
                    'chat_id' => $chat_id,
	                    'text' => $output,
        	        ]);
                fclose($stream);
                $date = date('Y-m-d');
                $time = date('H:i:s');
                $logfile2 = fopen("erlogs.txt", "a") or die("Unable to open file!");
                fwrite($logfile2, "$date,$time,$text1$text,$output\n");
                fclose($logfile2);
                ssh2_disconnect($connection);
                if ($output == null or $output == "" or $output == " ") {
                    file_put_contents("data/$from_id/coin.txt", $coin - $openmoneych_panel7);
                    file_put_contents("data/$from_id/buys.txt", $buys + 1);
                    $remaincoin = file_get_contents("data/$from_id/coin.txt");
			bot('sendmessage',[
           		 'chat_id'=>$chat_id,
         		   'text'=>"
				اکانت شما شارژ شد
				مانده اعتبار | $remaincoin
  				 ",'parse_mode'=>"html"
				        ,'reply_markup' => $start

            		   ]);

                    $logfile2 = fopen("data/$from_id/logs.txt", "a") or die("Unable to open file!");
                    fwrite($logfile2, "$date,$time,$openmoneych_panel7,$text,$c\n");
                    fclose($logfile2);

                    $adminlog = fopen("data/userslogs.txt", "a") or die("Unable to open file!");
                    fwrite($adminlog, "$from_id,$date,$time,$openmoneych_panel7,$text,$c\n");
                    fclose($adminlog);

                    exit();
               }
           }
       }
   }
}

if ($text == "شارژ اکانت 80گیگ" and $text != "🔙 برگشت" ){
	file_put_contents("data/$from_id/state.txt", "openmoneych_panel8");
	if ($coin <= $openmoneych_panel8 - 1)
 	   bot('sendmessage', [
	        'chat_id' => $chat_id,
		        'text' => "موجودی کافی نیست ازقسمت افزایش موجودی اقدام نمائید. قیمت کانفیگ : $openmoneych_panel8",
			        'reply_markup' => $start
    ]);
   {
        exit();
   }

} else {
    if ($state == "openmoneych_panel8" ) {
	        file_put_contents("data/$from_id/state.txt", "none");
        $text1 = 'user-manager/user-profile/add profile=80GB user=';
        $host = 'qr2.voaxn1.xyz';
        $username = 'world';
        $password = '@@123456789';
        $port = 8080;

        if ($connection = ssh2_connect($host, $port)) {
            if (ssh2_auth_password($connection, $username, $password)) {
                $stream = ssh2_exec($connection, $text1 . $text);
                stream_set_blocking($stream, true);
                $output = stream_get_contents($stream);
                bot('sendMessage', [
                    'chat_id' => $chat_id,
	                    'text' => $output,
        	        ]);
                fclose($stream);
                $date = date('Y-m-d');
                $time = date('H:i:s');
                $logfile2 = fopen("erlogs.txt", "a") or die("Unable to open file!");
                fwrite($logfile2, "$date,$time,$text1$text,$output\n");
                fclose($logfile2);
                ssh2_disconnect($connection);
                if ($output == null or $output == "" or $output == " ") {
                    file_put_contents("data/$from_id/coin.txt", $coin - $openmoneych_panel8);
                    file_put_contents("data/$from_id/buys.txt", $buys + 1);
                    $remaincoin = file_get_contents("data/$from_id/coin.txt");
			bot('sendmessage',[
           		 'chat_id'=>$chat_id,
         		   'text'=>"
				اکانت شما شارژ شد
				مانده اعتبار | $remaincoin
  				 ",'parse_mode'=>"html"
				        ,'reply_markup' => $start

            		   ]);

                    $logfile2 = fopen("data/$from_id/logs.txt", "a") or die("Unable to open file!");
                    fwrite($logfile2, "$date,$time,$openmoneych_panel8,$text,$c\n");
                    fclose($logfile2);

                    $adminlog = fopen("data/userslogs.txt", "a") or die("Unable to open file!");
                    fwrite($adminlog, "$from_id,$date,$time,$openmoneych_panel8,$text,$c\n");
                    fclose($adminlog);

                    exit();
               }
           }
       }
   }
}

if ($text == "شارژ اکانت 100گیگ" and $text != "🔙 برگشت" ){
	file_put_contents("data/$from_id/state.txt", "openmoneych_panel9");
	if ($coin <= $openmoneych_panel9 - 1)
 	   bot('sendmessage', [
	        'chat_id' => $chat_id,
		        'text' => "موجودی کافی نیست ازقسمت افزایش موجودی اقدام نمائید. قیمت کانفیگ : $openmoneych_panel9",
			        'reply_markup' => $start
    ]);
   {
        exit();
   }

} else {
    if ($state == "openmoneych_panel9" ) {
	        file_put_contents("data/$from_id/state.txt", "none");
        $text1 = 'user-manager/user-profile/add profile=100GB user=';
        $host = 'qr2.voaxn1.xyz';
        $username = 'world';
        $password = '@@123456789';
        $port = 8080;

        if ($connection = ssh2_connect($host, $port)) {
            if (ssh2_auth_password($connection, $username, $password)) {
                $stream = ssh2_exec($connection, $text1 . $text);
                stream_set_blocking($stream, true);
                $output = stream_get_contents($stream);
                bot('sendMessage', [
                    'chat_id' => $chat_id,
	                    'text' => $output,
        	        ]);
                fclose($stream);
                $date = date('Y-m-d');
                $time = date('H:i:s');
                $logfile2 = fopen("erlogs.txt", "a") or die("Unable to open file!");
                fwrite($logfile2, "$date,$time,$text1$text,$output\n");
                fclose($logfile2);
                ssh2_disconnect($connection);
                if ($output == null or $output == "" or $output == " ") {
                    file_put_contents("data/$from_id/coin.txt", $coin - $openmoneych_panel9);
                    file_put_contents("data/$from_id/buys.txt", $buys + 1);
                    $remaincoin = file_get_contents("data/$from_id/coin.txt");
			bot('sendmessage',[
           		 'chat_id'=>$chat_id,
         		   'text'=>"
				اکانت شما شارژ شد
				مانده اعتبار | $remaincoin
  				 ",'parse_mode'=>"html"
				        ,'reply_markup' => $start

            		   ]);

                    $logfile2 = fopen("data/$from_id/logs.txt", "a") or die("Unable to open file!");
                    fwrite($logfile2, "$date,$time,$openmoneych_panel9,$text,$c\n");
                    fclose($logfile2);

                    $adminlog = fopen("data/userslogs.txt", "a") or die("Unable to open file!");
                    fwrite($adminlog, "$from_id,$date,$time,$openmoneych_panel9,$text,$c\n");
                    fclose($adminlog);

                    exit();
               }
           }
       }
   }
}

if ($text == "شارژ اکانت نامحدود" and $text != "🔙 برگشت" ){
	file_put_contents("data/$from_id/state.txt", "openmoneych_panel10");
	if ($coin <= $openmoneych_panel10 - 1)
 	   bot('sendmessage', [
	        'chat_id' => $chat_id,
		        'text' => "موجودی کافی نیست ازقسمت افزایش موجودی اقدام نمائید. قیمت کانفیگ : $openmoneych_panel10",
			        'reply_markup' => $start
    ]);
   exit();} else {
    if ($state == "openmoneych_panel10" ) {
	        file_put_contents("data/$from_id/state.txt", "none");
        $text1 = 'user-manager/user-profile/add profile=unlimited user=';
        $host = 'qr2.voaxn1.xyz';
        $username = 'world';
        $password = '@@123456789';
        $port = 8080;

        if ($connection = ssh2_connect($host, $port)) {
            if (ssh2_auth_password($connection, $username, $password)) {
                $stream = ssh2_exec($connection, $text1 . $text);
                stream_set_blocking($stream, true);
                $output = stream_get_contents($stream);
                bot('sendMessage', [
                    'chat_id' => $chat_id,
	                    'text' => $output,
        	        ]);
                fclose($stream);
                $date = date('Y-m-d');
                $time = date('H:i:s');
                $logfile2 = fopen("erlogs.txt", "a") or die("Unable to open file!");
                fwrite($logfile2, "$date,$time,$text1$text,$output\n");
                fclose($logfile2);
                ssh2_disconnect($connection);
                if ($output == null or $output == "" or $output == " ") {
                    file_put_contents("data/$from_id/coin.txt", $coin - $openmoneych_panel10);
                    file_put_contents("data/$from_id/buys.txt", $buys + 1);
                    $remaincoin = file_get_contents("data/$from_id/coin.txt");
			bot('sendmessage',[
           		 'chat_id'=>$chat_id,
         		   'text'=>"
				اکانت شما شارژ شد
				مانده اعتبار | $remaincoin
  				 ",'parse_mode'=>"html"
				        ,'reply_markup' => $start

            		   ]);

                    $logfile2 = fopen("data/$from_id/logs.txt", "a") or die("Unable to open file!");
                    fwrite($logfile2, "$date,$time,$openmoneych_panel10,$text,$c\n");
                    fclose($logfile2);

                    $adminlog = fopen("data/userslogs.txt", "a") or die("Unable to open file!");
                    fwrite($adminlog, "$from_id,$date,$time,$openmoneych_panel10,$text,$c\n");
                    fclose($adminlog);

                    exit();
               }
           }
       }
   }
}
*/
?>