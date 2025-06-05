<?php

namespace Src\Config;

use PDO;

class Conexao {
    private static $instance;

    public static function getConn(){
        if(!isset(self::$instance)){
            self::$instance = new \PDO ('mysql:host=localhost;dbname=transacoes_api','root','');
        }
        return self::$instance;
    }
}