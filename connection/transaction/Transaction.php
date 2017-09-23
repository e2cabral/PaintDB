<?php
require_once './connection/Connection.php';

class Transaction {
    private static $conn;
    
    private function __construct() {}
    
    public static function open($database){
        if(empty(self::$conn)){
            self::$conn = Connection::open($database);
            self::$conn->beginTransaction();
        }
    }
    
    public static function get(){
        return self::$conn;
    }
    
    public static function rollback(){
        if(self::$conn){
            self::$conn->rollback();
            self::$conn = null;
        }
    }
    
    public static function close(){
        if(self::$conn){
            self::$conn->commit();
            self::$conn = null;
        }
    }
}
