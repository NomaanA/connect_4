<?php

//go to the data layer and actually get the data I want
require_once('bizDataLayer/chatData.php');
require_once('token_svc.php');
date_default_timezone_set('America/New_York');

/********************************************************************
talking to BizLayet to get the chat information
********************************************************************/

function getChat($d,$ip,$token){
	 
	global $posArray;
	$date = date_create();
	$timeStamp = date("Y-m-d H:i:s");
	$currentTimeStamp = date_timestamp_get($date);
	$userArray = fetchNameId($token,$posArray);
	breakToken($token,$posArray,$userArray[0],$userArray[1],$ip,$currentTimeStamp);
	echo(getChatData($d));
	
}

/********************************************************************

Sends the msg to the BizData Layer to set chat

********************************************************************/

function setChat($d,$ip,$token){
	global $posArray;
	$date = date_create();
	$timeStamp = date("Y-m-d H:i:s");
	$currentTimeStamp = date_timestamp_get($date);
	$userArray = fetchNameId($token,$posArray);
	breakToken($token,$posArray,$userArray[0],$userArray[1],$ip,$currentTimeStamp);
	$h = explode('|',$d);
	$msg = $h[0];
	$room = $h[1];
	echo(setChatData($msg,$room,$userArray[1],$timeStamp));
}

/********************************************************************

Requests the BizData Layer for online users

********************************************************************/

function getUsers($d,$ip,$token){
	$h = explode('|',$d);
	$room = $h[0];
	echo (getOnlineUsers($room));
}


?>