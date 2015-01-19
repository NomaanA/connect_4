<?php

require("bizDataLayer/loginData.php");

function getLogin($d){
	
	list($username, $password) = explode("|", $d);
	//you can sha the password here
	//$password = sha1()
	echo getLoginData($username, $password);
    //return '[{"success":true}]';
}



function setLogin($d){
	
	list($username, $password) = explode("|", $d);
	//you can sha the password here
	//$password = sha1()
	//checking the token here! TODO!
	echo setLoginData($username, $password);
    //return '[{"success":true}]';
}

function setLogout($d,$ip,$token){
    $h = explode('|',$d);
    $userid = $h[0];
    echo logout($userid);
}


?>