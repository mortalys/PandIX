<?php

#Init User Variable - can be override by admin
$_idUser=$this->_USERS->_idUser;

$apiData=[
	"systemData" => $this->_systemData,
	"appData" => $this->_appData,		
	"aux" => new auxFunctions(),
	"output" => "json",
	"request" => $_REQUEST,
	"users" => $this->_USERS,
	"idUser" => $_idUser
];

#no need to init USERS, already @ app.api for permission/routing


#api controller init
$api= new usersController($apiData);


#api method exist?
if (!method_exists($api,$this->_APIActions['actions'])) {
	
	echo $api->output($api->apiError());	
	exit();
}

#call dynamic method
echo $api->output($api->{$this->_APIActions['actions']}($_REQUEST));


?>
