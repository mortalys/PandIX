<?php

#INCLUDE MAIN CLASSES 
require_once ROOT.'/assets/backend/class/main/app/app.class.php';
require_once ROOT.'/assets/backend/class/main/common/common.api.php';

#region « MAIN »

    #app main settings
    $pandixInitData= [
               "aux" => new auxFunctions(),
               "root" => ROOT,
               "session" => $_SESSION
               ];        
	
    $pandix=new app($pandixInitData);    
    
    # init the application
    if (!$pandix->init([
						"fileSettings" => "./config.json"
						])) {
        echo $pandix->__errorLog;
        die();
    }    
	
	# get command from parameters
	$userRoute = $_REQUEST['c'];
	
	# pandix setup complete?
	if (!$pandix->_appData["setup"]) 
	{
		$userRoute = "installer.view.home";
	}

	#region « USERS required for Routing Permissions »
		/*
			Required to handle API request permissions - Routing
			Can bypass when no there is not users limitations
		*/
		$usersInitData= [
						   "db" => $pandix->_DBCON,
						   "dbTables" => $pandix->_appData["DB"]["TABLES"]["users"],	
						   "aux" => new auxFunctions(),
						   "idUser" => ($pandix->_SESSION['_idUser']>0?$pandix->_SESSION['_idUser']:0), #ADD ID USER GLOBAL
						   "userRoute" => $userRoute
						];
		
		$pandix->_USERS=new users($usersInitData);
		
		#EMERGENCY LOGOUT - kill switch
		#session_destroy();print_r($_SESSION);die();	
		
		//start app operations
		#user permissions are set in class USERS __constructor					
	#endregion 	

	
	
	# route the user
	$pandix->routing($pandix->_USERS->_userRoute);   
	
	#end application
	$pandix->terminate();

	
#endregion 

?>