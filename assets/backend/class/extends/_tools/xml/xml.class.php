<?php

/*******************************************************************************
 *                      XML Function Class
 *******************************************************************************
 *      Author:     César Pinto
 *      Email:      cesar.grancho@live.com.pt
 *      Website:    http://www.linkedin.com/in/cesargrancho/
 *
 *      File:       xml.class.php
 *      Version:    1.0.0
 *      Copyright:  (c) 2017 César Pinto
 *                  
 *      License:    MIT
 *
 *******************************************************************************
 *  VERSION HISTORY:
 *******************************************************************************
 *
 *      v1.0.0 [18.10.2017] - Initial Version
 *
 *******************************************************************************
 *  DESCRIPTION:
 *******************************************************************************
 *  The class provides basic functions to handle some xml operations
 *  
 *
 *******************************************************************************
 *  Functions Tree:
 *******************************************************************************
 *  
 *  .PUBLIC
 *  - XMLtoCSV (used to generate csv file from xml)
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
class xml  {
    public $_xmlData;  // Array of app vars    
    public $__errorLog; // USED to PARSE & DISPLAY ERRORS
    
    
    //EXTERNAL OBJECTS
    public $_AUX;
    public $_DBCON;
    public $__ROOT;
    public $_appData;
    
    ###########################
    # PUBLIC FUNCTIONS : BEGIN
    ###########################    
    
    public function __construct($db) {
        $this->_xmlData=array();        
        
        $this->__errorLog="";
        
        $this->_AUX="";
        $this->_DBCON=$db;
    }
    
    public function csvGenerator($data=array()) {
        
        # INIT REST API VAR : begin
        $APIResponse=array();
        $APIResponse['RESPONSE']=1; //DEFAULT TRUE
        $APIResponse['ERROR']='No errors';  //hmm defaults..
        # INIT REST API VAR : end
        
        #standardization variables names : begin        

        #standardization variables names : end
        
		$filename=$this->_appData['PATHING']['FRONTEND']['UPLOAD_DIR']."testing.xml"; #reading file
		
		$fileoutput=$this->_appData['PATHING']['FRONTEND']['UPLOAD_DIR']."output.csv"; #output file
		
		$xml = new XMLReader;
		$xml->open($filename);
		
		
		file_put_contents($fileoutput, "name,email,phone,dob,credit card type\r\n");
		
		$i=0;

		while ($xml->read()) {    
		  if ($xml->nodeType == XMLReader::ELEMENT
			  && $xml->name=="person" ) {
				$xml->moveToElement();
				$data = new SimpleXMLElement($xml->readOuterXml());        
				
				$data->emailaddress = $this->_AUX->mailValidation(substr($data->emailaddress, strpos($data->emailaddress,':') + 1))?substr($data->emailaddress, strpos($data->emailaddress,':') + 1):"" ;
				
				$data->phone = is_numeric($data->phone)?$data->phone:"" ;
				
				$dob= $data->profile->age>0?(date('Y', time())  - $data->profile->age):"";
				
				$data->creditcard=$this->_AUX->creditCardType(str_replace(' ', '', $data->creditcard));
		

				
				file_put_contents($fileoutput,
								  sprintf("%s,%s,%s,%s,%s,%s \r\n",
								  $data->name,
								  $data->emailaddress,
								  $data->phone,
								  $dob,
								  $data->creditcard,
								  $interests),
								  FILE_APPEND);
				
				$i++;    
				$nl=$i % 100;
				
				#Handle output buffer
				#if($nl==0) return $i;				
				#ob_flush(); flush();        
		  }
		}
		
		$xml->close();		
        
        # send API response without errors
        return $APIResponse;
        
    }
    

    ###########################
    # PUBLIC FUNCTIONS : BEGIN
    ###########################    
    
    ###########################
    # PRIVATE FUNCTIONS : BEGIN
    ###########################   

    
    
    ###########################
    # PRIVATE FUNCTIONS : end
    ########################### 
}

