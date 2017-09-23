<?php


final class Connection {
    private function __construct(){}
    
    public static function open($name){
        
        if(file_exists(__DIR__."/config/{$name}.ini")){
            $db = parse_ini_file(__DIR__."/config/{$name}.ini");
        }
        else {
            echo __DIR__;
            throw new Exception("Arquivo {$name}, não foi encontrado.");
        }
        
        $user = isset($db['user']) ? $db['user'] : null;
        $pass = isset($db['pass']) ? $db['pass'] : null;
        $dbname = isset($db['name']) ? $db['name'] : null;
        $host = isset($db['host']) ? $db['host'] : null;
        $type = isset($db['type']) ? $db['type'] : null;
        $port = isset($db['port']) ? $db['port'] : null;
        
        switch($type){
            case 'pgsql':
                $port = $port ? $port : '5432';
                $conn = new PDO("pgsql:dbname={$dbname}; user={$user}; password={$pass}; host={$host}; port={$port}");
                break;
            case 'mysql':
                $port = $port ? $port : '3306';
                //$conn = new PDO("mysql:host={$host}; port={$port}; dbname={$dbname}", $user, $pass);
                $conn = new PDO("mysql:host={$host}; dbname={$dbname}", $user, $pass);
                break;
            case 'sqlite':
                $conn = new PDO("sqlite:{$dbname}");
                break;
            case 'ibase':
                $conn = new PDO("firebird:dbname={$dbname}", $user, $pass);
                break;
            case 'oci8':
                $conn = new PDO("oci:dbname={$dbname}", $user, $pass);
                break;
            case 'mssql':
                $conn = new PDO("mssql:host={$host}, 1433; dbname={$dbname}", $user, $pass);
                break;
        }
        
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    }
}
