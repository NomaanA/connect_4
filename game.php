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

<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
    <title>Connect - 4 : Game</title>
 
   

    <!-- <link href="assets/css/bootstrap-responsive.css" rel="stylesheet"> -->
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <link href="//netdna.bootstrapcdn.com/font-awesome/3.1.1/css/font-awesome.css" rel="stylesheet">

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="assets/js/html5shiv.js"></script>
    <![endif]-->

    <!-- Fav and touch icons -->
 <!--    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="assets/ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="assets/ico/apple-touch-icon-114-precomposed.png">
      <link rel="apple-touch-icon-precomposed" sizes="72x72" href="assets/ico/apple-touch-icon-72-precomposed.png">
                    <link rel="apple-touch-icon-precomposed" href="assets/ico/apple-touch-icon-57-precomposed.png">
                                   <link rel="shortcut icon" href="assets/ico/favicon.png"> -->
    
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="cookies.js"></script>
    
    <script src="js/Objects/Cell.js" type="text/javascript"></script>
    <script src="js/Objects/Piece.js" type="text/javascript"></script>
    <script src="js/gameFunctions.js" type="text/javascript"></script>

    <script type="text/javascript">
        
        var gameId = <?php echo $_GET['game_id']; ?>;
        var username =  '<?php echo $userArray[0]; ?>';
        var player = username;
        var userid = <?php echo $userArray[1]; ?>;
        var roomid = gameId;

      
        $(document).ready(function() {
         
           // $(window).bind('beforeunload', function(){
           //       return "Closing the window or refreshing it will result in losing the game?";
           //       alert('sss');
           //  });

          //  window.onunload = window.onbeforeunload = (function(){

          //   var didMyThingYet=false;

          //   return function(){
          //     if (didMyThingYet) return;
          //     didMyThingYet=true;
          //     console.log('unloading');
          //     //ajaxCall("POST",{method:"ByBoycott",a:"game",data:dataString},callbackInitLogin);
          //     window.location = 'http://nova.it.rit.edu/~nxa2762/546/project/lobby.php';
          //     alert('You will lose the game!');

          //   }

          // }()); 


           // $(window).on('beforeunload', function() {
           //      var x = winClosed();
           //      return x;
           //  });
           //  function winClosed(){
           //    //ajaxCall("POST",{method:"ByBoycott",a:"game",data:dataString},callbackInitLogin);
           //    logout();
           //    return 1+3;
           //  }

          

          setTimeout(function(){
            $('.hint').fadeOut('slow');

          },5000);

          setTimeout(function(){
            $('.show').fadeIn('slow');

          },5000);

          
          //$( '[id^=cell_00]' ).css('fill', '#ffffff');
          getGame(gameId);          
          getChat(gameId);

        });
    
        function sendChat(){
            var msg = document.getElementById('chatMessage').value;
            if(msg != ''){
                document.getElementById('chatMessage').value='';
                var uData = msg + "|" + gameId;
                setChat(uData);
            }
        }

        function logout(){
            setLogoutData('<?php echo $userArray[1]; ?>');
        }

    </script>
    <script src="js/ajaxFunctions.js" type="text/javascript"></script>
     

    <style type="text/css">
      
      body{
        padding:0px;
        margin:0px;
       
      }

   
      .drop{
        fill: #ffffff;
      }

      .link:hover{
        cursor:pointer;
        color:black;
      }

      .link{
        color:#D97925;
      }

      .clear{
        clear:both;
      }
 
      #chatWindow{
        padding: 0px;
        /*border-width:1px;*/
        /*border-style: dotted;*/
        height: 432px;
        width: 350px;
        overflow-y: auto;
        float:left;
        clear:right;
        border: 1px solid #B2B2A5;
        border-radius: 15px;
      }

      
      #chatInput{
        clear:left;
        width: 480px;
        margin-left: 15px;
        padding-top: 3px;
        font-size: 20px
      }
       

      .contentAreas{
        background-color: rgb(231, 235, 238);
        background-color: #F5F5F5;
        margin: 15px 0 0 15px;
      }
 

      #logout{
        cursor: pointer;
      }
 

      #svgBackground{ 
        fill: #F5F5F5;
        stroke: 1px;
        stroke-style: dotted;
      }

      .player0{
        fill: #33e5e3;
      }

      .player1{
        fill: #f6242e;
      }

      .cell_white{
        fill:#b5b5b5;
        stroke-width:2px;
        stroke: #c9d2d9;
      }

      /*.name_black{
        fill:black;
        font-size:18px;
      }

      .name_orange{
        fill:orange;
        font-size:24px;
      }*/

      #chatMessage{
        width: 311px;
      }

      .container{
        width: 100%;
      }

      #chat_wrapper{
        float: left;
        width: 356px;
       
      }

      #wrapper{
        position: relative;
        top: 43px;
      }

      #svgTag{
        border: 1px solid black;
        border-radius: 20px;
        padding: 0px;
      }


      #gamewrapper{
        /*position: fixed;*/
        left: 581px;
        margin-left: -208px;
        
        width: 1399px;
        top: 39px;
      }

      .margin{
        margin: 20px 20px;
      }

      ::selection {
       background: #F5F5F5; /* Safari */
          color: black;
       }

       ::-moz-selection {

       background: #F5F5F5; /* Firefox */
          color: black;
       }

       .hint{
        width: 100px;
       }

       .pointer{
          left: 62px;
          position: relative;
       }

    </style>

  
