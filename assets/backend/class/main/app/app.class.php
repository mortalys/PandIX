<?php

/*******************************************************************************
 *                      Application Function Class
 *******************************************************************************
 *      Author:     César Pinto 
 *      Website:    https://www.linkedin.com/in/cesargrancho/
 *
 *      File:       app.class.php
 *      Version:    2.0.1
 *                  
 *      License:    GPL
 *
 *******************************************************************************
 *  VERSION HISTORY:
 ******************************************************************************* 
 *      v2.0.1 [25.09.2020] 
 *		- updated routing for defaults views
 * 
 *      v2 [13.07.2018] 
 *		- updated main structure file directory
 *		- re-factoring
 *
 *      v1.1.3 [5.06.2019] 
 *		- updated on appType initSystem
 *
 *      v1.1.3 [18.07.2018] 
 *		- Add DevMode Debug Output
 *      	USAGE:
 *      	In API Response ( if devMode TRUE will pass the debug into JSON otherwise will be clear )
 *      	$API->__errorLog=($this->_devMode?$this->_CLASS->__errorLog:"")
 *
 *      v1.1.2 [15.12.2017] 
 *		- Fix default routing  files
 *
 *      v1.1.1 [18.11.2017] 
 *		- Permissions structure changes - moved into users class
 *
 *      v1.1.0 [16.11.2017]
 *      - Front-End Structure changes
 *
 *      v1.0.5 [8.11.2017]
 *      - Construct INIT changes, all vars now properly in array
 *      
 *      v1.0.4 [5.11.2017]
 *      - Support for SQLite
 *      
 *      v1.0.3 [28.10.2017]
 *      - Fixed session id conflict for multiple applications in same server
 *
 *      v1.0.2 [16.10.2017]
 *      - Add Memory and Execution limits
 *
 *      v1.0.0 [6.9.2017] 
 *		- Public Version Release
 *
 *******************************************************************************
 *  DESCRIPTION:
 *******************************************************************************
 *  The class provides basic configuration and initialization for the application
 *  
 *
 *******************************************************************************
 *  Functions Tree:
 *******************************************************************************
 *  
 *  .PUBLIC
 *  - init
 *  - routing
 *  
 *  .PRIVATE
 *  - initSystem
 *  -- sessionInit
 *  -- headersInit
 *  -- timezoneInit
 *  
 *  - initApp
 *  -- databaseInit
 *
 *  
 *******************************************************************************
 *  USAGE:
 *******************************************************************************
 *
 *  $this->dbOperations()
 *  
 *******************************************************************************
*/

class app  {

	public $_globalSettings;  # Array with all settings - used to save when exist changes
	private $_globalSettingsLoaded;  # Array with all original settings - used to compare for changes	
	
	public $_systemData;  # Array with system settings
	
    public $_appData;  # Array of app settings
    
    public $_APIActions; # used for view actions
    
    public $__errorLog; # USED to PARSE & DISPLAY ERRORS
    
    public $__ROOT; # dirname(__FILE__)
    
    #EXTERNAL OBJECTS
    public $_AUX;
    
    ###########################
    # PUBLIC FUNCTIONS : BEGIN
    ###########################    
    
    public function __construct($data = array())
    {	
		$this->_globalSettings=array();
		$this->_globalSettingsLoaded=array();
		$this->_systemData=array();        
        $this->_appData=array();        
        
        $this->__errorLog="";        

        $this->_AUX=$data['aux'];

        $this->__ROOT=$data['root'];        
    }
    
    public function init($data = array()) 
	{	
		/*
		 use to initialize main application configurations
		
		 returns:
			Boolean
		*/		
		
		#Load GLOBAL Settings
		if (!($this->_globalSettings=$this->configLoad($data["fileSettings"]))) {
			$this->__errorLog.= sprintf("Error in loading settings: %s .",
										$data["fileSettings"]
										);				
			return false;
		}		
        
		#set config settings to apache settings
        if (!is_array($this->_systemData=$this->initSystem())) {
            return false;
        }
        
		#set config settings to PandIX app
        if (!is_array($this->_appData=$this->initApp($this->_globalSettings['app']))) {
            return false;
        }        
        
        return true;
        
    }
    
