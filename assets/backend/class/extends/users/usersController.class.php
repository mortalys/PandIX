<?php

/*
 *******************************************************************************
 *                      USERS Controller Class
 *******************************************************************************
 *      Author:     César Pinto 
 *      Website:    https://www.linkedin.com/in/cesargrancho/
 *
 *      File:       users.class.php
 *      Version:    1.0.0 
 *                  
 *      License:    GPL
 *
 *******************************************************************************
 *  VERSION HISTORY:
 *******************************************************************************
 *      v1.0.1 [23.05.2019] 
 *		- Added System Data to controller
 *      
 *      v1.0.0 [23.05.2019] 
 *		- Users Controller created
 *
 *******************************************************************************
 *  DESCRIPTION:
 *******************************************************************************
 *  This controller is intended to mix different "DOMAINS" operations 
 *  related to Domain "USERS"
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


class usersController {

	//core API
	private $_systemData;
	private $_appData;	
	private $_devMode;			
	
	private $_REQUEST;
	private $_OUTPUT;
	
	//idUser - current user requesting operations
	private $_idUser;	
	
	#Domains - DI High Level Modules
	private $_USERS;	
	
	#External Libraries - DI Low Level Modules
	private $_AUX;

	public function __construct($data=array()) 
	{
		$this->_systemData = $data["systemData"];
		$this->_appData = $data["appData"];		
		$this->_devMode = $this->_systemData["devMode"];

		$this->_devMode = $data["devMode"];	
		
		$this->_AUX = $data["aux"];
		$this->_OUTPUT = $data["output"];
		$this->_REQUEST = $data["request"];
					
		
		$this->_USERS= $data['users'];		
	}			

	#region « CORE API Functions »
	
	private static function apiInit($data=array()) 
	{		
		return (object) array(
					"RESPONSE" => 1,
					"ERROR" => 'No errors',
					"__errorLog" => "",
					"data" => array()
				);				 	
	}	


	public function apiError($data=array()) {
		
		#we can add some data to throw in errorLog
		
		return (object) array(
					"RESPONSE" => 25,
					"ERROR" => "API Error - Nothing to display",
					"__errorLog" => ($this->_devMode?"Error in API Request":""),
					"data" => array()
				);			 	
	}
	
	public function output($data=array()) {
	
		switch ($this->_OUTPUT) {
			
			default:
			#json
			return json_encode($data);
		}
		
	}	
	
	#endregion « CORE API Functions »
	
	#add available() to check mail, username
	
	public function addNew($data = array()) 
	{
		//set API Response
		$API=$this->apiInit();
		
		
		//data sent - do validations : begin
		#print_r($data);die();
		$sectionAlias=$data['sectionAlias'];
		
		$d=array();
		$d['idUser']=0;        
		$d['username']=isset($data[$sectionAlias.'username'])?$data[$sectionAlias.'username']:"";
		$d['password']=isset($data[$sectionAlias.'password'])?$data[$sectionAlias.'password']:"";		
		
		
		
		//data sent - do validations : end
		// update
		if ($this->_USERS->userExist($d)) {
			$API->RESPONSE=25; //Error
			$API->ERROR='User already exist';
			$API->__errorLog=($this->_devMode?$this->_USERS->__errorLog:"");
			return $API;  			
		}		
		
		if (!$this->add($d)) {
			$APIResponse['RESPONSE']=25; //Error
			$APIResponse['ERROR']='Error adding new user';
			return $APIResponse; 
		}        
		
		
		$d['idUser']=$this->_userData['idUser']; //assign idUser Data from adding - GLOBAL
		if (!$this->addDetailed($d)) { //SOMETHING WENT WRONG ADDING NEW MALL                        
			$APIResponse['RESPONSE']=25; //Error
			$APIResponse['ERROR']='Error adding user details';
			return $APIResponse;  
		}
		
		return $APIResponse;  
	}    
	
	#re-work ( needs to remove some code logic )
	public function usersList($data=array())
	{
			  
		$_idUser=is_numeric($data['idUser'])?$data['idUser']:0;        
		
		/* INIT REST API VAR */
		$APIResponse=array();
		$APIResponse['RESPONSE']=1; //DEFAULT FALSE
		$APIResponse['ERROR']='No errors';  //hmm defaults..           
		
		//ADD QUERY CONDITION FOR SINGLE USER
		if (is_numeric($_idUser)
			&& $_idUser>0) {
			$queryCondition=sprintf("WHERE `a`.`idUser` = %s",$_idUser);
		}
		
		$SQL=sprintf("SELECT `a`.*
					FROM `users` `a`                  
					%s
					ORDER BY `a`.`idUser` DESC",
					 $queryCondition);
		
		#echo $SQL;die();
		$query = $this->_USERS->_PDO->prepare($SQL);
		$query->execute();        

		$this->__errorLog.=sprintf("QUERY TO LIST User List: %s",$SQL);
		
		#echo $this->__errorLog;
		$results=$query->rowCount();
		if ($results>0) { //there is malls
			
			//count query results
			$APIResponse['results']=$results;
			
			//ASSIGN DATA
			$i =0;
			while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
				
				
				$APIResponse['data'][$i]=$row;
				$APIResponse['data'][$i]['tools']='p e x';
				
				
				$APIResponse['data'][$i]['registrationDate']=date('m/d/Y', $row['registrationDate']);                               

				unset($APIResponse['data'][$i]['password']);
				unset($APIResponse['data'][$i]['mediaFile']);
				$i++;
			};
		}
		else
		{
			$APIResponse['data'][$i]=array();
		}
		
		return $APIResponse;         
	}
	
	#re-work ( needs to remove some code logic )
	public function usersStatusUpdate($data=array()) 
	{
		//API Response : begin
		$APIResponse=array();
		$APIResponse['RESPONSE']=1; //Success
		$APIResponse['ERROR']='No Errors'; //No Errors
		//API Response : end
		
		
		//data sent - do validations : begin        
		$d=array();
		$_idUser=is_numeric($data['idUser'])?$data['idUser']:0;
		$_status=is_numeric($data['status'])?$data['status']:0;
		
		if ($_idUser==0
			&& $_status<0) {
			$APIResponse=array();
			$APIResponse['RESPONSE']=25;
			$APIResponse['ERROR']='Error in User or Status'; 
		}
		
		$SQL=sprintf("UPDATE `users` 
					   SET
					   `status` = '%s'
					   WHERE `idUser` = '%s'",   
					   $_status,
					   $_idUser);

	   #echo $SQL;die();
	   $result = $this->_PDO->prepare($SQL);            
		
	
		if (!$result->execute()) {
			$APIResponse['ERROR']=sprintf("Error in query");            
			$APIResponse['RESPONSE']=25; 
		}
		
		
		return $APIResponse;

	}
	
	#re-work ( needs to remove some code logic )
	public function groupUpdate($data=array()) 
	{
		//API Response : begin
		$APIResponse=array();
		$APIResponse['RESPONSE']=1; //Success
		$APIResponse['ERROR']='No Errors'; //No Errors
		//API Response : end
		
		
		//data sent - do validations : begin        
		$d=array();
		$_idUser=is_numeric($data['idUser'])?$data['idUser']:0;
		$_idGroup=is_numeric($data['idGroup'])?$data['idGroup']:0;
		
		if ($_idUser==0
			&& $_idGroup<1) {
			$APIResponse=array();
			$APIResponse['RESPONSE']=25;
			$APIResponse['ERROR']='Error in User or Status'; 
		}
		
		$SQL=sprintf("UPDATE `users` 
					   SET
					   `idGroup` = '%s'
					   WHERE `idUser` = '%s'",   
					   $_idGroup,
					   $_idUser);

	   #echo $SQL;die();
	   $result = $this->_PDO->prepare($SQL);            
		
	
		if (!$result->execute()) {
			$APIResponse['ERROR']=sprintf("Error in query");            
			$APIResponse['RESPONSE']=25; 
		}
		
		
		return $APIResponse;

	}    
	
	#re-work (needs to update function names )
	public function usersEdit($data=array()) 
	{
		//API Response : begin
		$APIResponse=array();
		$APIResponse['RESPONSE']=1; //Success
		$APIResponse['ERROR']='No Errors'; //No Errors
		//API Response : end
		
		
		//data sent - do validations : begin
		#rint_r($data);die();
		$sectionAlias=$data['sectionAlias'];
		
		$d=array();
		$d['idUser']=is_numeric($data[$sectionAlias.'idUser'])?$data[$sectionAlias.'idUser']:0;
		$d['idMedia']=is_numeric($data[$sectionAlias.'idMedia'])?$data[$sectionAlias.'idMedia']:0;
		$d['idGroup']=is_numeric($data[$sectionAlias.'idGroup'])?$data[$sectionAlias.'idGroup']:0;
		$d['status']=is_numeric($data[$sectionAlias.'status'])?$data[$sectionAlias.'status']:0;
		$d['idMall']=is_numeric($data[$sectionAlias.'idMall'])?$data[$sectionAlias.'idMall']:0;
		$d['idStore']=is_numeric($data[$sectionAlias.'idStore'])?$data[$sectionAlias.'idStore']:0;
		$d['email']=isset($data[$sectionAlias.'email'])?$data[$sectionAlias.'email']:"";
		$d['username']=isset($data[$sectionAlias.'username'])?$data[$sectionAlias.'username']:"";
		$d['password']=isset($data[$sectionAlias.'password'])?$data[$sectionAlias.'password']:"";
		$d['registrationCode']=is_numeric($data[$sectionAlias.'registrationCode'])?$data[$sectionAlias.'registrationCode']:0;        
		$d['nameFirst']=isset($data[$sectionAlias.'nameFirst'])?$data[$sectionAlias.'nameFirst']:"";
		$d['nameLast']=isset($data[$sectionAlias.'nameLast'])?$data[$sectionAlias.'nameLast']:"";
		$d['phone']=isset($data[$sectionAlias.'phone'])?$data[$sectionAlias.'phone']:"";
		$d['address']=isset($data[$sectionAlias.'address'])?$data[$sectionAlias.'address']:"";
		$d['city']=isset($data[$sectionAlias.'city'])?$data[$sectionAlias.'city']:"";
		$d['zipCode']=isset($data[$sectionAlias.'zipCode'])?$data[$sectionAlias.'zipCode']:"";
		$d['state']=isset($data[$sectionAlias.'state'])?$data[$sectionAlias.'state']:"";
		
		$d['idAgeGroup']=is_numeric($data[$sectionAlias.'idAgeGroup'])?$data[$sectionAlias.'idAgeGroup']:0;
		$d['birthday']=strtotime($data[$sectionAlias.'birthday'])?strtotime($data[$sectionAlias.'birthday']):0;
		$d['idUserTag']=isset($data[$sectionAlias.'idUserTag'])?$this->_AUX->formInputArrayToString($data[$sectionAlias.'idUserTag']):0;          
		//data sent - do validations : end
		
		if (!is_numeric($d['idUser'])
			&& $d['idUser']==0) {
			$APIResponse['RESPONSE']=25; //Error
			$APIResponse['ERROR']='User not defined';
			return $APIResponse;  
		}
		
		($d['password']!="")?$this->_USERS->passwordUpdate($d):0;        
		
		if (!$this->_USERS->edit($d)) {
			$APIResponse['RESPONSE']=25; //Error
			$APIResponse['ERROR']='Error edit user';
			return $APIResponse; 
		}
		
		if (!$this->_USERS->editDetails($d)) {
			$APIResponse['RESPONSE']=25; //Error
			$APIResponse['ERROR']='Error edit user details';
			return $APIResponse; 
		}
		
		return $APIResponse; 
	}
	
	#re-work (needs to update function names )
	public function usersRemove($data = array()) 
	{
		//API Response : begin
		$APIResponse=array();
		$APIResponse['RESPONSE']=1; //Success
		$APIResponse['ERROR']='No Errors'; //No Errors
		//API Response : end
		
		$d=array();
		$d['idUser']=is_numeric($data['idUser'])?$data['idUser']:0;
		
		if (!is_numeric($d['idUser'])
			&& $d['idUser']==0) {
			$APIResponse['RESPONSE']=25; //Error
			$APIResponse['ERROR']='User not defined';
			return $APIResponse;  
		}
		
		if (!$this->_USERS->remove($d)) {
			$APIResponse['RESPONSE']=25; //Error
			$APIResponse['ERROR']='Error deleting user';
			return $APIResponse; 
		}        
		
		return $APIResponse;          
		
	}
	
	#re-work (needs to update function names )
	#usersResetPw
	public function passwordReset($data = array()) 
	{
		//Set API Response 
		$API=$this->apiInit();		
		
		#validate logged user
		
		
		#generate password			            
            $_password=mt_rand(10300100, 59939139);
		
		#make update request
		
		//data sent - do validations : begin
		#print_r($data);die();
		$sectionAlias=$data['sectionAlias'];
		
		$d=array();
		$d['idUser']=is_numeric($data[$sectionAlias.'idUser'])?$data[$sectionAlias.'idUser']:0;
		$d['password']=isset($data[$sectionAlias.'password'])?$data[$sectionAlias.'password']:"";
		
		//data sent - do validations : begin
		
		if ($d['password']=="" || $d['idUser']==0 ||
			!$this->_USERS->passwordUpdate($d)) {                
			$APIResponse['RESPONSE']=25; //Error
			$APIResponse['ERROR']='Error updating user password';
			return $APIResponse; 
		}        
		
		return $APIResponse;         
	}
	
	#re-work (needs to update function names )
	#usersResetPw
	public function passwordUpdate($data = array()) 
	{
		//Set API Response 
		$API=$this->apiInit();
		
		
		
		#check for old password
		#checkPassword()
		
		//data sent - do validations : begin
		#print_r($data);die();
		$sectionAlias=$data['sectionAlias'];
		
		$d=array();
		$d['idUser']=is_numeric($data[$sectionAlias.'idUser'])?$data[$sectionAlias.'idUser']:0;
		$d['password']=isset($data[$sectionAlias.'password'])?$data[$sectionAlias.'password']:"";
		
		//data sent - do validations : begin
		
		if ($d['password']=="" || $d['idUser']==0 ||
			!$this->_USERS->passwordUpdate($d)) {                
			$APIResponse['RESPONSE']=25; //Error
			$APIResponse['ERROR']='Error updating user password';
			return $APIResponse; 
		}        
		
		return $APIResponse;         
	}	

	public function usersLogin($data=array()) 
	{						
		//set API Response
		$API=$this->apiInit();
		
		#standardization variables names : begin
		$userData=array();
		$userData['email']=$data[$data['formAlias'].'email'];
		$userData['password']=$data[$data['formAlias'].'password'];
		#standardization variables names : end          
		
		
		# Data Validations : begin

		if (!$this->_AUX->stringValidation($userData['email'])) {
			$API->ERROR=sprintf("No valid email: %s",$userData['email']);
			$API->RESPONSE=25; 
			return $API;              
		}
		
		if (!$this->_AUX->stringValidation($userData['password'])) {
			$API->ERROR=sprintf("No valid password: %s",$userData['password']);
			$API->RESPONSE=25; 
			return $API;              
		}        
	 
		# Data Validations : end        
		
		//try login		
		if (!$this->_USERS->login($userData)) {
			$API->ERROR=sprintf("Invalid login: %s - %s",$userData['email'], $userData['password']);
			$API->RESPONSE=25; 
			return $API;             
		}
		
		return $API;
	}	
	
	public function logout()
	{			
		session_destroy();			
		header("Refresh:0; url=index.php");						
	}  		
	
};
	

?>
