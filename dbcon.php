<?php

	$DbHost = "localhost";
	$DbUser = "root";
	$DbPass = "";
	$DbName = "twapp";

    try {
        $db = new PDO("mysql:host=$DbHost;dbname=$DbName",$DbUser,$DbPass);
    }
    catch(PDOException $e){
        die ("DataBase Connection failed: ".$e->getMessage());
    }