    public function terminate()
	{
		/*
		 use terminate app operations
		
		 returns:
			Boolean
		*/

		#close DB connections
		$this->_DBCON=null; 
		
		#save settings if updated
		$this->configSave();

		return true;		
	}
    public function routing($routeData = "") 
	{
        
        #echo $routeData;
        $c=explode('.',$routeData);

		/*
		//ROUTE STRUTURE
		ie: domain.tools.operation - API PANDIX TOOLS REQUESTS
		ie: domain.do.operation - API CLASS EXTENSION REQUESTS
		ie: domain.view.page - LAYOUT OUTPUTS
		|-SECTION
		|--DO
		|---ACTIONS
		|--TOOLS
		|---ACTIONS
		|--VIEWS
		|---ACTIONS
		*/        
		
		
		$defaultDir = sprintf("%s/%s",
							ROOT,
							$this->_appData['PATHING']['FRONTEND']['DIR']); 
		
		#BACK END DEFAULTS
		$_classExtensions=$this->_appData['PATHING']['BACKEND']['EXTENDS'];
		$_classTools=$this->_appData['PATHING']['BACKEND']['TOOLS'];

		
		#FRONT END Defaults
		$defaultViewFile=sprintf("%s/%s/%s/index.php",
							$defaultDir,
							$this->_appData['PATHING']['FRONTEND']['DEFAULTS']['DOMAIN'],
							$this->_appData['PATHING']['FRONTEND']['DEFAULTS']['ACTION']); 
			
		
		if (count($c) > 0 
		&& $this->_AUX->stringValidation($c[0])) 
		{
			#DOMAIN
			$domain=$c[0];
				
			#MODE - do:view:mobile:...
			if ($this->_AUX->stringValidation($c[1])) 
			{
				$mode=$c[1];
			}
			
			#ACTIONS 				
			if ($this->_AUX->stringValidation($c[2])) 
			{
				$actions=$c[2];                    
				#used to do operations
				$this->_APIActions['actions']=$c[2];					
			}                
			
			#echo $domain."-".$mode."-".$actions;die();
			#build required file from request
			switch ($mode) 
			{
				case "do": $requiredFile = sprintf("%s/%s/%s.api.php",
										$_classExtensions,										
										$domain,
										$domain);				
				break;
				
				case "tools": $requiredFile = sprintf("%s/%s/%s.api.php",
								$_classTools,
								$domain,
								$domain); 
				break;
				
				default: 
				#VIEW
				$requiredFile = sprintf("%s/%s/%s/index.php",
								$defaultDir,
								$domain,
								$actions); 
					
				#required for HTML file includes
				$fullPath=sprintf("%s/%s",
									$defaultDir,
									$domain); 
			}
		}
		
		#echo json_encode(array('Result'=>'25','API_OPERATION_ERROR' => $GLOBAL_LNG['API_OPERATION_ERROR'])); #."-".$_REQUEST['cmd']        				
		if (!file_exists($requiredFile)) {
			#show default view
			$requiredFile = $defaultViewFile;

			#required for HTML file includes
			$fullPath=sprintf("%s/%s",
								$defaultDir,
								$this->_appData['PATHING']['FRONTEND']['DEFAULTS']['DOMAIN']); 					
		}      
		
        require $requiredFile;
               
    }

    ###########################
    # PUBLIC FUNCTIONS : BEGIN
    ###########################    
    
    ###########################
    # PRIVATE FUNCTIONS : BEGIN
    ###########################
    
    #region « system functions »
	
	private function configLoad($fileSettings) 
	{
		/*		
			load default config json file with app settings and start the "engines"
			
		 requires:
			fileSettings : String 
		
		 returns:
			Boolean
			Array
		*/		
		
		#check if already loaded - skip rest
		if (count($this->_globalSettings)>0) return $this->_globalSettings;
		
		#keep original settings for later compare
		return ($this->_globalSettingsLoaded=json_decode(file_get_contents($fileSettings), true));
		
	}
	
	private function configSave() 
	{
		//$arraysAreEqual = ($a === $b);
		
		return true;
	}		
	

