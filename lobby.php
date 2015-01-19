<?php
	
    if(strlen($_COOKIE['token']) <= 0 ){
        header( 'Location: http://nova.it.rit.edu/~nxa2762/546/project/index.php' );
    }else{
    	 
        require_once('token_svc.php');
        date_default_timezone_set('America/New_York');
        global $posArray;
        $token = $_COOKIE['token'];
        $userArray = fetchNameId($token,$posArray);
    }
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Lobby Chat</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Le styles -->
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <link href="//netdna.bootstrapcdn.com/font-awesome/3.1.1/css/font-awesome.css" rel="stylesheet">
    <style type="text/css">
      

      body {
        padding-top: 40px;
        padding-bottom: 40px;
        background-color: #f5f5f5;
      }

      #onlineUsers{
        margin: 0px 10px;
      }
      
      #chatContainer{
        position: relative;
        top: 50px;
        left: 30px;
        background-color: #F5F5F5;
      }

  		#chatWindow{
  			width: 600px;
  			max-height: 700px;
  			overflow: scroll;
  		}
  	 	
  	 	#chatMessage{
        position: relative;
  	 		width: 560px;
  	 	}

      #onlineUsers{
        position: fixed;
        top: 88px;
        left: -3px;

      }

      .chal-btn{
        display: block;
        max-width: 200px;
        margin: 5px 0px;
      }

      #chat-hint{
        width: 550px;
      }

      .span2{
        position: relative;
        top: 60px;
      }

      .span10{
        /*padding: 28px 0px;*/

      }

      @media all and (max-width: 1260) and (min-width: 670px){
        .span10{
          margin-top: 180px;
          margin-left: -133px;
          min-height: 100px;
        }
      }
      .spin{
        background-image: url('media/spin.gif');
      }
	 	 
		
    </style>
    <link href="assets/css/bootstrap-responsive.css" rel="stylesheet">

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="assets/js/html5shiv.js"></script>
    <![endif]-->

    <!-- Fav and touch icons -->
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="assets/ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="assets/ico/apple-touch-icon-114-precomposed.png">
      <link rel="apple-touch-icon-precomposed" sizes="72x72" href="assets/ico/apple-touch-icon-72-precomposed.png">
                    <link rel="apple-touch-icon-precomposed" href="assets/ico/apple-touch-icon-57-precomposed.png">
                                   <link rel="shortcut icon" href="assets/ico/favicon.png">
    
    
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script type="text/javascript" src="assets/js/bootstrap.js"></script>
    
    <script src="cookies.js"></script>
    <script type="text/javascript">
 	    

      /***********************************************************************************
  
                          CHAT UTILS
      ***********************************************************************************/


    	function refreshChat(tableEle){
            //speed up by selecting the div only once
            var chatWindow = $("#chatWindow");

            //get the height of the scroll (if any)
            var oldScrollH = chatWindow.attr("scrollHeight") - 20;
     
            //update the chatWindow
            chatWindow.html(tableEle);
            //get the heigth of the scroll after the update
            var newScrollH = chatWindow.attr("scrollHeight") - 20;
            if(newScrollH > oldScrollH)
            {
                //*move* the scroll down using an animation :)
                chatWindow.animate({scrollTop: newScrollH}, 1);
            }    
      }

      function sendChat(){
        var msg = document.getElementById('chatMessage').value;
        if(msg != ''){
            document.getElementById('chatMessage').value='';
            var uData = msg + "|0";
            setChat(uData);
        }
      }

   
    	/***********************************************************************************
    	
    	                     ON READY AND USER INFO
    	***********************************************************************************/
    	
    	//geting user information 
    	var username = '<?php echo $userArray[0]; ?>';
      var userid   = '<?php echo $userArray[1]; ?>';
    	var roomid   = 0 ;
    	 
    	$(document).ready(function(){
    	 console.log($(window).width());
       $("[rel=tooltip]").tooltip();  
        $('.online-user').tooltip();

    		setInterval(function(){getWinLossData(userid);},30000);
        
        getChat();
        getUsers('0');
        
        setInterval(function(){getChallengeData(userid);},1000);
        
        setTimeout(function(){
          $('.hint').fadeOut('slow');

        },3000)

    	});
     
     		
      function logout(){
          setLogoutData('<?php echo $userArray[1]; ?>');
      }
         
    </script>
    <script src="js/ajaxFunctions.js"></script>
