<?php
	//include dbInfo
	require_once("../../../dbInfo.inc"); 
	
	//include exceptions
	require_once('bizDataLayer/exception.php');
	require_once('bizDataLayer/jsonUtil.php');
	
	date_default_timezone_set('America/New_York');
 

/********************************************************************

Sets a new challenge with challenge status as waiting

********************************************************************/

	function setChallengeData($p1,$p2_id,$p2_name,$challenge_status){
		global $mysqli;
		$p1 = (int)$p1;
		$p2 = (int)$p2_id;
		$challenge_status = (int)$challenge_status;
		$sql = "INSERT INTO c4_challenge (challenge_id,challenger_id,challenged_id,challenge_status) values (NULL,?,?,?)";
		$checkSql = "SELECT challenge_status FROM c4_challenge WHERE challenged_id = ? AND ( challenge_status = 1 OR challenge_status = 0 )";
		$checkResult = '';
		$result = '';
		try{
			if($ckStmt = $mysqli->prepare($checkSql)){
				$ckStmt->bind_param("i",$p2_id);
				$ckStmt->execute();
				$ckStmt->store_result();
				$count = $ckStmt->num_rows;
				$ckStmt->close();
				if($count != 0){
					$result = json_encode(array("Busy",$p2_name));
				}else{
					if($stmt=$mysqli->prepare($sql)){
					//would bind here if needed
					$stmt->bind_param("iii",$p1,$p2_id,$challenge_status);
					$stmt->execute();
					$newId = $mysqli->insert_id;
					$result = json_encode(array($p2_name,$newId));
					$stmt->close();
					$mysqli->close();
					}else{
						throw new Exception("An error occured while fetching record data");
					}
				}
			}else{
				throw new Exception("An error occured while fetching record data");
			}
		}
		catch(Exception $e){
			log_error($e,$sql,null);
			$result = json_encode(array('Fail'));
		}
		echo $result;
	}

/********************************************************************

Gets challenges with challenges that are waiting for a player

********************************************************************/

	function getChallengeData($p1){
		global $mysqli;
		$p1 = (int)$p1;
		$sql = "SELECT u.username,u.user_id,c.challenge_id FROM c4_users u, c4_challenge c WHERE c.challenge_status = 0 AND u.user_id = c.challenger_id AND u.room_id = 0 AND c.challenged_id = ?";
		$result = '';
		try{
			if($stmt=$mysqli->prepare($sql)){
				//would bind here if needed
				$stmt->bind_param("i",$p1);
				$stmt->execute();
				$stmt->store_result();
				$count = $stmt->num_rows;
				if($count > 0){
					$result = returnJson($stmt);
				}else{
					$result = $count;
				}
				$stmt->close();
				$mysqli->close();
			}
			else{
				throw new Exception("An error occured while fetching record data");
			}
		}
		catch(Exception $e){
			log_error($e,$sql,null);
			$result = 'Fail';
		}	
		echo $result;
	}

/********************************************************************

Changes the challenge status based on the user response, 

1 - Accepted
-1 - Declined
0 - Waiting
2 - Game Done

********************************************************************/

	function setChallengeStatusData($challenge_id,$challenge_status){
		global $mysqli;
		$challenge_id = (int)$challenge_id;
		$challenge_status = (int)$challenge_status;
		$sql = "UPDATE c4_challenge SET challenge_status = ? WHERE challenge_id = ?";
		$result = '';
		try{
			if($stmt=$mysqli->prepare($sql)){
				//would bind here if needed
				$checkSql = "SELECT challenge_status FROM c4_challenge WHERE challenge_id = ?";
				if($stmt1=$mysqli->prepare($checkSql)){
					$stmt1->bind_param("i",$challenge_id);
					$stmt1->execute();
					$stat = returnJson($stmt1);
					if(($stat['challenge_status'] == 0 && $challenge_status == 1) || ($stat['challenge_status'] == 0 && $challenge_status == -1)){
						$stmt->bind_param("ii",$challenge_status,$challenge_id);
						$stmt->execute();
						$result = $challenge_status;
						$stmt->close();
					}else if ($stat['challenge_status'] == -1){
						$result = $stat['challenge_status'];
					}

					$stmt1->close();
					$mysqli->close();
				}
				else{
					throw new Exception("An error occured while fetching record data");
				}
			}
			else{
				throw new Exception("An error occured while fetching record data");
			}
		}
		catch(Exception $e){
			log_error($e,$sql,null);
			$result = 'Fail';
		}
		return $result;
	}

/********************************************************************

Gets the challenge status

********************************************************************/

	function getChallengeStatusData($challenge_id){
		global $mysqli;
		$challenge_id = (int)$challenge_id;
		$sql = "SELECT challenge_id,challenge_status,challenger_id,challenged_id FROM c4_challenge WHERE challenge_id = ?";
		$result = '';
		try{
			if($stmt=$mysqli->prepare($sql)){
				//would bind here if needed
				$stmt->bind_param("i",$challenge_id);
				$result = returnJson($stmt);
				$stmt->close();
				$mysqli->close();
			}
			else{
				throw new Exception("An error occured while fetching record data");
			}
		}
		catch(Exception $e){
			log_error($e,$sql,null);
			$result = 'Fail';
		}
		return $result;
	}
?>