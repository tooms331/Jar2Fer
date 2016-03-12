<?php

define('DEBUG', 
    $_SERVER['REMOTE_ADDR']==='::1' || 
    $_SERVER['REMOTE_ADDR']==='127.0.0.1'
);

require_once 'config.secure.php';