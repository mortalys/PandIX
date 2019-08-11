<?php

/*******************************************************************************
 *                      database Function Class
 *******************************************************************************
 *      Author:     César Pinto
 *      Email:      cesar.grancho@live.com.pt
 *      Website:    http://www.linkedin.com/in/cesargrancho/
 *
 *      File:       database.class.php
 *      Version:    1.0.1
 *      Copyright:  (c) 2017 César Pinto
 *                  
 *      License:    MIT
 *
 *******************************************************************************
 *  VERSION HISTORY:
 *******************************************************************************
 *
 *      v1.0.0 [11.9.2017] - Initial Version
 *
 *******************************************************************************
 *  DESCRIPTION:
 *******************************************************************************
 *  The class provides basic functions to handle some database operations
 *  
 *
 *******************************************************************************
 *  Functions Tree:
 *******************************************************************************
 *  
 *  .PUBLIC
 *  - dbClassGenerator (used to generate base class file from db)
 *  
 *  .PRIVATE
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
class database  {
    
    public $_dbData;  // Array of app vars    
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
        $this->_dbData=array();        
        
        $this->__errorLog="";
        
        $this->_AUX="";
        $this->_DBCON=$db;
    }
    
    public function classGenerator($data=array()) {
        
        # INIT REST API VAR : begin
        $APIResponse=array();
        $APIResponse['RESPONSE']=1; //DEFAULT TRUE
        $APIResponse['ERROR']='No errors';  //hmm defaults..
        # INIT REST API VAR : end
        
        #standardization variables names : begin
        
        $dbData=array();
        //system vars
        
        //app vars
        $dbData['tables']=$data[$data['formAlias'].'tables'];
                # !!! IMPORTANT  excluded tables IMPORTANT !!
        $dbData['tablesExcluded']=$data[$data['formAlias'].'tablesExcluded'];
        $dbData['tablesExcluded'][]="users"; //default
        #standardization variables names : end          
        
        
        # Data Validations : begin
            # Retrieved from array SETTINGS -> PATHING -> CLASSES
            if (!$this->_AUX->stringValidation($this->_appData['PATHING']['CLASSES'])) {
                $APIResponse['ERROR']=sprintf("No valid class path provided: %s",$this->_appData['PATHING']['CLASSES']);
                $APIResponse['RESPONSE']=25; 
                return $APIResponse;              
            }
        
        # Data Validations : end
              
        
        #call class generator
        if (!$this->dbClassGenerator($dbData)) {
            $APIResponse['ERROR']=sprintf("Error creating file(s): %s"
                                          ,$dbData['PATH_CLASS']
                                          ,$dbData['table']
                                          );
            $APIResponse['RESPONSE']=25; 
            return $APIResponse;             
        }        
        
        # send API response without errors
        return $APIResponse;
        
    }
    

    ###########################
    # PUBLIC FUNCTIONS : BEGIN
    ###########################    
    
    ###########################
    # PRIVATE FUNCTIONS : BEGIN
    ###########################    

    private function dbClassGenerator($data=array()) {
        
        #dbClassGenerator - private functions : begin
        function underscoreToCamelCase($string, $first = false) {
            $string = str_replace("_", " ", $string);
            $string = ucwords($string);
            $string = str_replace (" ", "", $string);
            if ($first) return ucfirst($string);
            else return lcfirst($string);
        }
        
        function camelCaseToUpperCaseName($string) {
            $string = preg_replace("/([^A-Z])([A-Z])/", "$1_$2", $string);
            return strtoupper($string);
        }
        
        function makeConstantName($field, $value) {
            preg_match("/'?([^']+)'?/", $value, $mc);
            $constant = $mc[1];
            return camelCaseToUpperCaseName($field) . "_" . camelCaseToUpperCaseName($constant);
        }     
        #dbClassGenerator - private functions : end
        
        
        # check if is to make a particular table or full db generator
        
        
        if ($this->_AUX->stringValidation($data['table'])) {
            $tables = array($data['table']);
        } else {
            $tables = $this->getTableList();
        }
        
        #print_r($tables);die();
        
        foreach ($tables as $table) {
            #check if table is to exclude
            if (in_array($table, $data['tablesExcluded']))
                continue;
            
            $fileOutput=""; #new object file
            
            #create file path : begin
                $classMain=""; #clear main class
                $classFilePath=sprintf("%s/%s/%s",
                                   $this->__ROOT
                                   ,str_replace(".", "", $this->_appData['PATHING']['CLASSES'])
                                   ,"%s"
                                   );
                
                $pathArray=explode("_",$table);
                #print_r($pathArray);die();
                
                if (count($pathArray)>0) {
                    #check if table has prefix
                    # ROOT/PATH_CLASS/SECTION/SECTION_SUBCLASS
                    $classMain=$pathArray[0];
                
                    $classDirPath=sprintf($classFilePath,
                                           $pathArray[0]."/"
                                           );
                    
                    $classFilePath=sprintf($classFilePath,
                                           $pathArray[0]."/".$pathArray[0]."_".$pathArray[1].".class.php"
                                           );
                }
                else {
                    #no prefix main section class
                    # ROOT/PATH_CLASS/SECTION/SECTION_MAINCLASS
                    $classFilePath=sprintf($classFilePath,
                                           $table
                                           );                
                }
                
                #echo $classFilePath;
            #create file path : end            
            
            
            #$cls = underscoreToCamelCase($table, true);
            $cls=$table;
            $aifield = null;
            $allkeys = array();
            $numberkeys = array();
            $textkeys = array();
        
        
            $fileOutput.= sprintf("<?php           
/*
 *******************************************************************************
 *                      %s Class
 *******************************************************************************
 *      Author:     César Pinto
 *      Email:      cesar.grancho@live.com.pt
 *      Website:    https://www.linkedin.com/in/cesargrancho/
 *
 *      File:       %s.class.php
 *      Version:    1.0.0
 *      Copyright:  (c) %s César Pinto
 *                  PAID license to use this file
 *      License:    %s
 *
 *******************************************************************************
 *  VERSION HISTORY:
 *******************************************************************************
 *   
 *      v1.0.0 [%s] - Initial Version
 *
 *******************************************************************************
 *  DESCRIPTION:
 *******************************************************************************
 *  
 *
 *  
 *******************************************************************************
 *  Functions Tree:
 *******************************************************************************
 *  
 *  .PUBLIC
 *  - loadRow
 *  - makeRow
 *  
 *  .PRIVATE
 *  
 *******************************************************************************
 *  USAGE:
 *******************************************************************************
 *  
 *  
 *******************************************************************************
*/\r\n\r\n"
            ,strtoupper($cls)
            ,$cls
            ,date("Y")
            ,$this->_appData['license']['to']
            ,date("d.m.Y")
            );
            
            if ($this->_AUX->stringValidation($classMain)) {
                #extended class
                $fileOutput.= sprintf("class %s extends %s {\r\n", $cls, $classMain);
            }
            else {
                $fileOutput.= sprintf("class %s {\r\n", $cls);
            }
            
            $fileOutput.= "\r\n";
        
            //Properties
            $tableData=array();
            $tableData['table']=$table;
            $fields = $this->describeTable($tableData);
            //print_r($fields); die();
            
            $keys = array();
            foreach ($fields as $field) {
                if ($field["Field"] != "id")
                    $fileOutput.= "    private $".$field["Field"].";\r\n";
                if (preg_match("/auto_increment/", $field["Extra"])) {
                    $aifield = $field;
                }
                if ($field["Key"] == "PRI") {
                    if (preg_match("/(int|float|double|numeric)/i", $field["Type"])) {
                        $numberkeys []= $field;
                    } else{
                        $textkeys []= $field;
                    }
                    $allkeys []= $field;
                    $keys []= $field["Field"];
                }
            }
            $fileOutput.= "\r\n\r\n";
            
            $fileOutput.= "    protected static \$TABLE = '$table';\r\n";
            $fileOutput.= "    public static \$KEYS = array('" . implode("', '", $keys) . "');\r\n";
            
            $fileOutput.= "\r\n\r\n";
            
            //Enum constants
            
            $enums = array();
            
            foreach ($fields as $field) {
                if (strpos($field["Type"], "enum") !== 0) continue;
                
                preg_match("/enum\(([^)]+)\)/", $field["Type"], $matches);
                $constants = explode(",", $matches[1]);
                foreach ($constants as $constant) {
                    $constantname = makeConstantName($field["Field"], $constant);
                    preg_match("/'?([^']+)'?/", $constant, $mc);
                    $constant = $mc[1];
                    $fileOutput.= "    const " . $constantname . " = '" . $constant . "';\r\n";
                }
                
                $fileOutput.= "\r\n";
                
                $fileOutput.= "    private static \$" . camelCaseToUpperCaseName($field["Field"]) . " = array(\r\n";
                foreach ($constants as $constant) {
                    $constantname = makeConstantName($field["Field"], $constant);
                    $fileOutput.= "        self::" . $constantname . ",\r\n";
                }
                $fileOutput.= "    );\r\n\r\n\r\n";
                
                $enums []= $field["Field"];
            }
            
            //Constructor
            
            $fileOutput.= "\r\n    public function __construct(";
            $params = array();
            foreach ($allkeys as $field) {
                $params []= "$" . $field["Field"];
            }
            $fileOutput.= implode(", ", $params) . ") {\r\n";
            
            foreach ($fields as $field) {
                if ($field["Key"] == "PRI") {
                    $fileOutput.= "        \$this->" . $field["Field"] . " = \$" . $field["Field"] . ";\r\n";
                } else if ($field["Default"] != NULL) {
                    $fileOutput.= "        \$this->" . $field["Field"] . " = ";
                    if (is_numeric($field["Default"]))
                        $fileOutput.= $field["Default"];
                    else if ($field["Default"] == "CURRENT_TIMESTAMP")
                        $fileOutput.= "date(\"Y-m-d H:i:s\")";
                    else if (strpos($field["Type"], "enum") === 0)
                        $fileOutput.= "self::" . makeConstantName($field["Field"], $field["Default"]);
                    else
                        $fileOutput.= "'".$field["Default"]."'";
                    $fileOutput.= ";\r\n";
                }
            }
            
            $fileOutput.= "    }\r\n\r\n\r\n\r\n";
            
            //loadRow
            
            $fileOutput.= "    public function loadRow(\$row) {\r\n";
            foreach ($fields as $field) {
                $f = $field["Field"];
                $fileOutput.= "        if (isset(\$row['" . $f . "'])) { \$this->" . $f . " = \$row['" . $f . "']; }\r\n";
            }
            $fileOutput.= "        return \$this;\r\n";
            $fileOutput.= "    }\r\n\r\n";
            
            //makeRow
            
            $fileOutput.= "    public function makeRow() {\r\n";
            $fileOutput.= "        return array(\r\n";
            foreach ($fields as $field) {
                $f = $field["Field"];
                $fileOutput.= "            '" . $f . "' => \$this->" . $f . ",\r\n";
            }
            $fileOutput.= "        );\r\n";
            $fileOutput.= "    }\r\n\r\n\r\n\r\n";
        
            //newFromId
            
            if (count($allkeys) > 1) {
                $fileOutput.= "    public static function newFromId(";
                $params = array();
                foreach ($allkeys as $field) {
                    $params []= "$" . $field["Field"];
                }
                $fileOutput.= implode(", ", $params) . ") {\r\n";
                
                $fileOutput.= "        \$obj = new $cls(";
                $params = array();
                foreach ($allkeys as $field) {
                    $params []= "$" . $field["Field"];
                }
                $fileOutput.= implode(", ", $params) . ");\r\n";
                
                $fileOutput.= "        if (\$obj->retrieve()) return \$obj;\r\n";
                $fileOutput.= "        return null;\r\n";
                
                $fileOutput.= "    }\r\n\r\n\r\n";
            }
            
            //newFromKEY
            
            if (count($allkeys) > 1) {
                foreach ($allkeys as $field) {
                    if ($field["Field"] == "id") continue;
                    
                    $fileOutput.= "    public static function newFrom" . underscoreToCamelCase($field["Field"], true) . "($" . $field["Field"] . ") {\r\n";
                    
                    if (array_search($field, $textkeys) !== false) {
                        $val = "\"' . mysqli_real_escape_string($" . $field["Field"] . ") . '\"'";
                    } else {
                        $fileOutput.= "        if (!self::db()->isNumber($" . $field["Field"] . ")) return array();\r\n";
                        $val = "' . $" . $field["Field"];
                    }
                        
                    $fileOutput.= "        \$rows = self::db()->select('SELECT $table.* FROM $table WHERE " . $field["Field"] . " = " . $val . ");\r\n";
                    
                    $fileOutput.= "        \$arr = array();\r\n";
                    $fileOutput.= "        foreach (\$rows as \$row) {\r\n";
                    
                    $fileOutput.= "            \$obj = new $cls(";
                    $params = array();
                    foreach ($allkeys as $field) {
                        $params []= "\$row['" . $field["Field"] . "']";
                    }
                    $fileOutput.= implode(", ", $params) . ");\r\n";
                    $fileOutput.= "            \$arr []= \$obj->loadRow(\$row);\r\n";
                    
                    $fileOutput.= "        }\r\n";
                    
                    $fileOutput.= "        return \$arr;\r\n";
                    $fileOutput.= "    }\r\n\r\n\r\n";
                    
                }
            }
            
            //Getters and Setters
            
            foreach ($fields as $field) {
                $f = $field["Field"];
                if ($f == "id") continue;
                $name = underscoreToCamelCase($f, true);
                $fileOutput.= "    public function get$name() { return \$this->$f; }\r\n";
                $fileOutput.= "    public function set$name(\$$f) { \$this->$f = \$$f; }\r\n";
            }
            $fileOutput.= "\r\n\r\n";
            
            //Getters for enum fields
            
            foreach ($enums as $enum) {
                $fileOutput.= "    public static function get" . underscoreToCamelCase($enum, true) . "List() { return self::\$" . camelCaseToUpperCaseName($enum) . "; }\r\n";
            }
            
            $fileOutput.= "\r\n";
            
            //Getters for related objects (WARNING: Requires must be added manually)
            
            foreach ($fields as $field) {
                if (strpos($field["Field"], "id_") !== 0) continue;
                preg_match("/id_(.+)/", $field["Field"], $matches);
                $objectname = underscoreToCamelCase($matches[1], true);
                $fileOutput.= "    public function get" . $objectname . "() {\r\n";
                
                if ($field["Null"] == "YES")
                    $fileOutput.= "        if (\$this->" . $field["Field"] . " == null) return null;\r\n";
                $fileOutput.= "        return " . $objectname . "::newFromId(\$this->" . $field["Field"] . ");\r\n";
                
                $fileOutput.= "    }\r\n\r\n";
            }
            
            $fileOutput.= "\r\n";
            
            //End of the class
            
            $fileOutput.= "}\r\n?>";
            
            #write file : begin
                if (!is_dir($classDirPath)) {
                  // dir doesn't exist, make it
                  mkdir($classDirPath);
                }
                
                #echo $classDirPath ." -- ".$classFilePath." yoo";die();
                if (!file_put_contents($classFilePath,$fileOutput))
                    return false;
                
                #change folder permission recursive
                
                $dirPermissionUpdate=sprintf("chmod 0777 %s -R"
                                             ,$classDirPath
                                             );
                exec ($dirPermissionUpdate);
                
            #write file : end
        
        }
        
        
        return true;
    }
    

    private function getTableList() {
        
        $SQL = "SHOW TABLES";
        
       #echo $SQL;die();
        
        $S = $this->_DBCON->prepare($SQL);
        $S->execute();
        $this->__errorLog.=sprintf("ERROR IN SQL: %s. ",$SQL);        

        //LOG USER
        if ($S->rowCount()>0) //DATA FOUND
            return $S->fetchAll(PDO::FETCH_COLUMN);

        return false;        
    }
    
    private function describeTable($data = array()) {
        
        $SQL = sprintf("SHOW COLUMNS FROM `%s`",
                       $data['table']);
        
       #echo $SQL;die();
        
        $S = $this->_DBCON->prepare($SQL);
        $S->execute();
        $this->__errorLog.=sprintf("ERROR IN SQL: %s. ",$SQL);        

        //LOG USER
        if ($S->rowCount()>0) //DATA FOUND
            return $S->fetchAll();

        return false;        
    }    
    
    
    
    ###########################
    # PRIVATE FUNCTIONS : end
    ########################### 
}

?>