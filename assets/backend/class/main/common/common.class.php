<?php

/*******************************************************************************
 *                      Common Function Class
 *******************************************************************************
 *      Author:     César Pinto
 *      Website:    https://www.linkedin.com/in/cesargrancho/
 *
 *      File:       auxFunctions.class.php
 *      Version:    1.0.9
 *
 *      License:    GPL
 *                  
 *
 *******************************************************************************
 *  VERSION HISTORY:
 *******************************************************************************
 *      v1.0.9 [23.09.2020] - Latest Revision - added (isURL)
 *
 *      v1.0.8 [23.05.2019] - Latest Revision
 *
 *      v1.0.0 [6.9.2017] - Initial Version
 *
 *******************************************************************************
 *  DESCRIPTION:
 *******************************************************************************
 *  The class provides global functions that can be re-used in several objects
 *  
 *
 *******************************************************************************
*/

class auxFunctions  {

    public $__errorLog; // USED to PARSE & DISPLAY ERRORS
    
    public function __construct()
    {
        $this->__errorLog="";
    }
    
    public function mres($value)
    {
        #used to replace mysql_real_escape_string
        #skip following chars: \x00, \n, \r, \, ', " and \x1a
        $search = array("\\",  "\x00", "\n",  "\r",  "'",  '"', "\x1a");
        $replace = array("\\\\","\\0","\\n", "\\r", "\'", '\"', "\\Z");

        return str_replace($search, $replace, $value);
    }
	
    public function curl_get_contents($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); 
        curl_setopt($ch, CURLOPT_TIMEOUT, 12); //timeout in seconds		
        $data = curl_exec($ch);
        curl_close($ch);
    
