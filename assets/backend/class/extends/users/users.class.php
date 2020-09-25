<?php

/*
 *******************************************************************************
 *                      USERS Class
 *******************************************************************************
 *      Author:     César Pinto
 *      Website:    https://www.linkedin.com/in/cesargrancho/
 *
 *      File:       users.class.php
 *      Version:    1.1.1
 *                  
 *      License:    GPL
 *
 *******************************************************************************
 *  VERSION HISTORY:
 *******************************************************************************
 *      v1.1.3 [24.05.2019] 
 *		- revision for new PandIX release
 *		- routing re-worked
 *  
 *      v1.1.2 [14.10.2018] 
 *		- permissionsCheck() update fix permissions for restricted sections
 *
 *      v1.1.1 [18.8.2018] 
 *		- updated userExist
 *
 *      v1.1.0 [25.7.2018] 
 *		- added function permissionsUserGet() for customs options
 * 
 *      v1.0.9 [5.6.2018] 
 *		- Permissions now can be initialized in constructor from method permissionsCheck
 *
 *      v1.0.8 [18.11.2017] 
 *		- Permission structure changes
 *      
 *      v1.0.0 [06.06.2014] 
 *		- Public Version Release
 *
 *******************************************************************************
 *  DESCRIPTION:
 *******************************************************************************
 *  The class is intended to simplificy user managment
 *
 *  
 *******************************************************************************
 *  Functions Tree:
 *******************************************************************************
 *  
 *  .PUBLIC
 *  
 *  .PRIVATE
 *  
 *  
 *******************************************************************************
 *  USAGE:
 *******************************************************************************
 *  
 *  
 *******************************************************************************

*/


class users {
    
	public $_idUser;	
	
    public $_userData; // TO DO Create interface object    
	
	public $_userRoute;   
	
	protected $_status;
	
	public $__errorLog; // USED to PARSE & DISPLAY ERRORS

	
	#CORE CLASS Variables
	
	#require to be public for Inheritance 	
    public $_PDO; 
    public $_AUX;

    public function __construct($data = array())
    {
		
        $this->_PDO=$data['db'];
        $this->_AUX=$data['aux'];

        $this->_idUser=$data['idUser'];		

		$this->_status = (object) array(
					"banned" => 0,
					"pending" => 1,
					"active" => 2);
		
		$this->_userRoute=$this->permissionsCheck(["userRoute" => $data['userRoute']]); 							
		
		#Database Table Check		
		$this->dbTableCheck($data['dbTables']);
	}

    #PUBLIC FUNCTION : begin
	
	# maybe move to app.api ?
	public function permissionsCheck($data = array()) 
	{					
	/*
	Array with conditional routes allowed by registered and non registered users	
	Requires:
		userRoute:String
	
	Returns:	
		userRoute:String	
	*/		
	
		function permissionsUserGet()
		{
			/*
			Array with conditional routes allowed by registered and non registered users	
			
			 returns:	 		
					array	
			*/				
			
			return [
					[
					"domain" => "users",
					"redirects" => "admin.do.login", #default route when allowed
					"action" => [
						"default" => "admin.do.error", #send user here when not allowed
						"allow" => ["login","register","usersLogin"]
						]
					],
					[
					"domain" => "admin",
					"redirects" => "admin.view.dashboard", #default route when allowed
					"action" => [
						"default" => "admin.view.login", #send user here when not allowed
						"allow" => []
						]
					],
					[
					"domain" => "installer",
					"redirects" => "installer.view.home", #default route when allowed
					"action" => [
						"default" => "installer.view.home", #send user here when not allowed
						"allow" => []
						]
					]				
				]; 
		}
			
		
		if (!($this->_AUX->stringValidation($data["userRoute"]))) return false;				
		
		$userRoute=$data["userRoute"];
				
		#get userRouter options
		$cRoute=explode(".",$userRoute);		
		
		#domain - also can be domain
		$domain=$cRoute[0];		
		
		
		#view or requesting actions
		$mode=$cRoute[1];
		
		#what action will do
		$action=$this->_AUX->stringValidation($cRoute[2])?$cRoute[2]:"";			

		#check user permissions and update "userRoute" command		
		$permissions=permissionsUserGet();		
		
		#find Index for domain		
		$key = array_search($domain, array_column($permissions, 'domain'));	
		
		#user not log
		if (!$this->logStatus([
						"idUser" => $this->_idUser
						])) {		
			
			
			#check if domain / action is allowed for 
			if ($this->_AUX->stringValidation($action) &&
			is_numeric(array_search($action, $permissions[array_search($domain, array_column($permissions, 'domain'))]["action"]["allow"]))) return implode(".",$cRoute);				


			#rebuild allowed cRoute command if not allowed action
			$cRoute=$permissions[$key]["action"]["default"];						
				
			return $cRoute;
		}			
			
			
		#check access to restricted domain : begin
		if (is_numeric($key)) {										

			#Allow access to action		
			if ($this->_AUX->stringValidation($action)) return implode(".",$cRoute);		
			
			#No ACTIONS : begin 
			
				#find Index for domain			
				$key = array_search($domain, array_column($permissions, 'domain'));						
				
				#rebuild allowed cRoute command
				$cRoute=$permissions[$key]["redirects"];			
				
				return $cRoute;
			
			#No ACTIONS : end							
		}
		#check access to restricted domain : end
	
		#Allow ROUTE
		return implode(".",$cRoute);
		
	}
	