	private function initSystem($data = array()) {
		
		try {
			
			$settings=$this->_globalSettings["system"];
			
			#dev mode : begin 
			if ($settings['devMode']) {
				ini_set('display_errors', 1);
				ini_set('display_startup_errors', 1);

				error_reporting(E_ALL & ~E_NOTICE ^ E_DEPRECATED);				
			}
			else 
			{
				ini_set('display_errors', 0);
				ini_set('display_startup_errors', 0);

				error_reporting(0);
			}
			
			#dev mode : end
			
			#memory Limits : begin
			$appMemory=$settings['memory'];
			if (is_numeric($appMemory)
				&& $appMemory>0) {
				ini_set('memory_limit',$appMemory);
			}                
			#memory Limits : end
			
			#timeout Limits : begin
			$appTimeout=$settings['timeout'];
			if (is_numeric($appTimeout)
				&& $appTimeout>0) {                    
				ini_set('max_execution_time', $appTimeout);
			}                
			#timeout Limits : end    
			
			
			$appType=$settings["base"]["type"];				
			#check if app type is valid
			if (!$this->_AUX->stringValidation($appType)) {
				$this->__errorLog.= sprintf("Error in system type: %s .",
											$appType
											);
				return false;
			}
			
			#start methods for each app type
			switch($appType)
			{
			case "HTTP":
				#session init
				if (!$this->sessionInit($settings['session'])) {
					return false;
				}; 
				
				#headers init
				if (!$this->headersInit($settings['headers'])) {
					return false;
				};
				
				#timezone init
				if (!$this->timezoneInit($settings['timezone'])) {
					return false;    
				}
				
				#http url init
				if (!$this->httpURLInit($settings["BASE"]["URL"])) {
					return false;    
				}	
			
			break;
			
			default: 
				$this->__errorLog = sprintf("No support for system type: %s",
											$appType
											);	
				return false; 												
			}
			
			
		} catch (Exception $e) {
			 $this->__errorLog = sprintf("Expection Found: %s",
										 $e->getMessage()
										);
		}
		
		return $settings;
		
	}
	
	#region « WWW SYSTEM »
	
		#session: begin
			private function sessionInit($sessionData = array()) {
				
				try {                    
					#region [ session settings ]
					
						$sessionName=$sessionData['name'];
						#check if session name is valid string
						if (!$this->_AUX->stringValidation($sessionName)) {
							$this->__errorLog.= sprintf("Error in session name: %s .",
														$sessionName
														);
							return false;                   
						}						
					
						#add session time limits
						$sessionExpiration=$sessionData['expiration'];
						#check if session expiration is numeric
						if (!is_numeric($sessionExpiration)) {
							$this->__errorLog.= sprintf("Error in session expiration: %s .",
														$sessionExpiration
														);
							return false;                   
						}       

						ini_set('session.gc_maxlifetime', $sessionExpiration);
						session_set_cookie_params($sessionExpiration);						
					
					
					#endregion [ session settings ]
					
					//SESSION START
					session_start(); 						
					
					#different session
					if ($_SESSION['siteID'] != $sessionName) {
						session_destroy();
						session_start();
						$_SESSION['siteID']=$sessionName;
						#echo $_SESSION['siteID'];die();
					}   
					
					return true;
				
				} catch (Exception $e) {
					 $this->__errorLog = sprintf("Expection Found: %s",
												 $e->getMessage()
												);
					 return false;
				}                
			}            
		#session: end
		
		#headers: begin
			private function headersInit($headers = array()) {
				
				try {
					
					#check if header is valid with array count
					if (count($headers)==0) {
						$this->__errorLog.= sprintf("Error in headers array: %s .",
													print_r($headers, true)
													);
						return false;                         
					}
					
					#start output of headers : begin
					foreach ($headers as $header=>$headerValue) {
						
						#check if header title is valid
						if (!$this->_AUX->stringValidation($header)) {
							$this->__errorLog.= sprintf("Error in header name: %s .",
														$header
														);
							continue;                 
						}
						
						#check if header value is valid
						if (!$this->_AUX->stringValidation($headerValue)) {
							$this->__errorLog.= sprintf("Error in header value: %s - %s.",
														$header,
														$headerValue
														);
							continue;                 
						}                        
					
						#format header output
						$headerOutput=sprintf("%s: %s",
											  $header,
											  $headerValue
											  );
						
						#output header
						#echo $headerOutput."<br>";
						header($headerOutput);
						
						
					}
					#start output of headers : end
					
					
					return true;
				
				} catch (Exception $e) {
					 $this->__errorLog = sprintf("Expection Found: %s",
												 $e->getMessage()
												);
					 return false;
				}
				
			}
		#headers : end
		
		#timezone : begin
			private function timezoneInit($timeZone = "") {
				
				try {                    
					date_default_timezone_set($timeZone);
					
					return true;
				
				} catch (Exception $e) {
					 $this->__errorLog = sprintf("Expection Found: %s",
												 $e->getMessage()
												);
					 return false;
				}                
			}  
		#timezone : end