        return $data;
    }

    public function multiKeyExists(array $arr, $key) {
        // is in base array?
        if (array_key_exists($key, $arr)) {
            return $arr[$key];//return true;
        }
    
        // check arrays contained in this array
        foreach ($arr as $element) {
            if (is_array($element)) {
                if ($this->multiKeyExists($element, $key)) {                    
                    return $element[$key];//return true;
                    
                }
            }
    
        }
    
        return false;
    } 

    public function convertFloat($floatAsString) {
		#CONVERT FLOAT TO STRING READING
		
		#8 decimals    
        return preg_replace('/(?<=\d{2})0+$/', '', number_format($floatAsString, 8, '.', ''));
    }	
	
    public function formInputArrayToString($values) {
        $stringValue="";
        foreach($values as $id=>$value) {
            $stringValue.=$value.";";
        }
        return substr_replace($stringValue, "", -1);
    }
    
    public function stringValidation($string) {		
        return (isset($string)&&$string!=""?true:false);
    }
    
    public function mailValidation($email) {  
            return ((filter_var($email, FILTER_VALIDATE_EMAIL))?true:false);
    }    
    
    public function creditCardType($ccNumber) {
        $cards = array(
            // Debit cards must come first, since they have more specific patterns than their credit-card equivalents.
            'visaelectron' => array(
                'type' => 'visaelectron',
                'pattern' => '/^4(026|17500|405|508|844|91[37])/',
                'length' => array(16),
                'cvcLength' => array(3),
                'luhn' => true,
            ),
            'maestro' => array(
                'type' => 'maestro',
                'pattern' => '/^(5(018|0[23]|[68])|6(39|7))/',
                'length' => array(12, 13, 14, 15, 16, 17, 18, 19),
                'cvcLength' => array(3),
                'luhn' => true,
            ),
            'forbrugsforeningen' => array(
                'type' => 'forbrugsforeningen',
                'pattern' => '/^600/',
                'length' => array(16),
                'cvcLength' => array(3),
                'luhn' => true,
            ),
            'dankort' => array(
                'type' => 'dankort',
                'pattern' => '/^5019/',
                'length' => array(16),
                'cvcLength' => array(3),
                'luhn' => true,
            ),
            // Credit cards
            'visa' => array(
                'type' => 'visa',
                'pattern' => '/^4/',
                'length' => array(13, 16),
                'cvcLength' => array(3),
                'luhn' => true,
            ),
            'mastercard' => array(
                'type' => 'mastercard',
                'pattern' => '/^(5[0-5]|2[2-7])/',
                'length' => array(16),
                'cvcLength' => array(3),
                'luhn' => true,
            ),
            'amex' => array(
                'type' => 'amex',
                'pattern' => '/^3[47]/',
                'format' => '/(\d{1,4})(\d{1,6})?(\d{1,5})?/',
                'length' => array(15),
                'cvcLength' => array(3, 4),
                'luhn' => true,
            ),
            'dinersclub' => array(
                'type' => 'dinersclub',
                'pattern' => '/^3[0689]/',
                'length' => array(14),
                'cvcLength' => array(3),
                'luhn' => true,
            ),
            'discover' => array(
                'type' => 'discover',
                'pattern' => '/^6([045]|22)/',
                'length' => array(16),
                'cvcLength' => array(3),
                'luhn' => true,
            ),
            'unionpay' => array(
                'type' => 'unionpay',
                'pattern' => '/^(62|88)/',
                'length' => array(16, 17, 18, 19),
                'cvcLength' => array(3),
                'luhn' => false,
            ),
            'jcb' => array(
                'type' => 'jcb',
                'pattern' => '/^35/',
                'length' => array(16),
                'cvcLength' => array(3),
                'luhn' => true,
            ),
        );
        
        foreach ($cards as $type => $card) {
            if (preg_match($card['pattern'], $ccNumber)) {
                return $type;
            }
        }
        return '';    
    }    
	public function encryptString($data=array()) {
		$string=$data["string"];
		$password=$data["password"];		
		$mode=$data["mode"]; #1,2...	
		
		switch($mode) {
			default:
			$mode="AES-256-CFB";
		}		
		
		return openssl_encrypt($string,$mode,$password);		
	}
	
	public function decryptString($data=array()) {
		$string=$data["string"];
		$password=$data["password"];		
		$mode=$data["mode"]; #1,2...	
		
		switch($mode) {
			default:
			$mode="AES-256-CFB";
		}			
		
		return openssl_decrypt($string,$mode,$password);	
	}	
	
	public function secondsToTime($seconds) {
		$dtF = new \DateTime('@0');
		$dtT = new \DateTime("@$seconds");
		return $dtF->diff($dtT)->format('%a days, %h hours, %i minutes and %s seconds');
	}
    
    static public function isURL($url) {
        return filter_var($url, FILTER_VALIDATE_URL);
    }    

    static public function PDODebugger($raw_sql, $parameters)
    {
		
        $keys = array();
        $values = array();

        /*
         * Get longest keys first, sot the regex replacement doesn't
         * cut markers (ex : replace ":username" with "'joe'name"
         * if we have a param name :user )
         */
        $isNamedMarkers = false;
        if (count($parameters) && is_string(key($parameters))) {
            uksort($parameters, function($k1, $k2) {
                return strlen($k2) - strlen($k1);
            });
            $isNamedMarkers = true;
        }
        foreach ($parameters as $key => $value) {

            // check if named parameters (':param') or anonymous parameters ('?') are used
            if (is_string($key)) {
                $keys[] = '/:'.ltrim($key, ':').'/';
            } else {
                $keys[] = '/[?]/';
            }

            // bring parameter into human-readable format
            if (is_string($value)) {
                $values[] = "'" . addslashes($value) . "'";
            } elseif(is_int($value)) {
                $values[] = strval($value);
            } elseif (is_float($value)) {
                $values[] = strval($value);
            } elseif (is_array($value)) {
                $values[] = implode(',', $value);
            } elseif (is_null($value)) {
                $values[] = 'NULL';
            }
        }
        if ($isNamedMarkers) {
            return preg_replace($keys, $values, $raw_sql);
        } else {
            return preg_replace($keys, $values, $raw_sql, 1, $count);
        }
    }	
		
	static public function isJson($string) {
		json_decode($string);
		return (json_last_error() == JSON_ERROR_NONE);
	}
}

?>