	#region « SESSION - AUTHENTICATION »
	
	#(OLD) userExist 
	public function exist($data = array()) 
	{
		/*
		get user information if exist
		
		 conditional:
				idUser:Integer
				email:String
				username:String			
		
		 returns:
		 	false (on error) 
			array
		*/
		
		$this->__errorLog.=("<br>\n exist(init)");			

		#region « Variables Validation »
			$SQLCondition="";
			
			$SQLConditionCheck=function($str) {
				return $str==""?"WHERE":"AND";
			};							
			
			#idUser : Integer
				$idUser=is_numeric($data["idUser"])?$data["idUser"]:0;        			
								
				#ADD SQL & query array
				if ($idUser>0) {
					$SQLQueryData[":idUser"]=$idUser;	
					#Condition Search 
					$SQLCondition.=sprintf(" %s `a`.`idUser`=:idUser",
					$SQLConditionCheck($SQLCondition));							
				}				
				
			#email : String
				$email=isset($data["email"])?$data["email"]:"";        

				#ADD SQL & query array
				if ($email!="") {
					$SQLQueryData[":email"]=$email;	
					#Condition Search 
					$SQLCondition.=sprintf(" %s `a`.`email` = :email",
					$SQLConditionCheck($SQLCondition));							
				}
				
			#username : String
				$username=isset($data["username"])?$data["username"]:"";        

				#ADD SQL & query array
				if ($username!="") {
					$SQLQueryData[":username"]=$username;	
					#Condition Search 
					$SQLCondition.=sprintf(" %s `a`.`username` = :username",
					$SQLConditionCheck($SQLCondition));							
				}				
			
			#SQLCondition is require to continue 
			if ($SQLCondition=="") {
				$this->__errorLog.=sprintf("Missing Values - IdUser: %s - Mail: %s - Username - %s",
								$idUser,
								$email,
								$username
								);			
				return false;
			}
		#endregion « Variables Validation »			
		
		
		# Create SQL Query
		$SQLQuery=sprintf("SELECT *
							FROM `users` `a`
							%s",
							$SQLCondition);                		
						
		
		$resultQuery = $this->_PDO->prepare($SQLQuery);	
		
		#try get data from database
		if (!$resultQuery->execute($SQLQueryData)) {
			$this->__errorLog.=sprintf("->(error in query to get user details)[ %s ]",$this->_AUX->PDODebugger($SQLQuery,$SQLQueryData));
			return false;
		}	
		
		#has no records return empty array
		if ($resultQuery->rowCount()==0) {
			$this->__errorLog.=("->(no user found)");
			return array();
		}
		
		return $resultQuery->fetch(PDO::FETCH_ASSOC);
	}	

