<?php

function check_channel_member($channel , $chat_id){
	$res = bot("getChatMember" , array("chat_id" => $channel , "user_id" => $chat_id));
	if(isset($res->result->user) && $res->result->status == "member"){
		return "yes";
	}elseif($res->result->status == "administrator"){
		return "yes";
	}elseif($res->result->status == "creator"){
		return "yes";
	}else{
	    return "no";
	}
}
function bot($method,$datas=[]){
    $url = "https://api.telegram.org/bot".API_KEY."/".$method;
   $ch = curl_init();
   curl_setopt($ch,CURLOPT_URL,$url);
   curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
   curl_setopt($ch,CURLOPT_POSTFIELDS,$datas);
   $res = curl_exec($ch);
   if(curl_error($ch)){
   var_dump(curl_error($ch));
   }else{
   return json_decode($res);
   }
   }
   function objectToArrays( $object ){
    if( !is_object( $object ) && !is_array( $object ))
    {
    return $object;
    }
    if( is_object( $object ))
    {
    $object = get_object_vars( $object );
    }
    return array_map( "objectToArrays", $object );
    }

