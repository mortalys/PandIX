<?php

#INIT CLASS
$x=new xml($this->_DBCON);
$x->_AUX=new auxFunctions();
$x->__ROOT=$this->__ROOT;
$x->_appData=$this->_appData;

//INCLUDE USER LANGUAGE VARS
#require_once ROOT.'/lib/lng/'.$this->_LNG['KEY'].'/users/users.LNG.php';

    
$do=$this->_APIActions['actions'];

if ($do=="csvGenerator") {
        /*
        Variables
        .PATH_CLASS
        .tables (optional)
        .tablesExcluded (optional)
        */

        echo json_encode($x->csvGenerator($_REQUEST));  
}
else {
        echo json_encode(array('OPERATION_ERROR' => $this->LNG['DBOPERATIONS_OPERATION_ERROR'],
                               //'DevDebugger' => obj->__errorLog,
                               'RESPONSE' => 25));
}

?>
