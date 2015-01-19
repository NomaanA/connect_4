<?php
/***************************************************************************************************************

THIS FILE IS IN BIZ LAYER AND IS IN CHARGE OF CONNECTIONG TO DATABASE AND SPITTING DATA BACK TO SERVIVE LAYER

***************************************************************************************************************/

require_once("../../../dbInfo.inc"); 
require_once('bizDataLayer/exception.php');
require_once('bizDataLayer/jsonUtil.php');
require_once('token_svc.php');

/********************************************************************

giving data back when called. check the credentials against the database

********************************************************************/

function getLoginData($username, $password){
 	    //return '[{"success":true}]';
        //$array = array ($username, $password);
        //return json_encode($array);       
        //password is sha1
        
        global $mysqli;
        
        global $posArray;

        $sql="SELECT user_id,username FROM c4_users WHERE username = ? AND password = ?";
         
        $result;
         

        try{
            if($stmt=$mysqli->prepare($sql)){

                $stmt->bind_param("ss", $username, $password);
                $result = returnJson($stmt);
                
                $userData = json_decode($result, true);
                $updateSql = "UPDATE c4_users SET room_id = 0 WHERE user_id = ?";

                if($stmt1=$mysqli->prepare($updateSql)){
                    $userId = (int)$userData[0]['user_id'];

                    $stmt1->bind_param("i",$userId);
                    $stmt1->execute();
                    $stmt1->close();
                    $mysqli->close();
                    
                }else{
                    throw new Exception("An error occurred while feting record data");
                }


                setTheCookie($userData[0]['username'],$userData[0]['user_id'],$posArray);
                $stmt->close();
                $mysqli->close();
                 
            }else{
                throw new Exception("An error occurred while feting record data");
            }
        }catch(Exception $e){
            log_error($e, $sql, null);
            return 'false';
        }
        return $result;
    } 

function setLoginData($username, $password){

        global $mysqli;
        global $posArray;
        //$date = date();
        
        $sql = "INSERT INTO c4_users ('username','password') VALUES (?,?)";
        $checkSql = "SELECT * FROM c4_users WHERE username = ?";
        $result='';

        try{
            if ($stmt = $mysqli->prepare($checkSql)) {
                $stmt->bind_param('s',$username);
                $stmt->execute();
                $stmt->store_result();
                $count = $stmt->num_rows;
                $stmt->close();
                if($count != 0){
                    $result = json_encode(array("Exist","$username"));
                }else{
                	try{
						if($stmt=$mysqli->prepare("INSERT INTO c4_users (username,password) VALUES (?,?)")){
			        		 
			                $stmt->bind_param("ss",$username,$password);
			                $stmt->execute();
			                $newId = $stmt->insert_id;
							$result = json_encode(array('True'));
			                $stmt->close();
			            	$mysqli->close();
	            	        setTheCookie($user,$newId,$posArray);
			            }
			            else{
            				throw new Exception("An error occured while fetching record data");
			            }
        			}catch(Exception $e){
            			log_error($e,$sql,null);
            			$result = json_encode(array('Fail'));
        			}

                }
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

Logs the user out of the system and clears the cookie. 

********************************************************************/

    function logout($userid){
    	global $mysqli;
		$updateSql = "UPDATE c4_users SET room_id = -1 WHERE user_id = ?";
        $result='';
        try{
			if($stmt1=$mysqli->prepare($updateSql)){
				$userId = (int)$userid;
				
				//would bind here if needed
				
				$stmt1->bind_param("i",$userId);
				$stmt1->execute();
				$stmt1->close();
				$mysqli->close();
				setCookie("token","",time()-36000,"https://nova.it.rit.edu/~nxa2762/546/project","rit.edu"); 
				
				$result = json_encode('Done');
			}
			else{
				throw new Exception("An error occured while fetching record data");
			}
        }
        catch(Exception $e){
            log_error($e,$sql,null);
            $result =json_encode('Fail');
        }
        return $result;

    }

 
?>