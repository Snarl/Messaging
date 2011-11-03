<?php 
/*    foreach ($GLOBALS as $name=>$val) { 
        echo "$name = " . var_dump($val) ."<BR/>";
    }
    echo "<HR/>";
*/  
    foreach ($_SERVER as $name=>$val) { 
        echo "$name = " . var_dump($val) ."<BR/>";
    } 
    echo "<HR/>";

    foreach ($_GET as $name=>$val) { 
        echo "$name = " . var_dump($val) ."<BR/>";
    } 
    echo "<HR/>";

    foreach ($_POST as $name=>$val) { 
        echo "$name = " . var_dump($val) ."<BR/>";
    } 
    echo "<HR/>";

    foreach ($_FILES as $name=>$val) { 
        echo "$name = " . var_dump($val) ."<BR/>";
    } 
    echo "<HR/>";
    
    foreach ($_REQUEST as $name=>$val) { 
        echo "$name = " . var_dump($val) ."<BR/>";
    } 
    echo "<HR/>";
    foreach ($_SESSION as $name=>$val) { 
        echo "$name = " . var_dump($val) ."<BR/>";
    } 
    echo "<HR/>";
    foreach ($_ENV as $name=>$val) { 
        echo "$name = " . var_dump($val) ."<BR/>";
    } 
    echo "<HR/>";
    foreach ($_COOKIE as $name=>$val) { 
        echo "$name = " . var_dump($val) ."<BR/>";
    } 
    echo "<HR/>";

?>
