<?php
    $host = "localhost";
    $user = "athtitph";
    $pass = "Sunny1234";
    $dbname = "dbshop";
try{
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8",$user,$pass);
    $conn->setAttribute(PDO ::ATTR_ERRMODE,PDO ::ERRMODE_EXCEPTION);
}catch(PDOException $e){
    echo "Connection Failed : ".$e->getMessage();
}
?>