</head>

<body>
  <div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
      <div class="container">
        <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="brand" href="#">Connect <span class="badge">4</span></a>
        <div class="nav-collapse collapse">
          <ul class="nav">
             
            
          </ul>
          <ul class='nav pull-right'>
            <li><a class='brand' href='#'><?php echo $userArray[0]; ?>, <span id='myScore'></span></a></li>
            <li><button style ='position: relative; top: 5px;' class='btn btn-danger btn-mini' onclick="logout()" ><a style='color: white;' >Logout</a></button></li>
          </ul>
           
        </div><!--/.nav-collapse -->
      </div>
    </div>
  </div>
     
    	
  <div class="container-fluid">
    <div class="row-fluid">
      <div class="span2">
     
        <div id="onlineUsers" class="contentAreas well"></div>
        
      </div>
      <div class="span10">
        <div id="mainContent">
          <div id='chatContainer'>
            
            <p id='chat-hint' class="hint lead alert alert-success">
                 You can chat here with other online users on the left
            </p>
            
            <div id="chatWindow" class="contentAreas">
              
            </div>
        
            
            <div id="chatInput">
              <div class="input-prepend">
                <span class="add-on"><i class="icon-pencil"></i></span>
                <input class="input-xxlarge" type="text" size="50" id="chatMessage" placeholder='Type Here'      onkeydown="if(event.keyCode == 13) {sendChat(); scrollChatBox();}"/> 
              </div>
            </div>

          </div> <!-- chatcontiner -->
        </div>  <!--mian content-->
      </div>  <!--span 10-->
     
    </div>
  </div>
  








        
     
  <!-- Generic Modal to be used with Javascript if needed -->
  <div id="modal" class="modal hide fade"  aria-labelledby="modalLabel" aria-hidden="true">
  	<div class="modal-header">
  			<!-- <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icon-remove"></i></button> -->
  			<h3 id="modalLabel-empty"></h3>
  	</div>

  	<div class="modal-body">

  	</div>

  	<div class="modal-footer">
  		<button class="btn btn-danger" data-dismiss="modal" aria-hidden="true">Cancel</button>
  	</div>
  </div>

  <!-- modal for when challenging a user -->
  <div id="challengeBox" class="modal hide fade"  aria-labelledby="modalLabel" aria-hidden="true">
  	<div class="modal-header">
  			<!-- <a id="closeButt" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icon-remove"></i></a> -->
  			<h4 id="modalLabelChallenge"></h4>
  	</div>

  	<div id="challengeText" class="modal-body">
  		 
  	</div>

  	<div id='challengeBoxFooter' class="modal-footer">
  		 <span id='challenged-timer'></span>
          <a id="challenge-accepted" class="btn btn-success" data-dismiss="modal" aria-hidden="true">Accept</a>
  		<a id="challenge-denied" class="btn btn-danger" data-dismiss="modal" aria-hidden="true">Deny</a>
  	</div>
  </div>

  <!-- modal for user who's been challenged -->
  <div id="challengerBox" class="modal hide fade"  aria-labelledby="modalLabel" aria-hidden="true">
      <div class="modal-header">
              <!-- <button id="closeButt" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icon-remove"></i></button> -->
              <h4 id="modalLabelChallenger"></h4>
      </div>

      <div id="challengerText" class="modal-body">
           
      </div>

      <div class="modal-footer">
           
      </div>
  </div>
</body>
</html>