	#(OLD) UserLogCheck
    public function logStatus($data = array())    
	{
		/*
		update Session if valid user		
		
		
		 requires:
				idUser:Integer
				
				token <TO DO> will require to add session token
		
		 returns:
		 	boolean			
		*/    		
		
		#region « VAR Validations »
		
			#idUser:Integer
				$idUser=isset($data["idUser"])?$data["idUser"]:0;        
			
				#allow only numeric values
				if (!is_numeric($idUser)|| $idUser==0) {
					$this->__errorLog.=sprintf("->(error in idUser: %s)", $idUser);
					return false; 
				}		
		
		#endregion « VAR Validations »	
        

        #check if exist  
		if (!is_array($this->exist(["idUser"=>$idUser]))) {
			$this->__errorLog.=sprintf("->(Not valid user: %s)", $idUser);
			return false;
		}
			
		//UPDATE SESSION VARS
		$_SESSION += [
				'loginAtempt' => 0,
				'loginSession' =>sprintf("%s_%s",
									  $idUser,
									  time()),
				'_idUser' => $idUser
				];						  

		$this->__errorLog.=sprintf("Update Session Vars: %s\n",$_SESSION['loginSession']);
		
		return true;
    }

	#RE-WORK
    public function login($data = array()) 
	{
		/*
		check for user credentials
		
		 requires:
			email:String
			password:String			
		
		 returns:
			Boolean
		*/		

		$this->__errorLog.=("<br>\n login(init)");	
		
		#region « Variables Validation »
			$SQLCondition="";
			
			$SQLConditionCheck=function($str) {
				return $str==""?"WHERE":"AND";
			};				
				
			#email : String
				$email=isset($data["email"])?$data["email"]:"";        

				#required 
				if ($email=="") {
					$this->__errorLog.=sprintf("->(error in Email: %s)", $email);
					return false; 						
				}
				
				#ADD SQL & query array
				$SQLQueryData[":email"]=$email;	
				#Condition Search 
				$SQLCondition.=sprintf(" %s `a`.`email` = :email",
				$SQLConditionCheck($SQLCondition));					
				
			#password : String
				$password=isset($data["password"])?$data["password"]:"";        

				#required 
				if ($password=="") {
					$this->__errorLog.=sprintf("->(error in Password: %s)", $password);
					return false; 						
				}
				
				#ADD SQL & query array
				$SQLQueryData[":password"]=md5(preg_replace('/\s+/', '', $password));	
				#Condition Search 
				$SQLCondition.=sprintf(" %s `a`.`password` = :password",
				$SQLConditionCheck($SQLCondition));				
			
		#endregion « Variables Validation »	

		#TO CONTINUE
        
        $SQLQuery = sprintf("SELECT * FROM `users` 
					WHERE 
					`email` = :email 
					AND
					`password` = :password
					AND
					`status` = %s",
					$this->_status->active);                     
		
		#echo $this->_AUX->PDODebugger($SQLQuery,$SQLQueryData);														
		$resultQuery = $this->_PDO->prepare($SQLQuery);			
		
		
		if (!$resultQuery->execute($SQLQueryData)) {
			$this->__errorLog.=sprintf("->(Error Login Query)[ %s ]",$this->_AUX->PDODebugger($SQLQuery,$SQLQueryData));				
			return false;
		}					
       
        #user not found
        if ($resultQuery->rowCount()==0) {//USER FOUND
			return false;            
        }   		
		
		#assign user data 
		$this->_userData=$resultQuery->fetch(PDO::FETCH_ASSOC);
		
		
		#setup session
		
		$_SESSION += [
				'loginAtempt' => 0,
				'loginSession' =>sprintf("%s_%s",
									  $this->_userData['idUser'],
									  time()),
				'_idUser' => $this->_userData['idUser']
				];

		$this->__errorLog.=sprintf("VAR LOGIN SESSION: %s\n",$_SESSION['loginSession']);		

		return true;		
    }
    
	#endregion
	
	#region « CRUD OPERATIONS »

