<?php
//What do we do here?
//Check if they should be here!
//if so - prepare data and make call to data/biz layer

//error_reporting (E_ALL);
require "./bizDataLayer/gameData.php";
require_once('./token_svc.php');
date_default_timezone_set('America/New_York');
//Why include the database stuff here?  (not doing any db stuff in the service layer!)
//because it forces all to go through the service layer in order to get to the bizLayer
//if someone tries to access the bizLayer on it's own the code will fail since there isn't a connection!
require_once("../../../dbInfo.inc"); //to use we need to put in: global $mysqli;
//remember that dbInfo.inc looks like:
/*
$mysqli=new mysqli("localhost","yourUsername","yourPass",'yourUsername');             
if(mysqli_connect_errno()){
	printf("connection failed: ",mysqli_connect_errno());
	exit();
}
*/

/*************************
	start
	takes: 		gameId
	uses in bizLayer: gameBiz.php->startData
	returns:	gameInfo
				[{"game_id":38,"whoseTurn":1,"player0_name":"Dan","player0_pieceID":null,"player0_boardI":null,"player0_boardJ":null,"player1_name":"Fred","player1_pieceID":null,"player1_boardI":null,"player1_boardJ":null,"last_updated":"0000-00-00 00:00:00"}]
*/


function start($d){
	//Should they be here?  (check)
	//if true:
	return startData($d);
}
/*************************
	changeTurn
	takes: gameId
	uses in bizLayer: gameBiz.php->changeTurnData
	returns:	Nothing
*/
function changeTurn($d){
	//can they change the turn?
	//if true:
	changeTurnData($d);
}
/*************************
	checkTurn
	takes: gameId
	uses in bizLayer: gameBiz.php->checkTurnData
	returns:	whoseTurn
				[{"whoseTurn":1}]
*/
function checkTurn($d){
	//Can they check is it my turn yet?
	//if true:
	return checkTurnData($d);
	
}
/*************************
	changeBoard
	takes: gameId~pieceId~boardI~boardJ~playerId
	uses in bizLayer: gameBiz.php->changeBoardData
	returns:	Nothing
*/
function changeBoard($d){
	//can they change the board?
	//if true:
	//split the data  //data: gameId~pieceId~boardI~boardJ~playerId
							//38~piece_1|10~4~6~1
	$h=explode('~',$d);
	//changeBoardData($gameId,$pieceId,$boardI,$boardJ,$playerId);
	changeBoardData($h[0],$h[1],$h[2],$h[3],$h[4]);
}
/*************************
	getMove
	takes: gameId
	uses in bizLayer: gameBiz.php->getMoveData
	returns:	gameInfo
				[{"game_id":38,"whoseTurn":1,"player0_name":"Dan","player0_pieceID":"piece_0|10","player0_boardI":"6","player0_boardJ":"2","player1_name":"Fred","player1_pieceID":"piece_1|3","player1_boardI":"0","player1_boardJ":"2","last_updated":"0000-00-00 00:00:00"}]
*/
function getMove($d){
	//if it is my turn and I should be here, get the other players move	
	return getMoveData($d);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/********************************************************************

Creates new game

********************************************************************/

    function createGame($d,$ip,$token){
        global $posArray;
        $date = date_create();
        $currentTimeStamp = date_timestamp_get($date);
        $userArray = fetchNameId($token,$posArray);
        $player1 = $userArray[1];
        
        list($game_id, $player1_id, $player2_id) = explode("|", $d);

        breakToken($token,$posArray,$userArray[0],$userArray[1],$ip,$currentTimeStamp);

        return (createGameData($game_id,$player1_id,$player2_id));
    }


/********************************************************************

Gets the game with the player who challenged as player one

********************************************************************/

    function getGame($d,$ip,$token){
        global $posArray;
        
        $date = date_create();
        $currentTimeStamp = date_timestamp_get($date);
        $userArray = fetchNameId($token,$posArray);
        $player1 = $userArray[1];
        
        breakToken($token,$posArray,$userArray[0],$userArray[1],$ip,$currentTimeStamp);

        return (getGameData($d));   
    }


/********************************************************************

Gets the turn from the database after the opponent makes a move

********************************************************************/

    function getTurn($d,$ip,$token){
        global $posArray;
        $date = date_create();
        $currentTimeStamp = date_timestamp_get($date);
        $userArray = fetchNameId($token,$posArray);
        $player1 = $userArray[1];
        $game_id = $d;
        breakToken($token,$posArray,$userArray[0],$userArray[1],$ip,$currentTimeStamp);
        return (getTurnData($game_id)); 
    }
    
/********************************************************************

Sets the turn in the database after the player makes a move

********************************************************************/

    function setTurn($d,$ip,$token){
        global $posArray;
        $date = date_create();
        $currentTimeStamp = date_timestamp_get($date);
        $userArray = fetchNameId($token,$posArray);
        $player1 = $userArray[1];

		list($game, $newTurn) = explode('|',$d);

		breakToken($token,$posArray,$userArray[0],$userArray[1],$ip,$currentTimeStamp);


		return (setTurnData($game,$newTurn)); 
    }

/********************************************************************

Sets the board after the player makes a move

********************************************************************/

    function setBoard($d,$ip,$token){
        global $posArray;
        $date = date_create();
        $currentTimeStamp = date_timestamp_get($date);
        $userArray = fetchNameId($token,$posArray);
        $player1 = $userArray[1];
		
		list($pieceMoved, $i, $j, $gameId )= explode('~',$d);
        
        breakToken($token,$posArray,$userArray[0],$userArray[1],$ip,$currentTimeStamp);
		return (setBoardData($pieceMoved,$i,$j,$gameId)); 
    }

/********************************************************************

Gets the new board after the opponent makes a move

********************************************************************/

	function getBoard($d,$ip,$token){
        global $posArray;
        $date = date_create();
        $currentTimeStamp = date_timestamp_get($date);
        $userArray = fetchNameId($token,$posArray);
        $player1 = $userArray[1];
		breakToken($token,$posArray,$userArray[0],$userArray[1],$ip,$currentTimeStamp);
		return (getBoardData($d)); 
    }

/********************************************************************

Sets the winner based on the game conditions

********************************************************************/

    function setWinner($d,$ip,$token){
        global $posArray;
        $date = date_create();
        $currentTimeStamp = date_timestamp_get($date);
        $userArray = fetchNameId($token,$posArray);
        $player1 = $userArray[1];
    	list($winner, $gameId)= explode('|',$d);
    	
		//breakToken($token,$posArray,$userArray[0],$userArray[1],$ip,$currentTimeStamp);
		return (setWinnerData($winner,$gameId));
    }

/********************************************************************

Gets the game winner

********************************************************************/
 
    function getWinner($d,$ip,$token){
        global $posArray;
        $data = date_create();
    	$currentTimeStamp = date_timestamp_get($date);
    	$userArray = fetchNameId($token,$posArray);
    	$gameId = $d;
		//breakToken($token,$posArray,$userArray[0],$userArray[1],$ip,$currentTimeStamp);
		echo (getWinnerData($gameId));
    }

/********************************************************************

Changes the room id in the user table as the game id for private chat

********************************************************************/

    function setRoom($d,$ip,$token){
        global $posArray;
        $data = date_create();
    	$currentTimeStamp = date_timestamp_get($date);
    	$userArray = fetchNameId($token,$posArray);
    	$userId = $d;
        breakToken($token,$posArray,$userArray[0],$userArray[1],$ip,$currentTimeStamp);
		return (setRoomData($userId));
    }

/********************************************************************

Gets the total # of wins and losses for a player

********************************************************************/

    function getWinLoss($d,$ip,$token){
        global $posArray;
        $date = date_create();
        $currentTimeStamp = date_timestamp_get($date);
        $userArray = fetchNameId($token,$posArray);
        $player1 = $userArray[1];
        $userId = $d;
        breakToken($token,$posArray,$userArray[0],$userArray[1],$ip,$currentTimeStamp);
        return getWinLossData($userId);
    }

/********************************************************************

Sets the player who boycotted the game as a loser

********************************************************************/

    // function ByBoycott($d,$ip,$token){
    //     global $posArray;
    //     $date = date_create();
    //     $currentTimeStamp = date_timestamp_get($date);
    //     $userArray = fetchNameId($token,$posArray);
    //     $player1 = $userArray[1];
 

    //     list($userid, $gameid) = explode('|', $d);
        
    //     breakToken($token,$posArray,$userArray[0],$userArray[1],$ip,$currentTimeStamp);
    //     return setWinnerBoycottData($userId,$gameId);   
    // }



?>