		#region « url »
			private function httpURLInit($data = array()) {
				
				try {                    
					#Define Base URL for current HOST
					
					if ($data["BASE"]["URL"]=="null")
					{						
						$uri_parts = explode('?', $_SERVER['REQUEST_URI'], 2);
						$BASE_URL = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . "://$_SERVER[HTTP_HOST]$uri_parts[0]";						
						
						if (!($this->_globalSettings["app"]["BASE"]["URL"] = $BASE_URL))
						{
							$this->__errorLog = sprintf("No base URL defined: %s",
														$BASE_URL
														);					
							return false;
						}
					}	
				
				} catch (Exception $e) {
					 $this->__errorLog = sprintf("Expection Found: %s",
												 $e->getMessage()
												);
					 return false;
				}   

				return true;				
			} 		
		#endregion
	#endregion « WWW SYSTEM »
    
    #endregion « system functions »
    
    
    #app functions : begin
        private function initApp($data = array()) {
            
            try {
                
                #assign app values in array
				$settings=$data;
                
                #register and load required class              
                if (!$this->classesInit($settings["PATHING"])) {
                    return false;
                };                 

                    
				#database init
				if (!$this->databaseInit($settings["DB"])) {
					return false;
				}; 
                
                
            } catch (Exception $e) {
                 $this->__errorLog = sprintf("Expection Found: %s",
                                             $e->getMessage()
                                            );
            }
			
			return $settings;
            
        }
        
        #www app : begin
        
            #database : begin
                private function databaseInit($dbData = array()) {
                    
                    try {
                        
						switch($dbData['obdc']) {
							case null:
								return true;
							break;
							#region « PDO Settings »
							case "PDO":
							
								switch($dbData['type']) {
									case "mysql":
										$DBCON = sprintf('%s:host=%s;dbname=%s',$dbData['type'], $dbData['url'], $dbData['name']);
										$this->_DBCON = new PDO($DBCON, $dbData['user'], $dbData['pass']);                                									
									break;
									
									case "sqlite":
										$DBCON = sprintf('%s:%s',$dbData['type'], $dbData['name']);                                                                
										$this->_DBCON = new PDO($DBCON);                                       									
									break;
									
									default:
									
										$this->__errorLog = sprintf("No support for OBDC type: %s",
																	$dbData['type']
																	);	
										return false;																
								}
								
								#global settings								
								$this->_DBCON->query("SET CHARACTER SET utf8");	
								#$this->_DBCON->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
								$this->_DBCON->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);                            								
							
							break;
							#endregion
							
							default:
								$this->__errorLog = sprintf("No support for obdc: %s",
															$dbData['obdc']
															);
								
								return false;							
						}
                        
                    
                    } catch (Exception $e) {
                         $this->__errorLog = sprintf("Expection Found: %s",
                                                     $e->getMessage()
                                                    );
                         return false;
                    }   

					return true;					
                }  
            #database : end
            
            #classes : begin
                private function classesInit($classesData = array()) {
                    
                    try {
                      
                        function autoload( $class, $dir = null ) {
                        
                            if ( is_null( $dir ) )
                              $dir = "./assets/backend/class/extends/"; #$dir = $this->_appData['PATHING']['CLASSES']; 
                         
                            foreach ( scandir( $dir ) as $file ) {
                                
                              # directory?
                              if ( is_dir( $dir.$file ) && substr( $file, 0, 1 ) !== '.' )
                                autoload( $class, $dir.$file.'/' );
                         
                              # php file?
                              if ( substr( $file, 0, 2 ) !== '._' && preg_match( "/.php$/i" , $file ) ) {
                         
                                # filename matches class?
                                
                                if ( str_replace( '.php', '', $file ) == $class || str_replace( '.class.php', '', $file ) == $class ) {
                                    
                                    include $dir . $file;
                                }
                              }
                            }
                          }                        
                        
                        #try init extended classes                        
                        if (!spl_autoload_register( 'autoload' )) {
                            $this->__errorLog = sprintf("Error loading class: %s",
                                                        $class
                                                        );                            
                            return false;                              
                        }
                        
                        return true;
                    
                    } catch (Exception $e) {
                         $this->__errorLog = sprintf("Expection Found: %s",
                                                     $e->getMessage()
                                                    );
                         return false;
                    }                
                } 
            #classes : end
            
        #www app : end
        
    #app functions : end
    
    ###########################
    # PRIVATE FUNCTIONS : end
    ###########################    
}
?>