	#old add()
	public function create($data = array())
    {
		/*
		check for user credentials
		
		 requires:
			username : String
			email : String
			password : String		
			status : Integer			
			registrationCode : Integer
		
		 returns:
			Boolean
		*/		

		$this->__errorLog.=("<br>\n create(init)");	
		
		#region « Variables Validation »
				
				
			#username : String
				$username=isset($data["username"])?$data["username"]:"";        

				#required 
				if ($username=="") {
					$this->__errorLog.=sprintf("->(error in Username: %s)", $username);
					return false; 						
				}
				
			#email : String
				$email=isset($data["email"])?$data["email"]:"";        

				#required 
				if ($email=="") {
					$this->__errorLog.=sprintf("->(error in Email: %s)", $email);
					return false; 						
				}
				
			#password : String
				$password=isset($data["password"])?md5(preg_replace('/\s+/', $data['password'])):"";        

				#required 
				if ($password=="") {
					$this->__errorLog.=sprintf("->(error in Password: %s)", $password);
					return false; 						
				}
				
			#status : Integer
				$status=isset($data["status"])?$data['status']:0;        

				#required 
				if (!is_numeric($status)) {
					$this->__errorLog.=sprintf("->(error in Status: %s)", $status);
					return false; 						
				}				

			#registrationCode : Integer
				$registrationCode=isset($data["registrationCode"])?$data["registrationCode"]:0;        

				#required 
				if (!is_numeric($registrationCode)) {
					$this->__errorLog.=sprintf("->(error in Registration Code: %s)", $registrationCode);
					return false; 						
				}	
				
		#endregion « Variables Validation »				
		
		$SQLQueryData = [
			":username" => $username,
			":email" => $email,
			":password" => $password,
			":status" => $status,
			":registrationCode" => $registrationCode,
			":time" => time()
		];

        $SQLQuery="INSERT INTO `users` (`idUser`, `username`, `email`, `password`, `status`, `registrationCode`, `registrationDate`)
                     VALUES (NULL, :username, :email, :password, :status, '%s', '%s', '%s')";

		#echo $this->_AUX->PDODebugger($SQLQuery,$SQLQueryData);die();														
        $resultQuery = $this->_PDO->prepare($SQLQuery);                
		
		if (!$resultQuery->execute($SQLQueryData)) {
			$this->__errorLog.=sprintf("->(error in adding user to database)[ %s ]",$this->_AUX->PDODebugger($SQLQuery,$SQLQueryData));				
			return false;
		}			

        $this->_userData['idUser']=$this->_PDO->lastInsertId();
        $this->__errorLog.=sprintf("New User ID: %s",$this->_userData['idUser']);            
		
        return true;
    }    
    
	#old edit()
    public function update($data = array())
    {
		/*
		Edit user data
		
		requires:
			idUser : Integer
			
			
		Conditional:
			username : String
			email : String
			password : String		
			status : Integer						
		
		 returns:
			Boolean
		*/		
		
		$this->__errorLog.=("<br>\n update(init)");			
		
		#region « Variables Validation »
			$SQLCondition="";
			
			$SQLConditionCheck=function($str) {
				return $str==""?"SET":",";
			};		
			
			#idUser : Integer
				$idUser=isset($data["idUser"])?$data["idUser"]:0;        

				#required 
				if (!is_numeric($idUser)) {
					$this->__errorLog.=sprintf("->(error in idUser: %s)", $idUser);
					return false; 						
				}			
			
		
			#username : String
				$username=isset($data["username"])?$data["username"]:"";  
				
				if ($username!="") {
					$SQLQueryData[":username"]=$username;	
					#Condition Search 
					$SQLCondition.=sprintf(" %s `username`=:username",
					$SQLConditionCheck($SQLCondition));							
				}					
				
			#email : String
				$email=isset($data["email"])?$data["email"]:"";  
				
				if ($email!="") {
					$SQLQueryData[":email"]=$email;	
					#Condition Search 
					$SQLCondition.=sprintf(" %s `email`=:email",
					$SQLConditionCheck($SQLCondition));							
				}	
				
			#password : String
				$password=isset($data["password"])?md5($data["password"]):"";  
				
				if ($password!="") {
					$SQLQueryData[":password"]=$password;	
					#Condition Search 
					$SQLCondition.=sprintf(" %s `password`=:password",
					$SQLConditionCheck($SQLCondition));							
				}					
				
				
			#status : Integer
				$status=isset($data["status"])?$data['status']:0;    

				if ($status>=0) {
					$SQLQueryData[":status"]=$status;	
					#Condition Search 
					$SQLCondition.=sprintf(" %s `status`=:status",
					$SQLConditionCheck($SQLCondition));							
				}	
				
			#require SQL condition
			if ($SQLCondition=="") 
			{				
				$this->__errorLog.=sprintf("->(No SQL Conditions)[ %s ]", $SQLCondition);					
				return false;
			}
		
		#endregion
	
        $SQLQuery=sprintf("UPDATE `users`
                       %s
                       WHERE `idUser` = '%s'",
                       $SQLCondition,
                       $idUser);
					   
		#echo $this->_AUX->PDODebugger($SQLQuery,$SQLQueryData)."<br><br>"; die();
		$resultQuery = $this->_PDO->prepare($SQLQuery);						   
       
		if (!$resultQuery->execute($SQLQueryData)) {
			$this->__errorLog.=sprintf("->(error in editing user)[ %s ]",$this->_AUX->PDODebugger($SQLQuery,$SQLQueryData));
			return false;
		}

        return true;

    }
	