</head>

<body>
     <div class="navbar navbar-fixed-top">
      <div class="navbar-inner">
        <div style='margin-left: 20px;'>
          <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="brand" href="#">Connect<span class="badge">4</span></a>
          <div class="nav-collapse collapse">
            <ul class="nav">
              
              
            </ul>
            <ul class='nav pull-right'>
              <li><a class='brand' id='username' href='#'><?php echo $userArray[0]; ?><span id='myScore'></span></a></li>
              <li><button style ='position: relative; top: 5px;'class='btn btn-danger btn-mini margin' onclick="logout()" ><a style='color: white;' >Logout</a></button></li>
            </ul>
             
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>
     
   
    <div class="container">

        <div id='wrapper' >
          <div  id='chat_wrapper'>
               <div id="chatWindow" class="contentAreas">
               </div>
                
                    
                <div id="chatInput">
                     
                    <div class="input-prepend">
                      
                      <span class="add-on"><i class="icon-pencil"></i></span>
                      <input class="input-xlarge" type="text" size="50" id="chatMessage" placeholder='Type Here'      onkeydown="if(event.keyCode == 13) {sendChat();}"/> 
                    </div>
                </div>

          </div>

          <div  id="gamewrapper">


            <svg id='svgTag' class="contentAreas" xmlns="http://www.w3.org/2000/svg" version="1.1"  width="650px" height="470px">
            
                <rect x="0px" y="0px" width="100%" height="100%" id="svgBackground" />
               
              

               <!--  <foreignObject x="450px" y="20px" id="nyt" display="none" class="label label-important" height="18" width="122">
                  <body xmlns="http://www.w3.org/1999/xhtml">
                    <div class="well label label-important">
                        NOT YOUR TURN!
                    </div>
                  </body>
                </foreignObject> -->

                <!-- <foreignObject x="450px" y="35px" id="nyp" display="none" class="label label-important" height="18" width="122">
                  <body xmlns="http://www.w3.org/1999/xhtml">
                    <div class="label label-warning">
                        NOT YOUR PIECE!
                    </div>
                  </body>
                </foreignObject> -->

               

                <foreignObject  x="95px" y="10px" height="30" width="100%">
                  <body xmlns="http://www.w3.org/1999/xhtml">
                    <div  id="youPlayer" class="">
                        
                    </div>
                  </body>
                </foreignObject>

    
                <text x="200px" y="30px" fill='black'>
                    VS
                </text>
               


                <foreignObject   x="270px" y="10px" height="30" width="100%">
                  <body xmlns="http://www.w3.org/1999/xhtml">
                    <div  id="opponentPlayer" class="">
                        
                    </div>
                  </body>
                </foreignObject>
                
           
                <foreignObject  x="460px" y="139px" id="p1" display="none" height="60" width="140px">
                  <body xmlns="http://www.w3.org/1999/xhtml">
                    <i class="pointer icon-hand-right show" style='display: none;'></i>
                    <p class='icon-hand-right hint alert alert-success'>  You</p>
                    
                  </body>
                </foreignObject>
 
                <foreignObject x="460px" y="250px" display="none" id="p2" height="60" width="140px" >
                  <body xmlns="http://www.w3.org/1999/xhtml">
                    <i class="pointer icon-hand-right show" style='display: none;'></i>
                    <p class='icon-hand-right hint alert alert-success'>  You </p>
                    
                  </body>
                </foreignObject>


                <foreignObject   x="460px" y="59px"   height="60" width="140px">
                  <body xmlns="http://www.w3.org/1999/xhtml">
                    <i class="icon-hand-left show " style='display: none;'></i>
                    <p class='icon-hand-left hint alert alert-success'> Drop Area</p> 
                    
                  </body>
                </foreignObject>

            </svg>

          </div>
        </div>

    </div> 



    <div id="winner-modal" class="modal hide fade"  aria-labelledby="modalLabel" aria-hidden="true">
        <div class="winner-modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icon-remove"></i></button>
            <h3 id="winner-modalLabel"></h3>
        </div>

        <div id="winner-modal-body" class="modal-body">
            <h4 class='pull-right' style='position: relative; top: 49px; font-size: 23px;'> Congratulations! You are the winner </h4> <img src='media/cup.png'/> 
        </div>

        <div id='winner-footer' class="modal-footer">
          <button class="btn btn-danger" data-dismiss="modal" aria-hidden="true">Cancel</button>
        </div>
    </div>

     <div id="loser-modal" class="modal hide fade"  aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icon-remove"></i></button>
            <h3 id="loser-modalLabel"></h3>
        </div>

        <div id="loser-modal-body" class="modal-body">
            
        </div>

        <div class="modal-footer">
          <button class="btn btn-danger" data-dismiss="modal" aria-hidden="true">Cancel</button>
        </div>
    </div>

</body>
</html>
