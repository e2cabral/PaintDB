<?php
require_once 'connection/transaction/Transaction.php';
require_once 'connection/Connection.php';

class ModelDB {
    
    protected $data;

    public function __construct($id = null){
        if($id){
            $object = $this->load($id);
            
            if($object){
                $this->fromArray($object->toArray());
            }
        }
    }
    
    public function __clone(){
        unset($this->data['id']);
    }
    
    public function __set($attr, $value){
        
        $attr[0] = strtoupper($attr[0]); //Transforma a primeira letra da propriedade em maiúscula
        
        if(method_exists($this, 'set'.$attr)){
            call_user_func(array($this, 'set'.$attr), $value);
        }
        else {
            //$attr = strtolower($attr);
            
            if($value === null){
                unset($this->data[$attr]);
            }
            else {
                $this->data[$attr] = $value;
            }
        }
    }
    
    public function __get($attr){
        
        $attr[0] = strtoupper($attr[0]); //Transforma a primeira letra da propriedade em maiúscula
        
        if(method_exists($this, 'get'.$attr)){
            return call_user_func(array($this, 'get'.$attr));
        }
        else {
            $attr = strtolower($attr);
            
            if(isset($this->data[$attr])){
                return $this->data[$attr];
            }
        }
    }
    
    public function __isset($attr) {
        return isset($this->data[$attr]);
    }
    
    private function getEntity(){
        $class = get_class($this);
        return constant("{$class}::TABLENAME");
    }
    
    public function fromArray($data){
        $this->data = $data;
    }
    
    public function toArray(){
        return $this->data;
    }
    
    public function store(){
        $prepared = $this->prepare($this->data);
        
        if(empty($this->data['id']) or (!$this->load($this->id))){
            
            if(empty($this->data['id'])){
                $this->id = $this->getLast();
                $prepared['id'] = $this->id;
            }
            if(empty($prepared['id'])){
                $prepared['id'] = 0;
                $sql = "INSERT INTO {$this->getEntity()} (".implode(', ', array_keys($prepared)).") VALUES (".implode(', ', array_values($prepared)).")";
            }
            else {
                $sql = "INSERT INTO {$this->getEntity()} (".implode(', ', array_keys($prepared)).") VALUES (".implode(', ', array_values($prepared)).")";
            }
        }
        else {
            $sql = "UPDATE :table";
            if($prepared){
                foreach($prepared as $columm => $value){
                    if($columm !== 'id'){
                        $set[] = "{$columm} = :{$value}";
                    }
                }
            }
            $sql .= ' SET '.implode(', ', $set);
            $sql .= " WHERE id = (int)$this->data['id']";
        }
        
        if($conn = Transaction::get()){
            
            $query = $conn->prepare($sql);
            var_dump($sql);
            $query->execute();
            
            return $query->rowCount();
        }
        else {
            throw new Exception('Não há transação ativa!');
        }   
    }
    
    public function load($id){
        $sql = "SELECT * FROM {$this->getEntity()}";
        $sql .= ' WHERE id = :id';
        
        if($conn = Transaction::get()){
            
            $query = $conn->prepare($sql);
            $query->bindValue(':id', $id);
            $query->execute();
            
            return $query->fetchAll(PDO::FETCH_OBJ);
        }
        else {
            throw new Exception('Não há transação ativa!');
        }
    }
    
    public function delete($id = null){
        
        $id = $id ? $id : $this->id;
        
        $sql = "DELETE FROM {$this->getEntity()}";
        $sql .= ' WHERE id = :id';
        
        if($conn = Transaction::get()){
            $query = $conn->prepare($sql);
            $query->bindValue(':id', $id);
            
            $query->execute();
            
            return $query->rowCount();
        }
        else {
            throw new Exception('Não há transação ativa!');
        }
    }
    
    public static function find($id){
        $classname = get_called_class();
        $ar = new $classname;
        return $ar->load($id);
    }
    
    public function getLast(){
        if($conn = Transaction::get()){
            $sql = "SELECT max(id) as id FROM {$this->getEntity()}";
            
            $query = $conn->prepare($sql);
            $query->execute();
            
            $row = $query->fetch();
            
            return $row[0];
        }
        else {
            throw new Exception('Não há transação ativa!');
        }
    }
    
    public function prepare($data){
        $prepared = array();
        foreach($data as $key => $value){
            if(is_scalar($value)){
                $prepared[$key] = $this->escape($value);
            }
        }
        return $prepared;
    }
    
    public function escape($value){
        if(is_string($value)){
            $value = addslashes($value);
            
            return "'$value'";
        }
        else if(is_bool($value)){
            return $value ? 'TRUE' : 'FALSE';
        }
        else if($value !== ''){
            return $value;
        }
        else {
            return "NULL";
        }
    }
    
}