	#old remove()
	public function delete($data = array()) 
	{
		/*
		Removes User
		
		requires:
			idUser : Integer					
		
		 returns:
			Boolean
		*/		
		
		$this->__errorLog.=("<br>\n delete(init)");			
		
		#region « Variables Validation »
			
			#idUser : Integer
				$idUser=isset($data["idUser"])?$data["idUser"]:0;        

				#required 
				if (!is_numeric($idUser)) {
					$this->__errorLog.=sprintf("->(error in idUser: %s)", $idUser);
					return false; 						
				}
				
		#endregion
		
        $SQLQuery=sprintf("DELETE FROM `users`
                     WHERE
                     `idUser`=%s",
                     $idUser);
					   
		#echo $this->_AUX->PDODebugger($SQLQuery,$SQLQueryData)."<br><br>"; die();
		$resultQuery = $this->_PDO->prepare($SQLQuery);						   
       
		if (!$resultQuery->execute($SQLQueryData)) {
			$this->__errorLog.=sprintf("->(error in removing user)[ %s ]",$this->_AUX->PDODebugger($SQLQuery,$SQLQueryData));
			return false;
		}		

        return true;            		
    }

	#endregion « CRUD OPERATIONS »    

	/*

	TO DO 	
	
	*/
    public function status($data) 
	{
		
		/*
		check for user credentials			
		
		 requires:
			idUser : Integer
			status : Integer

			
		 returns:
			Boolean
		*/		

		$this->__errorLog.=("<br>\n status(init)");	
		
		#region : Variables Validation 
			
			#idUser : Integer
				$idUser=isset($data["idUser"])?$data['idUser']:0;        

				#required 
				if (!is_numeric($idUser)
					|| $idUser==0) {
					$this->__errorLog.=sprintf("->(error in idUser: %s)", $idUser);
					return false; 						
				}
				
			#status : Integer
				$status=isset($data["status"])?$data['status']:0;        

				#required 
				if (!is_numeric($status)) {
					$this->__errorLog.=sprintf("->(error in status: %s)", $status);
					return false; 						
				}				

		#endregion : Variables Validation
		
		$SQLQueryData[":idUser"]=$idUser;
        $SQL="SELECT * FROM `users` 
				WHERE `idUser`=:idUser
				AND `status`=:status"; 
				
        $result = $this->_PDO->prepare($SQL);
		
		if (!$result->execute($SQLQueryData))
		{
			$this->__errorLog.=sprintf("->(error in query to get user status)[ %s ]",
							$this->_AUX->PDODebugger($SQLQuery,$SQLQueryData));							
			return false;
		}
		
		if ($result->rowCount()==0) {
			$this->__errorLog.=sprintf("(No user found in selected status)[ %s ]",$status);
			return false;		
		}
		
		return true;
		
	}


