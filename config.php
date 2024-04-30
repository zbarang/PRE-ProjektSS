<?php
define ('DBSERVER', 'localhost');
define ('DBUSERNAME', 'root');
define ('DBPASSWORD' , '');
define ('DBNAME', 'benutzerverwaltung');

$db = mysqli_connect(DBSERVER, DBUSERNAME, DBPASSWORD, DBNAME);


if ($db === false){
    die("error: connection error. " . mysqli_connect_error());
}?>
