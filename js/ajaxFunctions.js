 
/* Golobal Var */
var challengeStatusInterval;

////////////////////////////////////////////
//////////     NOT AJAX 	////////////////
////////////////////////////////////////////

function countdown(whoseTimer,secs){
	var sec = secs;
    //$("#"+whoseTimer+"-timer").text(secs);
    $("#"+whoseTimer+"-timer").text(secs);
    timer = setInterval(function(){
        $("#"+whoseTimer+"-timer").text(sec--);
        if(sec == -1){
            sec = secs - 1;
            clearInterval(timer);
        }
    },1000);
}

 scrollChatBox = function(){

	$("#chatWindow").scrollTop($("#chatWindow")[0].scrollHeight);
}

///////////////////////////////////////////


function ajaxCall(GetPost,d,callback){
	$.ajax({
 		type: GetPost,
 		async: true, 
  		cache:false,
  		url: "mid.php",
  		data: d,  
  		dataType: "json",
  		success: callback
	});
}
	

/***************************
		GET CHAT
***************************/

function getChat(uData){
    ajaxCall('POST',{a:'chat',method:'getChat',data:uData},getChatCallback);
}



/***************************
    getChatCallback
***************************/

function getChatCallback(data){
    //var h ='';
    document.getElementById('chatWindow').innerHTML = '';
    
    if(data != null){
        //making the table
        var tableEle = "<table class='table table-bordered'>";
        
        //adding chat entries as <tr?
        for(var i=0,l=data.length;i<l;i++){
           
            name  = data[i].username+' :';
            messageText = data[i].message;
            timeStamp = data[i].time_stamp;

		    if(userid == data[i].user_id){
				//to show messages from other online users
				tableEle += "<tr><td><p><strong>"+name+"</strong><span class=''>"+messageText+"</span><span class='pull-right'><small>"+timeStamp+"</small></span></p></td></tr>";
		    }else{
				//will show the messages from you in different co]or, class info of bootstrap which is light blue. 
		    	tableEle += "<tr class='info'><td class='messageBox'><p><strong>"+name+"</strong><span class=''>"+messageText+"</span><span class='pull-right'><small>"+timeStamp+"</small></span></p></td></tr>";
		    }
		    $("#chatWindow").html(tableEle);
        }

	scrollChatBox();

    }else{
        $("#chatWindow").html("<div class='margin'><p class='lead'>No Chat History Found...</p><p class='small'> Start chatting...</p></div>");
    }

    setTimeout(function(){getChat(roomid);},1500);
}


		
/***************************
    Get Online Users
***************************/

function getUsers(uData){
	
  ajaxCall('post',{a:'chat',method:'getUsers',data:uData},getOnlineUsersCallback);
}

/***************************
        Callback
***************************/

function getOnlineUsersCallback(data){
	
    document.getElementById('onlineUsers').innerHTML = '';

   
    
   	 

    if(data != null ){
        console.log(data);
    	//if the length of json object is one, meaning that it's only "You" then show there is no online user. 
		if(data.length <= 1){
			$('#onlineUsers').html("<p> NO ONLINE USER </p>");
		}

        for(var i=0,l=data.length;i<l;i++){
    	    if(userid != data[i]['user_id']){
        	 	// had to go oldfashion way since for onclick event, the single and double quotation would get crazy and not work
        		
        		//making a butt to challenge
        		var buttEle = document.createElement('a');

        		
        		buttEle.setAttribute('class','online-user btn btn-inverse chal-btn');

        		//to add the popover - doesn't work!!!!!!!!!!!
        		buttEle.setAttribute('rel','tooltip');
        		buttEle.setAttribute('data-toggle','tooltip');
        		buttEle.setAttribute('data-placement','top');
        		buttEle.setAttribute('data-original-title','click to challenge');


        		//user icon
	        	iEle = document.createElement('i');
        		iEle.setAttribute('class','icon-user icon-white');

        		//trophy icon
        		trophyEle = document.createElement('i');
        		trophyEle.setAttribute('class','icon-trophy icon-white');

        		//lost
        		lostEle = document.createElement('i');
        		lostEle.setAttribute('class','icon-ban-circle icon-white');
        	
        		buttEle.appendChild(iEle);
        		//userinfo
        		spanEle = document.createElement('span');
        		spanEle.setAttribute('id','player_'+data[i]['user_id']);
        		spanEle.appendChild(document.createTextNode('   '+data[i]['username']+',    '));

        		spanEle.appendChild(trophyEle);
        		spanEle.appendChild(document.createTextNode(data[i]['wins']+'  |  '));

        		spanEle.appendChild(lostEle);
        		spanEle.appendChild(document.createTextNode(''+data[i]['losses']));
        			
        		//adding onClick event to trigger the challenge	
        		spanEle.setAttribute('style','cursor:pointer;');
        		spanEle.setAttribute('onclick','setChallengeData("'+data[i]['user_id']+'|'+data[i]['username']+'|0")');

        		buttEle.appendChild(spanEle);
        		document.getElementById('onlineUsers').appendChild(buttEle);
         
    	    }else{
    	    	 $('#myScore').html("<span class='icon-trophy'>"+ data[i]['wins'] +"</span>&nbsp; |&nbsp; <span class='icon-ban-circle'>&nbsp;"+ data[i]['losses']);
            }
        }
    }
    
    setTimeout(getUsers,1000);
}

