<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <?php
        require_once 'ModelDB.php';
        require_once 'Teste.php';
        require_once 'connection/transaction/Transaction.php';
        
        
//        $array = array('id' => 1, 2, 3, 4, 5, 6);
//        $key = array_keys($array);
//        $key[0] = ':'.$key[0];
//        echo $key[0];
//        
        
//        try {
//            
//            Transaction::open('model');
//            
//            $teste = new Teste;
//            $teste->nome = "Edson";
//            $teste->idade = 24;
//            $teste->altura = 1.74;
//            
//            $teste->store();
//            
//            Transaction::close();
//            
//        } catch (Exception $ex) {
//            Transaction::rollback();
//            print $ex->getMessage().'<br>';
//            
//            print $ex->getCode().'<br>';
//            print $ex->getFile().'<br>';
//            print $ex->getLine().'<br>';
//            print $ex->getPrevious().'<br>';
//            print $ex->getTrace().'<br>';
//            print $ex->getTraceAsString().'<br>';
//        }
        
        try {
            Transaction::open('model');
            
            $teste = new Teste;
            $t = $teste->delete(1);
            
            //echo $t[0]->nome;
            
            
        } catch (Exception $ex) {

        }
        
        ?>
    </body>
</html>
