<?php
 
	if(isset($_COOKIE['token'])){
		setCookie("token", "", time()-3600, "http://nova.it.rit.edu/~nxa2762/","rit.edu");
    }
 
	$username = $_POST['username'];
	$password = $_POST['password'];
	$sha_password= sha1($password);

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Sign in</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Le styles -->
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <style type="text/css">
      body {
        padding-top: 40px;
        padding-bottom: 40px;
        background-color: #f5f5f5;
      }

      .form-signin {
        max-width: 300px;
        padding: 19px 29px 29px;
        margin: 0 auto 20px;
        background-color: #fff;
        border: 1px solid #e5e5e5;
        -webkit-border-radius: 5px;
           -moz-border-radius: 5px;
                border-radius: 5px;
        -webkit-box-shadow: 0 1px 2px rgba(0,0,0,.05);
           -moz-box-shadow: 0 1px 2px rgba(0,0,0,.05);
                box-shadow: 0 1px 2px rgba(0,0,0,.05);
      }
      .form-signin .form-signin-heading,
      .form-signin .checkbox {
        margin-bottom: 10px;
      }
      .form-signin input[type="text"],
      .form-signin input[type="password"] {
        font-size: 16px;
        height: auto;
        margin-bottom: 15px;
        padding: 7px 9px;
      }

      body{
      	background-image: url('media/logo.png');
      	background-color: #F5F5F5;
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
    
    <!-- jQuery and javaScript functions -->
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
	<script type="text/javascript" src="assets/js/bootstrap.js"></script> 
	<script type="text/javascript" src="js/ajaxFunctions.js"></script> 

	<script type="text/javascript">  
	
		$(document).ready(function () {  
		    $("[rel=tooltip]").tooltip();  
	  	}); 
	 

		/******************
		Login
		*******************/ 
		/*starting the login process by making an ajax call to the severice layer*/
		function initLogin(){
			 
			var username = "<?php echo $username ?>";
			var password = "<?php echo $sha_password ?>";
			var dataString = username+"|"+password;
			ajaxCall("POST",{method:"getLogin",a:"login",data:dataString},callbackInitLogin);
		
		}

		function callbackInitLogin(jsonObj){
			
			if(jsonObj != null){
				//redirecrt to lobby!
				 window.location = "http://nova.it.rit.edu/~nxa2762/546/project/lobby.php"; 
			}else{
				$('#error').html("<div class='alert alert-error'>Please make sure your username and password is correct!</div>");
			}
		}

		/******************
		signing up
		*******************/ 
		/*starting the login process by making an ajax call to the severice layer*/
		function initSignUp(){
			
			var username = "<?php echo $username ?>";
			var password = "<?php echo $sha_password ?>";

			var dataString = username+"|"+password;
			ajaxCall("POST",{method:"setLogin",a:"login",data:dataString},callbackSignUp);
		
		}

		function callbackSignUp(jsonObj){
			if(jsonObj[0] == 'Exist'){
			 
				$('#error').html("<div class='alert alert-error'>Please use a different username, <strong>"+ jsonObj[1]+"</strong> already in use!</div>");
			}else{
				window.location = 'http://nova.it.rit.edu/~nxa2762/546/project/lobby.php';
			}

		}

		<?php
			if($_POST['login']){

				if(strlen($username)>0 && strlen($password)>0) {
					echo "initLogin();";
				}
			}elseif($_POST['register']) {
			 	
				if(strlen($username)>0 && strlen($password)>0) {
					echo "initSignUp();";
				}
			}
		?>


	</script>
    
  </head>

  <body>
    <div class="container">
		<form class="form-signin" action="index.php" method="POST" >
	        
	        <h3 class="form-signin-heading">Login or Sign up here</h3>

	        <div id="error">
	        </div>
	       	
	        <input type="text" name="username" id="username"  class="input-block-level" value="<?=$_POST['username'] ?>" placeholder="username">
	        <input type="password" name="password" id="password" class="input-block-level" placeholder="Password" value="<?=$_POST['password']?>" />
	        <label class="checkbox">
	          <!--<input type="checkbox" value="remember-me"> Remember me-->
	        </label>
	        <input class="btn btn-large" type="submit" value='Sign in' name ='login' />
	        <input data-toggle="tooltip" data-placement="right" data-original-title="fill out the form above and click here to register! That easy!" class="btn btn-large btn-primary" type="submit" value='Sign Up' name ='register' />
		      	
	  	</form> 
    </div> <!-- container -->
  </body>
</html>