<?php
 
	date_default_timezone_set('America/New_York');
    //Position Array
    $posArray = array(0,2,4,6,1,3,5,7);
    
    //creating the date
    $date = date_create();
    $currentTimeStamp = date_timestamp_get($date);

    //IP handeling 
    $currentIp = $_SERVER['REMOTE_ADDR'];
    $currentIp = str_replace('.','',$currentIp);

/*====================================================================================
handy functions
=====================================================================================*/




/********************************************************************

converting the base from 

********************************************************************/

    function convertBase($what){
        $enWhat = base_convert($what,36,10);
        return $enWhat;
    }

/********************************************************************

Appends Zeros

********************************************************************/
    function addZeros($which)
    {
        $largest = 0;
        for($i = 0; $i < count($which); $i++){
            $largest = max($largest,strlen($which[$i]));
        }
        $largest = (ceil($largest/10))*10;
        
        for($i = 0; $i < count($which); $i++){
            $appendStr = '';
            for($j = 0; $j < ($largest - strlen($which[$i])) ; $j++){
                $appendStr .= '0';
            }
            $which[$i] = $appendStr . $which[$i];
        }
        return $which;
    }

/*============================================================================================*/




/********************************************************************

Sets the Cookie after creating the token by mismatching the base
converted ip, time stamp, user id and username. 

append the checksum to the token

********************************************************************/

    function setTheCookie($username,$user_id,$positionArray){
        //Date Creation
        //setCookie('token','',time()-36000);
        $date = date_create();
        $currentTimeStamp = date_timestamp_get($date);

        //IP Mgmt
        $currentIp = $_SERVER['REMOTE_ADDR'];
        $currentIp = str_replace('.','',$currentIp);
        
        //base converting all the data
        $converted_ip = convertBase($currentIp);
        $converted_timestamp = convertBase($currentTimeStamp);
        $converted_username = convertBase($username);
        $converted_userid = convertBase($user_id);

        //CheckSum
        $converted_checksum = sha1(convertBase($username.$user_id));    

        //making an array with all the converted data
        $Arr = array($converted_ip,$converted_timestamp,$converted_userid,$converted_username);

        //Appending Zeros
        $Arr = addZeros($Arr);
        
        //Making the Token
        $token = makeToken($Arr,$converted_checksum,$positionArray);
     
        //Setting the cookie
        if(strlen($_COOKIE['token']) <= 0){
            setCookie('token',$token,time()+3600,"https://nova.it.rit.edu/~nxa2762","rit.edu");
        } 
    }
    
    //Fetching from the cookie
    //$tokenFromCookie = $_COOKIE['token'];

    //Splitting the token to check for everything
    //breakToken($tokenFromCookie,$posArray,$username,$user_id,$currentIp,$currentTimeStamp);

    /*********************  Methods ********************/

    function fetchNameId($newToken,$positions){
        $tokenLength = strlen($newToken);
        $singleLen = ($tokenLength-40)/8;
        $user_id = substr($newToken, 2*$singleLen, $singleLen).substr($newToken, 2*$singleLen+4*$singleLen+40,$singleLen);
        $user_id = base_convert($user_id, 10, 36);
        $username = substr($newToken, 3*$singleLen, $singleLen).substr($newToken, 3*$singleLen+4*$singleLen+40,$singleLen);
        $username = base_convert($username, 10, 36);
        $user = array($username,$user_id);
        return $user;
    }

/********************************************************************

Break the token for checking for checksum and the token

********************************************************************/

    function breakToken($newToken,$positions,$username,$user_id,$ip,$ts){
        //base converting all the data
        $ip = str_replace('.','',$ip);
        $converted_ip = convertBase($ip);
        $converted_timestamp = convertBase($ts);
        $converted_username = convertBase($username);
        $converted_userid = convertBase($user_id);
        $oldArr = array($converted_ip,$converted_timestamp,$converted_userid,$converted_username);

        //Appending Zeros
        $oldArr = addZeros($oldArr);

        //CheckSum
        $converted_checksum = sha1(convertBase($username.$user_id));
        $tokenLen = (strlen($newToken)-40);
        $eachCompLen = $tokenLen/8;
        $start = $tokenLen/2;
        $token_checksum = substr($newToken, $start,40);
        if(strcmp($converted_checksum , $token_checksum) == 0){
            for($i=0; $i < count($positions)/2; $i++){
                $time = joinAndCompare($newToken,($i*($eachCompLen)),$eachCompLen,$oldArr[$i]);
				if(($time == -1 || $time > 36000) && $time != 0){
					setCookie("token","",time()-36000,"http://nova.it.rit.edu/~nxa2762","rit.edu"); 
                    header('Location: http://nova.it.rit.edu/~nxa2762/546/project');
                }
            }
        }
        else{
            setCookie("token","",time()-36000,"http://nova.it.rit.edu/~nxa2762/","rit.edu"); 
            header('Location: http://nova.it.rit.edu/~nxa2762/546/project');
        }
    }

/********************************************************************

Fetches the time spent since login

********************************************************************/

    function loginTime($str1,$str2){
        $logTime = base_convert($str2,10,36) - base_convert($str1,10,36);
        return $logTime;
    }

/********************************************************************

Breaks the token apart and checks for the time spent on the system

********************************************************************/

    function joinAndCompare($tokenString,$first,$len,$enStr){
        $firstPart = substr($tokenString, $first, $len);
        $secondPart = substr($tokenString, $first+4*$len+40, $len);
        $singleStr = $firstPart.$secondPart;
        $boo = strcmp($enStr,$singleStr);
        $login_time = 0;
        if($first == 10 && $boo > 0){
            $login_time = loginTime($singleStr,$enStr);
        }elseif ($boo != 0) {
            $login_time = '-1';
        }
        return $login_time;
    }


/********************************************************************

Splits the base converted input into 2 halves. 

********************************************************************/

    function splitArray($whatString){
        $half = (int) ( (strlen($whatString) / 2) ); // (int) incase str length is odd
        $left = substr($whatString, 0, $half);
        $right = substr($whatString, $half);
        $splitArray = array($left,$right);
        return $splitArray;
    }



/********************************************************************

Concatenates different parts to along with checksum to make the token

********************************************************************/

    function makeToken($someArray,$someSum,$positions){
        $mashedArray = array();
        for($i=0;$i<count($someArray);$i++){
            $tempArr = splitArray($someArray[$i]);
            $mashedArray[] = $tempArr[0];
            $mashedArray[] = $tempArr[1];
        }

        $token='';
        for($i=0;$i<count($positions);$i++)
        {
            $token .= $mashedArray[$positions[$i]];
            if($i == 3){
                $token .= $someSum;
            }
        }
        return $token;
    }

?>