    public function checkPassword($data)
	{
		/*
		Use to validate current user password
		
		requires:
			idUser : Integer	
			password : String
		
		 returns:
			Boolean
		*/		
		
		$this->__errorLog.=("<br>\n delete(init)");			
		
		
		#region « Variables Validation »
			$SQLCondition="";
			
			$SQLConditionCheck=function($str) {
				return $str==""?"WHERE":"AND";
			};		
			
			#idUser : Integer
				$idUser=isset($data["idUser"])?$data["idUser"]:0;        

				#required 
				if (!is_numeric($idUser)) {
					$this->__errorLog.=sprintf("->(error in idUser: %s)", $idUser);
					return false; 						
				}		
				
				$SQLQueryData[":idUser"]=$idUser;	
				#Condition Search 
				$SQLCondition.=sprintf(" %s `idUser`=:idUser",
				$SQLConditionCheck($SQLCondition));					
		
			#password : String
				$password=isset($data["password"])?$data["password"]:"";  
				
				if ($password=="") {
					$this->__errorLog.=sprintf("->(error in Password: %s)", $password);
					return false; 
				}		
				
				$SQLQueryData[":password"]=md5(preg_replace('/\s+/', $password));	
				#Condition Search 
				$SQLCondition.=sprintf(" %s `password`=:password",
				$SQLConditionCheck($SQLCondition));											
				
		#endregion


        $SQLQuery=sprintf("SELECT * FROM `users` %s",
							$SQLCondition);
					   
		#echo $this->_AUX->PDODebugger($SQLQuery,$SQLQueryData)."<br><br>"; die();
		$resultQuery = $this->_PDO->prepare($SQLQuery);						   
       
		if (!$resultQuery->execute($SQLQueryData)) {
			$this->__errorLog.=sprintf("->(error in Query)[ %s ]",$this->_AUX->PDODebugger($SQLQuery,$SQLQueryData));
			return false;
		}

		if ($result->rowCount()!=1) {
			$this->__errorLog.=sprintf("->(User not found)[ %s ]",$result->rowCount);
			return false;
		}

        return true;

    }

	#old mailValid()
    public function available($data) 
	{
	/*
	Use to validate if exist already user
	
	conditional:
		username : String
		email : String
	
	 returns:
		Boolean
	*/			
	
		$this->__errorLog.=("<br>\n available(init)");		
	
		#region « Variables Validation »
			$SQLCondition="";
			
			$SQLConditionCheck=function($str) {
				return $str==""?"WHERE":"OR";
			};			
		
		
			#username : String
			$username=isset($data["username"])?$data["username"]:"";  
			
			if ($username!="") {
				$SQLQueryData[":username"]=$username;	
				#Condition Search 
				$SQLCondition.=sprintf(" %s `username`=:username",
				$SQLConditionCheck($SQLCondition));							
			}					
				
			#email : String
			$email=isset($data["email"])?$data["email"]:"";  
			
			if ($email!="") {
				$SQLQueryData[":email"]=$email;	
				#Condition Search 
				$SQLCondition.=sprintf(" %s `email`=:email",
				$SQLConditionCheck($SQLCondition));							
			}	

			#require SQL condition
			if ($SQLCondition=="") 
			{				
				$this->__errorLog.=sprintf("->(No SQL Conditions)[ %s ]", $SQLCondition);					
				return false;
			}				
		
		#endregion

					   
        $SQLQuery=sprintf("SELECT * FROM `users` %s",
							$SQLCondition);
					   
		#echo $this->_AUX->PDODebugger($SQLQuery,$SQLQueryData)."<br><br>"; die();
		$resultQuery = $this->_PDO->prepare($SQLQuery);						   
       
		if (!$resultQuery->execute($SQLQueryData)) {
			$this->__errorLog.=sprintf("->(error in Query)[ %s ]",$this->_AUX->PDODebugger($SQLQuery,$SQLQueryData));
			return false;
		}	
       
		return true;
    }

    #region « PRIVATE »
    		
	private function dbTableCheck($data = array()) 
	{
		
		
	}
		
	private function dbTableCreate()
	{
	/*
	Use to validate if exist already user	
	
	returns:
		Boolean
	*/	

	
	}
	#endregion
	
}

?>
