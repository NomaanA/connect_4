<?php
	//include dbInfo
	require_once("../../../dbInfo.inc"); 
	


	require_once('bizDataLayer/exception.php');
	require_once('bizDataLayer/jsonUtil.php');
	require_once('token_svc.php');
/*
    $username = "nxa2762"; 
    $password = "qwerty"; 
    $host = "localhost"; 
    $dbname = "nxa2762"; 
 
    $mysqli=new mysqli($host,$username,$password,$dbname);             
    // database link for connection to database
    if(mysqli_connect_errno()){
        printf("connection failed: ",mysqli_connect_errno());
        exit();
    }

	*/


	date_default_timezone_set('America/New_York');

	

/********************************************************************

Gets the chat data based on the room id

********************************************************************/

	function getChatData($room_id){
		global $mysqli;
		$room_id = (int)$room_id;
		$sql = "select c.message,c.time_stamp,u.username,c.room_id,u.user_id from c4_chat c, c4_users u where c.room_id = ? and c.player_id = u.user_id ORDER BY time_stamp ASC ";
		$result = '';
		try{
			if($stmt=$mysqli->prepare($sql)){
				//would bind here if needed
				$stmt->bind_param('i',$room_id);
				$result =  returnJson($stmt);
				$stmt->close();
				$mysqli->close();
			}
			else{
				throw new Exception("An error occured while fetching record data");
			}
		}
		catch(Exception $e){
			log_error($e,$sql,null);
			$result = json_encode(array('Fail'));
		}
		return $result;
	}

/********************************************************************

Sets the new chat

********************************************************************/

	function setChatData($msg,$room,$id,$ts){
		global $mysqli;
		$sql = "INSERT INTO c4_chat (room_id,player_id,message,time_stamp) VALUES (?,?,?,?)";
		$result = '';
		try{
			if($stmt=$mysqli->prepare($sql)){
				$room = (int)$room;
				$id = (int)$id;
				$msg = htmlspecialchars((string)$msg);
				$ts = (string)$ts;
				$stmt->bind_param("iiss",$room,$id,$msg,$ts);
				$stmt->execute();
				
				$result = json_encode(array($room,$id,$msg,$ts));

				$stmt->close();
				
				//$result = json_encode(array('Done'));
			}
			else{
				throw new Exception("An error occured while fetching record data");
			}
		}
		catch(Exception $e){
			log_error($e,$sql,null);
			$result = json_encode(array('Fail'));
		}
		$mysqli->close();
		return $result;
	}

/********************************************************************

Gets the online users for the public chat

********************************************************************/

	function getOnlineUsers($room_id){
		global $mysqli;
		$sql = "SELECT username,user_id,wins,losses FROM c4_users where room_id = ?";
		$result = '';
		try{
			if($stmt=$mysqli->prepare($sql)){
				$room_id = (int)$room_id;
				
				//would bind here if needed
				$stmt->bind_param("i",$room_id);
				$result =  returnJson($stmt);
				$stmt->close();
				$mysqli->close();
			}
			else{
				throw new Exception("An error occured while fetching record data");
			}
		}
		catch(Exception $e){
			log_error($e,$sql,null);
			$result = json_encode(array('Fail'));
		}
		return $result;
	}
?>