<?php

/*******************************************************************************
 *                      Common Function Class
 *******************************************************************************
 *      Author:     César Pinto
 *      Website:    https://www.linkedin.com/in/cesargrancho/
 *
 *      File:       Encryptor.class.php
 *      Version:    1.0.0
 *
 *      License:    GPL
 *                  
 *
 *******************************************************************************
 *  VERSION HISTORY:
 *******************************************************************************
 *
 *      v1.0.0 [06.10.2020] - Initial Version
 *
 *******************************************************************************
 *  DESCRIPTION:
 *******************************************************************************
 *  The class provides encryption functions 
 *  
 *
 *******************************************************************************
*/

class Encryptor
{

    /**
     * Holds the Encryptor instance
     * @var Encryptor
     */
    private static $instance;

    /**
     * @var string
     */
    private $method;

    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $separator;

    /**
     * Encryptor constructor.
     */
    public function __construct($data)
    {   
        $this->method = $data['method'];
        $this->key = $data['key'];
        $this->separator = ':';
    }

    private function __clone()
    {
    }

    /**
     * Returns an instance of the Encryptor class or creates the new instance if the instance is not created yet.
     * @return Encryptor
     */
    public static function getInstance($data)
    {
        if (self::$instance === null) {
            self::$instance = new Encryptor($data);
        }
        return self::$instance;
    }

    /**
     * Generates the initialization vector
     * @return string
     */
    private function getIv()
    {
        return openssl_random_pseudo_bytes(openssl_cipher_iv_length($this->method));
    }

    /**
     * @param string $data
     * @return string
     */
    public function encrypt($data)
    {
        $iv = $this->getIv();
        return base64_encode(openssl_encrypt($data, $this->method, $this->key, 0, $iv) . $this->separator . base64_encode($iv));
    }

    /**
     * @param string $dataAndVector
     * @return string
     */
    public function decrypt($dataAndVector)
    {
        $parts = explode($this->separator, base64_decode($dataAndVector));
        // $parts[0] = encrypted data
        // $parts[1] = initialization vector
        return openssl_decrypt($parts[0], $this->method, $this->key, 0, base64_decode($parts[1]));
    }

}
?>
