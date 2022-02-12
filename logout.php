<?php 
session_start();

session_unset();
session_destroy();

if(isset($_COOKIE['user_id'])):
    setcookie('user_id', '', time()-7000000, '/');
endif;
header("Location: login.php");