/***************************
        Setting chat 
***************************/

function setChat(msg){
    var uData = msg;
    ajaxCall('post',{a:'chat',method:'setChat',data:uData},setChatCallback);
}

/***************************
        setChatCallback
        -->scrolling the chat win
		-->refreshing chat. getting all the chat data from the database
***************************/

function setChatCallback(data){
    scrollChatBox();
    refreshChat();
}



/***************************
setChallengeData
***************************/

function setChallengeData(uData){
	
	ajaxCall('post',{a:'game',method:'setChallenge',data:uData},setChallengeCallback);
}

/***************************
setChallengeCallBack
-->after challenging a user
***************************/

function setChallengeCallback(data){
    
	//if challenged user is busy show a modal!
    if(data[0] == 'Busy'){
          
    	$('#modal').modal();

    	$('#modal').on('shown', function (event) {
	    	 
    		$("#modalLabel").html("<i class='icon-warning-sign'></i>");
	    	$(".modal-body").html( "<h1 class='text-center'> "+data[1]+" is busy!</h1>");
	    	setTimeout(function(){$('#modal').modal('hide');}, 3000);
		});

    	//killing the modal
		$('#modal').on('hidden', function (event) {
			$(this).data('modal', null);
		});

       
    }else{
       
   		$('#challengerBox').modal();

		$(function(){
            $("#modalLabelChallenger").html("<h4><img src='media/time.png'> waiting on "+data[0]+"</h4>");
    		$("#challengerText").html("<h4 class='text-center'>"+data[0]+" will decide in <span id='challenger-timer'></span> seconds.</h4>");
            countdown('challenger',20);
            challengeStatusInterval = setInterval(function(){getChallengeStatusData(data[1]);},1000);
            setTimeout(function(){$('#challengerBox').modal('hide');},20000);
        });
 
		//when the modal is faded out. either canceled or time was up set the challenge to -1
       	$('#modal').on('hidden', function (event) {
			$(this).data('modal', null);
			
			clearInterval(challengeStatusInterval);
            clearInterval(timer);
		    
		    var uData = data[1] + '|-1';//-1 means declined 
		    setChallengeStatusData(uData);
		});
		
    }
}


/***************************
    Get Challenge Status
***************************/

function getChallengeStatusData(uData){
    ajaxCall('post',{a:'game',method:'getChallengeStatus',data:uData},getChallengeStatusDataCallback);
}
 



/***************************
    getChallengeStatusDataCallback
    --> showing modal if the challenge was denied or accepted, etc
***************************/

function getChallengeStatusDataCallback(data){
    
    //if the challenge was denied or canceld, show a modal to the challenger 	
    if(data[0]['challenge_status'] == -1){
    	 	
		$("#modalLabelChallenger").html("<img src='media/time.png'>");
    	$("#challengerText").html("<h4>Your challenge was declined :(</h4>");
    	
    	clearInterval(challengeStatusInterval);
    	setTimeout(function(){$('#challengerBox').modal('hide');},3000);
         
    }else if(data[0]['challenge_status'] == 0){
        //still waiting for an answer
    }else{
    	 
    	createGame(data[0]['challenge_id']+'|'+data[0]['challenger_id']+'|'+data[0]['challenged_id']); 
		 
		$("#modalLabelChallenger").html("<img src='media/time.png'>");
    	$("#challengerText").html("<h1 class='text-center'>Challenge accepted!</h1>");

    	clearInterval(timer);
        clearInterval(challengeStatusInterval);
        setTimeout(function(){window.location = "http://nova.it.rit.edu/~nxa2762/546/project/game.php?game_id="+data[0]['challenge_id']},2000);
		    	
    }   
}




