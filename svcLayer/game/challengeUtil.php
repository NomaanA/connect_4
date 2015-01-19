<?php
//ALL game goes in this folder
require_once('./bizDataLayer/challengeData.php');
require_once('./token_svc.php');
date_default_timezone_set('America/New_York');

/********************************************************************

Sends the set challenge details to BizData Layer

********************************************************************/

    function setChallenge($d,$ip,$token){
        global $posArray;
        $date = date_create();
        $currentTimeStamp = date_timestamp_get($date);
        $userArray = fetchNameId($token,$posArray);
        $player1 = $userArray[1];
        
        list($player2_id, $player2_name, $room_id) = explode('|',$d);
 
        breakToken($token,$posArray,$userArray[0],$userArray[1],$ip,$currentTimeStamp);
        return (setChallengeData($player1,$player2_id,$player2_name,$room_id));
    }

/********************************************************************

Requests the BizData Layer for challenge

********************************************************************/

    function getChallenge($d,$ip,$token){
        global $posArray;
        $date = date_create();
        $currentTimeStamp = date_timestamp_get($date);
        $userArray = fetchNameId($token,$posArray);
        $player1 = $d;
        breakToken($token,$posArray,$userArray[0],$userArray[1],$ip,$currentTimeStamp);
        echo (getChallengeData($player1));
    }

/********************************************************************

Sends the set challenge status details to BizData Layer

********************************************************************/
	function setChallengeStatus($d,$ip,$token){
		global $posArray;
        $date = date_create();
        $currentTimeStamp = date_timestamp_get($date);
        $userArray = fetchNameId($token,$posArray);
        
        list($challenge_id, $challenge_status) = explode('|',$d);

        breakToken($token,$posArray,$userArray[0],$userArray[1],$ip,$currentTimeStamp);
		echo (setChallengeStatusData($challenge_id,$challenge_status));
	}

/********************************************************************

Requests the BizData Layer for challenge status

********************************************************************/

    function getChallengeStatus($d,$ip,$token){
        global $posArray;
        $date = date_create();
        $currentTimeStamp = date_timestamp_get($date);
        $userArray = fetchNameId($token,$posArray);
        breakToken($token,$posArray,$userArray[0],$userArray[1],$ip,$currentTimeStamp);
        echo (getChallengeStatusData($d));
    }
?>