<?php

define('BDD_SERVER','127.0.0.1');
define('BDD_SCHEMA','mlauhot119020fr26694_djalf2');
define('BDD_USER','mlauh_admin'); 
define('BDD_PASSWORD','Metropolis33');

echo "Hello World!";

class Contact{
    public $nom;
    public $age;
    public $tel;
    
    public function Appeler(){
        $reussi = call($this->tel);
        
        return $reussi
    }
}

?>