/***************************
    Set Challenge Status
***************************/

function setChallengeStatusData(uData){
    ajaxCall('post',{a:'game',method:'setChallengeStatus',data:uData},setChallengeStatusDataCallback);
}

/***************************
    setChallengeStatusDataCallback
***************************/

function setChallengeStatusDataCallback(data){
	console.log('setChallengeStatusDataCallback');
}



/***************************
    Get Challenge
    -->checking for challenges
***************************/

function getChallengeData(uData){
    ajaxCall('post',{a:'game',method:'getChallenge',data:uData},getChallengeCallback);
}

	 
/*************************** 
    getChallengeCallback
    -->
***************************/

function getChallengeCallback(data){
	

    if(data != 0){
   
        $('#challengeBox').modal('show');
     	
        $("#modalLabelChallenge").html("Let's play a game..");
        $("#challengeText").html("<h3 class='text-center'>You have been challenged by "+data[0]['username']+" <img src='media/game.png' height='63' width='94'/></h3>");
     	 
     	
     	//in case there was no answer
     	setTimeout(function(){
     		var uData = data[0]['challenge_id'] + '|-1';
		    setChallengeStatusData(uData);
     	},19000);

     	//when the modeal shows itself, do the followings
     	$('#challengeBox').on('shown', function (event) {
		   	
     		countdown('challenged',20);
     		setTimeout(function(){$('#challengeBox').modal('hide');},20000);

     		//when accepted
			$("#challenge-accepted").click(function(e) {
				e.preventDefault();
				
		     	$('#chatContainer').html("<img src='media/spin.gif' alt='spin' />");
		     	//$('#onlineUsers').html("<img src='media/spin.gif' alt='spin' />");

			   	//set the chal status
			   	var uData = data[0]['challenge_id'] + '|1';
			   	setChallengeStatusData(uData);

			  	var URL = "http://nova.it.rit.edu/~nxa2762/546/project/game.php?game_id="
	        	setTimeout(function(){window.location = URL+data[0]['challenge_id']},4000);
			
			});

			//when denied 
			$("#challenge-denied").click(function() {
			   
			    var uData = data[0]['challenge_id'] + '|-1';
			    setChallengeStatusData(uData);
			    $('#challengeBox').modal('hide');
			});

			//when canceled
			$("#closeButt").click(function() {
			   
			    var uData = data[0]['challenge_id'] + '|2';
			    setChallengeStatusData(uData);
			    $('#challengeBox').modal('hide');
			});
	    	 
		});

		$('#challengeBox').on('hidden', function (e) {
			
            var uData = data[0]['challenge_id'] + '|2';
			setChallengeStatusData(uData);

		});

    }else if(data = 0){
    	 //there is no challenge, keep calm and chat 
    }
}


	
/***************************
    getGame
***************************/
    
function getGame(uData){
    ajaxCall('post',{a:'game',method:'getGame',data:uData},getGameCallback);
}


/***************************
    getGameCallback
    -->
***************************/

function getGameCallback(data){

    turn = data[0]['turn'];
    if(userid == data[0]['p2_id']){
        player2 = data[0]['p1'];
        playerId = data[0]['p2_id'];
        player = data[0]['p2'];
        player2Id = data[0]['p1_id'];
    }else{
        player2 = data[0]['p2'];
        playerId = data[0]['p1_id'];
        player = data[0]['p1'];
        player2Id = data[0]['p2_id'];
    }
    
    gameInit();
}

/***************************
    Create Game
***************************/
function createGame(uData){
    ajaxCall('post',{a:'game',method:'createGame',data:uData},createGameCallback);
}

/***************************
    createGameCallback
***************************/
function createGameCallback(data){
	 
}



/***************************
		Get Turn		
***************************/
function getTurnData(uData){
	if(turn != playerId){
		ajaxCall('post',{a:'game',method:'getTurn',data:uData},getTurnCallback);
	}
	
	setTimeout(function(){getTurnData(gameId)},3000);
}

