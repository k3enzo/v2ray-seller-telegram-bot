<?php

defined('Dbhost') || define('Dbhost', 'localhost');
defined('Dbname') || define('Dbname', 'irannanc_v2ray');
defined('Dbuser') || define('Dbuser', 'irannanc_v2rayuser');
defined('Dbpass') || define('Dbpass', '123!@#QWE');


defined('Db') || define('Db', 'mysql');
defined('setDefaultProtocol') || define('setDefaultProtocol', 'vmess');

defined('setDefaultTransmission') || define('setDefaultTransmission', 'ws');
defined('setDefaultHeader') || define('setDefaultHeader', 'wikipedia.org');
defined('setSniffing') || define('setSniffing', ['http', 'tls', 'quic']);
defined('setDefaultUserPort') || define('setDefaultUserPort', 57120);
defined('setDefaultUserUUID') || define('setDefaultUserUUID', 'userUUID');
defined('defaultUserDetail') || define('defaultUserDetail', [
    'storage' => 20,
    'expire' => 30,
    'protocol' => setDefaultProtocol,
    'transmission' => setDefaultTransmission,
    'remark' => 'user_' . rand(111111, 999999999)
]);


defined('VPNServersConfig') || define('VPNServersConfig', [
    1 => ['host' => '65.108.150.108', 'port' => '2053', 'username' => 'y', 'password' => 'y', '' => ''],
    2 => ['host' => '37.27.15.218', 'port' => '2053', 'username' => 'y', 'password' => 'y', '' => ''],
]);

//include_once 'class/Dbase.php';


class VPNServer
{
    private $serverid, $Xui, $DB;
    private $chatid=null,$telegrami=null;


    function __construct($serverid)
    {
        $this->serverid = intval($serverid);
        $this->connect();
    }

    function connect()
    {
        $serverAddress = 'api://' . VPNServersConfig[$this->serverid]['host'] . ':' . VPNServersConfig[$this->serverid]['port'] . '/';
        $this->DB = new Dbase();
        $this->Xui = new xuiConnect($serverAddress, $serverAddress, VPNServersConfig[$this->serverid]['username'], VPNServersConfig[$this->serverid]['password'], 1);
        return $this;
    }

    function setTelegramUser($chatid,$telegramid=null)
    {
        $this->chatid = $chatid??null;
        $this->telegramid = $telegramid??null;
        return $this;
    }

    function createUser($GB = null, $Expire = null, $protocol = null, $tansmission = null, $username = null)
    {
        $port = xuiTools::randPort();
        $GB ??= defaultUserDetail['storage'];
        $Expire ??= defaultUserDetail['expire'];
        $protocol ??= defaultUserDetail['protocol'];
        $tansmission ??= defaultUserDetail['transmission'];
        $username ??= defaultUserDetail['remark'];
        $clientCreated = $this->Xui->add($GB, $Expire, $protocol, $tansmission, $username);
        if ($clientCreated and !empty($clientCreated['obj'])) {

            $Created = $this->setAddress($clientCreated['obj']['uuid'], $clientCreated['obj']['email'], $port);
            if ($Created and !empty($Created['obj'])) {
                $this->DB->insert('insert into `users` (`uuid`,`protocol`,`expiredate`,`transmission`,`remark`,`port`,`url`,`qr`,`telegram_id`,`telegram_username`,`storage`) VALUES (?,?,?,?,?,?,?,?,?,?,?)',
                    [
                        $clientCreated['obj']['uuid'], $protocol,$Expire,$tansmission,$username,$port,$Created['obj']['url'],$this->generateQR($Created['obj']['url']),$this->chatid,$this->telegramid,$GB
                    ]);
                return $Created['obj']['url'];
            }
            return 'Error in Create Address';
        }
        return 'error in Create User';
    }

    function setAddress($uuid, $email, $port)
    {

        return $this->Xui->createUrl([
            'uuid' => $uuid,
            'email' => $email,
            'enable' => true, # true/false
        ]);
    }


    function fetchUser($UUID=null){
        return $this->Xui->fetch(['uuid' => 'userUUID']);
    }
    function generateQR($url)
    {
        $Make = xuiTools::genQRCode($url, 'MyHtmlClassName');
        return $Make['obj']['html'];
    }

}

