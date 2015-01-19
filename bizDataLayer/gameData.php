<?php
	//include exceptions
	require_once('./bizDataLayer/exception.php');
	require_once("../../../dbInfo.inc"); 
	require_once('bizDataLayer/winCondition.php');
 
	
	//set time zone
	date_default_timezone_set('America/New_York');

	//include jsonUtil
	require_once('./bizDataLayer/jsonUtil.php');
	$board_state = "0~0~0~0~0~0~0~0|0~0~0~0~0~0~0~0|0~0~0~0~0~0~0~0|0~0~0~0~0~0~0~0|0~0~0~0~0~0~0~0|0~0~0~0~0~0~0~0|0~0~0~0~0~0~0~0";

/********************************************************************

BizDataLayer Methods

********************************************************************/
function createGameData($g,$p1,$p2){
		global $board_state;
		global $mysqli;
		$p1 = (int)$p1;
		$p2 = (int)$p2;
		$g = (int)$g;
		$sql = "INSERT INTO c4_game (game_id,player1_id,player2_id,game_state,turn,last_piece,board_state,winner) values (?,?,?,1,?,'X',?,0)";
		$result = '';
		try{
			if($stmt=$mysqli->prepare($sql)){
			//would bind here if needed
			$stmt->bind_param("iiiis",$g,$p1,$p2,$p1,$board_state);
			$stmt->execute();
			$stmt->close();
			//$mysqli->close();
			}else{
				throw new Exception("An error occured while fetching record data");
			}
		}
		catch(Exception $e){
			log_error($e,$sql,null);
			$result = json_encode(array('Fail'));
		}
		$sql = "UPDATE c4_users SET room_id = ? WHERE user_id = ?";
		$result = '';
		try{
			if($stmt=$mysqli->prepare($sql)){
			//would bind here if needed
			$stmt->bind_param("ii",$g,$p1);
			$stmt->execute();
			$stmt->close();
			//$mysqli->close();
			}else{
				throw new Exception("An error occured while fetching record data");
			}
		}
		catch(Exception $e){
			log_error($e,$sql,null);
			$result = json_encode(array('Fail'));
		}
		$sql = "UPDATE c4_users SET room_id = ? WHERE user_id = ?";
		$result = '';
		try{
			if($stmt=$mysqli->prepare($sql)){
			//would bind here if needed
			$stmt->bind_param("ii",$g,$p2);
			$stmt->execute();
			$stmt->close();
			$mysqli->close();
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

Gets the game from the database

********************************************************************/

	function getGameData($g){
		global $mysqli;
		$g = (int)$g;
		$sql = "SELECT u1.username as p1,u2.username as p2,g.player1_id as p1_id,g.player2_id as p2_id,g.turn FROM c4_users u1,c4_users u2,c4_game g WHERE g.player1_id = u1.user_id AND g.player2_id = u2.user_id AND g.game_id = ?";
		$result = '';
		try{
			if($stmt=$mysqli->prepare($sql)){
			//would bind here if needed
			$stmt->bind_param("i",$g);
			$result = returnJson($stmt);
			$stmt->close();
			$mysqli->close();
			}else{
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

Gets the turn 

********************************************************************/

	function getTurnData($g){
		global $mysqli;
		$g = (int)$g;

		$sql = "SELECT turn FROM c4_game WHERE game_id = ?";
		$result = '';
		try{
			if($stmt=$mysqli->prepare($sql)){
			//would bind here if needed
			$stmt->bind_param("i",$g);
			$result = returnJson($stmt);
			$stmt->close();
			$mysqli->close();
			}else{
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

Sets the turn for the particular game after the player makes a move

********************************************************************/

	function setTurnData($g,$pid){
		global $mysqli;
		$g = (int)$g;
		$pid = (int)$pid;
		$sql = "UPDATE c4_game SET turn = ? WHERE game_id = ?";
		$result = '';
		try{
			if($stmt=$mysqli->prepare($sql)){
			//would bind here if needed
			$stmt->bind_param("ii",$pid,$g);
			$stmt->execute();
			//$result = returnJson($stmt);
			$result = json_encode(array('Done'));
			$stmt->close();
			$mysqli->close();
			}else{
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

Sends the new board after the player makes a move & checks for the 
winner!

********************************************************************/

	function setBoardData($pieceId,$i,$j,$gameId){
		global $mysqli;
		
		$pieceId = (string)$pieceId;
		$i = (int)$i;
		$j = (int)$j;
		$gameId = (int)$gameId;
		

		$sql = "SELECT board_state from c4_game where game_id = ?";
		$result = '';
		try{
			if($stmt=$mysqli->prepare($sql)){
				$stmt->bind_param("i",$gameId);
				$stmt->execute();
				$stmt->bind_result($exBoard);
				$stmt->fetch();
				$rows = explode('|',$exBoard);

				$i = $i-1;
				$piece = explode('|',$pieceId);
				$player = substr($piece[0],6);
				$pieceId .= '|'.$i.'|'.$j;
				
				$col = explode('~',$rows[$i]);
				
				$col[$j] = $player;
				
				$rows[$i] = implode('~',$col);
				$newBoard = implode('|',$rows);
				$newBoard = (string)$newBoard;
				$stmt->close();
				$insertSql = "UPDATE c4_game SET board_state = ?,last_piece = ? WHERE game_id = ?";
				$newResult = '';
				try{
			
					if($newStmt=$mysqli->prepare($insertSql)){
						$newStmt->bind_param("ssi",$newBoard,$pieceId,$gameId);
						$newStmt->execute();
						$newResult = json_encode(array('done'));
						$newStmt->close();
					}else{
						throw new Exception("An error occured while fetching record data");
					}
				}catch(Exception $e){
					log_error($e,$insertSql,null);
					$newResult = json_encode(array('new Fail'));
				}	
				
				$mysqli->close();
			}else{
				throw new Exception("An error occured while fetching record data");
			}
		}
		catch(Exception $e){
			log_error($e,$sql,null);
			$result = json_encode(array('Fail'));
			return $result;
		}
		$win = 0;

		$win = didIWin($newBoard, $pieceId);
		$result = json_encode(array($win));
		return $result;
	}
	
/********************************************************************

Gets the board from the database

********************************************************************/

	function getBoardData($g){
		global $mysqli;
		$g = (int)$g;
		
		$sql = "SELECT * FROM c4_game WHERE game_id = ?";
		$result = '';
		try{
			if($stmt=$mysqli->prepare($sql)){
				$stmt->bind_param("i",$g);
				$result = returnJson($stmt);
				$stmt->close();
				$mysqli->close();
			}else{
				throw new Exception("An error occured while fetching record data");
			}
		}catch(Exception $e){
			log_error($e,$insertSql,null);
			$result = json_encode(array('Fail'));
		}
		return $result;
	}

/********************************************************************

Sets the winner field in the database

********************************************************************/

	function setWinnerData($w,$g){
		global $mysqli;
		$w = (int)$w;
		$g = (int)$g;
		$sql = "UPDATE c4_game SET winner = ?,game_state = 2 where game_id = ?";
		$result = '';
		try{
			if($stmt=$mysqli->prepare($sql)){
				$stmt->bind_param("ii",$w,$g);
				$stmt->execute();
				//$result = json_encode(array('Done'));
				$stmt->close();
				$sql2 = "SELECT u.username from c4_users u, c4_game c where c.winner = u.user_id and c.winner = ? and c.game_id = ?";
				try{
					if($stmt2=$mysqli->prepare($sql2)){
						$stmt2->bind_param("ii",$w,$g);
						$result = returnJson($stmt2);
						$stmt2->close();
					}else{
						throw new Exception("An error occured while fetching record data");
					}
				}catch(Exception $e){
					log_error($e,$sql2,null);
					$result = json_encode(array('Fail'));
				}
			}else{
				throw new Exception("An error occured while fetching record data");
			}
		}catch(Exception $e){
			log_error($e,$sql,null);
			$result = json_encode(array('Fail'));
		}

		$sql = "UPDATE c4_challenge SET challenge_status = 2 WHERE challenge_id=?";
		try{
			if($stmt=$mysqli->prepare($sql)){
				$stmt->bind_param("i",$g);
				$stmt->execute();
				$stmt->close();
			}else{
				throw new Exception("An error occured while fetching record data");
			}
		}catch(Exception $e){
			log_error($e,$sql,null);
			$result = json_encode(array('Fail'));
		}
		return $result;
	}

/********************************************************************

Gets the winner, if the opponent won the game

********************************************************************/

	function getWinnerData($g){
		global $mysqli;
		$g = (int)$g;
		$sql = "SELECT winner FROM c4_game WHERE game_id = ?";
		$result = '';
		try{
			if($stmt=$mysqli->prepare($sql)){
				$stmt->bind_param("i",$g);
				$stmt->execute();
				$stmt->bind_result($winner);
				$stmt->fetch();
				$stmt->close();
				$result = json_encode(array('0'));
				if($winner > 0){
					$sql2 = "SELECT username FROM c4_users where user_id = ?";
					try{
						if($stmt = $mysqli->prepare($sql2)){
							$stmt->bind_param("i",$winner);
							$result = returnJson($stmt);
							$stmt->close();
							$mysqli->close();
						}else{
							throw new Exception("An error occured while fetching record data");
						}
					}catch(Exception $e){
						log_error($e,$sql2,null);
						$result = json_encode(array('Fail'));
					}
				}	
			}else{
				throw new Exception("An error occured while fetching record data");
			}
		}catch(Exception $e){
			log_error($e,$sql,$null);
			$result=json_encode(array('Fail'));
		}
		return $result;
	}

/********************************************************************

Sets the room id for chat back to public room after the game is 
completed

********************************************************************/
	
	function setRoomData($uid){
		global $mysqli;
		$uid = (int)$uid;
		$sql = "UPDATE c4_users SET room_id = 0 WHERE user_id = ?";
		try{
			if($stmt=$mysqli->prepare($sql)){
				$stmt->bind_param("i",$uid);
				$stmt->execute();
				$stmt->close();
				$mysqli->close();
			}else{
				throw new Exception("An error occured while updating record data");
			}
		}catch(Exception $e){
			log_error($e,$sql,null);
			$result = json_encode(array('Fail'));
		}
	}
	
/********************************************************************

Count the # of games the user won and lost and update the user table

********************************************************************/

	function getWinLossData($u){
		global $mysqli;
		$u = (int)$u;

		$sql = "UPDATE c4_users SET wins = (SELECT COUNT(*) FROM c4_game WHERE (player1_id = winner OR player2_id = winner) AND winner = ?) WHERE user_id = ?";
		try{
			if($stmt=$mysqli->prepare($sql)){
				$stmt->bind_param("ii",$u,$u);
				$stmt->execute();
				$stmt->close();
				$result = json_encode(array('Done1'));
			}else{
				throw new Exception("An error occured while updating record data");
			}
		}catch(Exception $e){
			log_error($e,$sql,null);
			$result = json_encode(array('Fail1'));
		}

		$sql = "UPDATE c4_users SET losses = (SELECT COUNT(*) FROM c4_game WHERE (player1_id = winner OR player2_id = winner) AND winner <> ?) WHERE user_id = ?";
		try{
			if($stmt=$mysqli->prepare($sql)){
				$stmt->bind_param("ii",$u,$u);
				$stmt->execute();
				$stmt->close();
				$mysqli->close();
				$result = json_encode(array('Done2'));
			}else{
				throw new Exception("An error occured while updating record data");
			}
		}catch(Exception $e){
			log_error($e,$sql,null);
			$result = json_encode(array('Fail2'));
		}
		return $result;	
	}

/********************************************************************

Set Winner if the opponent boycotts

********************************************************************/
 
	function setWinnerBoycottData($u,$g){
		global $mysqli;
		$sql = "SELECT player1_id,player2_id FROM c4_game WHERE game_id = ?";
		try{
			if($stmt=$mysqli->prepare($sql)){
				$stmt->bind_param("i",$g);
				$stmt->execute();
				$stmt->bind_result($p1,$p2);
				$stmt->fetch();
				$stmt->close();
			}else{
				throw new Exception("An error occured while updating record data");
			}
		}catch(Exception $e){
			log_error($e,$sql,null);
			$result = json_encode(array('Fail1'));
		}
		if($p1 == $u){
			$w = $p2;
		}else{
			$w = $p1;
		}

		$sql = "UPDATE c4_game SET winner = ? WHERE game_id = ?";
		try{
			if($stmt=$mysqli->prepare($sql)){
				$stmt->bind_param("ii",$w,$g);
				$stmt->execute();
				$stmt->close();
				$mysqli->close();
				$result = json_encode(array('Done'));
			}else{
				throw new Exception("An error occured while updating");
			}
		}catch(Exception $e){
			log_error($e,$sql,null);
			$result = json_encode(array('Fail2'));
		}
		return $result;
	}