/***************************
    getTurnCallback
***************************/
var xTurn='';
function getTurnCallback(data){
	if(playerId == data[0]['turn']){
        turn = data[0]['turn'];
       	//document.getElementById('youPlayer').setAttribute("style","fill: red;font-size: 22px;font-weight: bold");
    	document.getElementById('opponentPlayer').setAttribute("style","fill: black;");
    	getBoardData(gameId);
    	getWinnerData(gameId);
	}
}

/***************************
		Set Turn		
***************************/
function setTurnData(uData){
	ajaxCall('post',{a:'game',method:'setTurn',data:uData},setTurnCallback);
}

/***************************
        setTurnCallback
***************************/
function setTurnCallback(data){

}



/***************************
		Set Board		
***************************/
function setBoardData(uData){
    ajaxCall('post',{a:'game',method:'setBoard',data:uData},setBoardCallback);
}

/***************************
        Callback
***************************/
function setBoardCallback(data){
    uData = data[0]+'|'+gameId;
    if(data[0] != '0'){
	   setWinnerData(uData);
    }
}



/***************************
	Set Winner		
***************************/
function setWinnerData(uData){
    ajaxCall('post',{a:'game',method:'setWinner',data:uData},setWinnerCallback);
}

/***************************
    setWinnerCallback
***************************/
function setWinnerCallback(data){
	 
  	$('#winner-modal').modal('show');
    
	setTimeout(function(){
	    $("#winner-modal").modal("hide");
	    window.location = "http://nova.it.rit.edu/~nxa2762/546/project/lobby.php";
	    setRoomData(userid);
	},5000); 
}

/***************************
		Get Winner
***************************/

function getWinnerData(uData){
    ajaxCall('post',{a:'game',method:'getWinner',data:uData},getWinnerCallback);
}

/***************************
    getWinnerCallback
    --> showing the loser modal
    -->changing the room number 
***************************/
function getWinnerCallback(data){
	if(data[0] != 0){
	  
		$("#loser-modal").modal('show');
		$("#loser-modal-body").html("<h4>You lost the game!</h4><h2>"+data[0]['username']+" won the game");
		
		setTimeout(function(){
		    $("#loser-modal").modal("hide");
		    setRoomData(userid);
		    window.location = "http://nova.it.rit.edu/~nxa2762/546/project/lobby.php";
		},5000);
    }
 
}


/***************************
        Set Room	
***************************/
function setRoomData(uData){
    ajaxCall('post',{a:'game',method:'setRoom',data:uData},setRoomCallback);
}

/***************************
        Callback
***************************/
function setRoomCallback(data){

}



/***************************
		Get Board		
***************************/
function getBoardData(uData){
	ajaxCall('post',{a:'game',method:'getBoard',data:uData},getBoardCallback);
}

/***************************
    getBoardCallback
***************************/
function getBoardCallback(data){
	if(data[0]['last_piece'] != 'X'){
		var brokenPiece = data[0]['last_piece'].split('|');
		var pieceToMove = brokenPiece[0] + '|' + brokenPiece[1];
		
		var temp = parseInt(brokenPiece[2])+1;
		
		var x = boardArr[temp][parseInt(brokenPiece[3])].getCenterX();
		var y = boardArr[temp][parseInt(brokenPiece[3])].getCenterY();
		setTransform(pieceToMove,x,y,1);
		getPiece(pieceToMove).changeCell('cell_'+temp+parseInt(brokenPiece[3]),temp,parseInt(brokenPiece[3]));
	}
}



/***************************
        Get Win Loss
***************************/

function getWinLossData(uData){
	console.log(uData);
    ajaxCall('post',{a:'game',method:'getWinLoss',data:uData},getWinLossCallback);
}

/***************************
        Callback
***************************/

function getWinLossCallback(data){
    
}


// /***************************
//         Winner by Boycott
// ***************************/

// function setWinnerByBoycott(data){
//     ajaxCall('post',{a:'game',method:'ByBoycott',data:uData},setWinnerByBoycottCallback);
// }

// /***************************
//         Callback
// ***************************/

// function setWinnerByBoycottCallback(data){
//     if(data[0] == "Done"){
//         alert('lost');
//     }
// }

 		
function setLogoutData(uData){
	ajaxCall('post',{a:'login',method:'setLogout',data:uData},setLogoutCallback);
}

function setLogoutCallback(data){
	window.location.href = 'http://nova.it.rit.edu/~nxa2762/546/project';
}


	 
