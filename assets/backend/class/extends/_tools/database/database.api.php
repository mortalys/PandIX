<?php

#INIT CLASS
$db=new database($this->_DBCON);
$db->_AUX=new auxFunctions();
$db->__ROOT=$this->__ROOT;
$db->_appData=$this->_appData;

//INCLUDE USER LANGUAGE VARS
#require_once ROOT.'/lib/lng/'.$this->_LNG['KEY'].'/users/users.LNG.php';

    
$do=$this->_APIActions['actions'];

if ($do=="classGenerator") {
        /*
        Variables
        .PATH_CLASS
        .tables (optional)
        .tablesExcluded (optional)
        */

        echo json_encode($db->classGenerator($_REQUEST));  
}
else {
        echo json_encode(array('OPERATION_ERROR' => $this->LNG['DBOPERATIONS_OPERATION_ERROR'],
                               //'DevDebugger' => obj->__errorLog,
                               'RESULT' => 25));
}

?>
