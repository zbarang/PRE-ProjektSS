<?php 

require_once "config.php";
require_once "session.php";

$error = '';


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {

    $fullname = trim($_POST ['name']);
    $email = trim($_POST ['email']);
    $password = trim($_POST ['password']);
    $confirm_password = trim($_POST ['confirm_password']);
    $password_hash = password_hash($password, PASSWORD_BCRYPT);

        if ($query = $db->prepare("SELECT * FROM users WHERE email = ?")){
                $error = '';

            $query->bind_param('s', $email);
            $query->execute();
            $query->store_result();
            if ($query->num_rows() > 0) {
                $error .= '<p class = "error"> E-mail ist schon registriert </p>';
            }
            else {
                if(strlen($password) < 6) {
                    $error .= '<p class = error"> Passwort muss mind 6 Zeichen haben </p>';
                }
                if (empty($confirm_password)){
                    $error .= '<p class = "error"> Bitte Passwort bestätigen </p>';
                } else {
                    if (empty($error) && ($password != $confirm_password)){
                        $error .= '<p class = "error"> Passwörter stimmen nicht überein </p>';
                    }
                }

                if (empty($error) ) {
                    $insertQuery = $db->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");

                    if (!$insertQuery) {
                        die('Vorbereitung der Anweisung fehlgeschlagen: ' . $db->error);
                    }

                    $insertQuery->bind_param("sss", $fullname, $email, $password_hash);
                    $result = $insertQuery->execute();

                    if ($result) {
                        $error .= '<p class="success"> Registrierung war erfolgreich </p>';
                    } else {
                        $error .= '<p class="error"> Etwas ist schiefgelaufen beim Einfügen in die Datenbank: ' . $insertQuery->error . '</p>';
                    }

                    $insertQuery->close();  // Nur close() aufrufen, wenn $insertQuery erfolgreich erstellt wurde
                } else {
                    $error .= '<p class="error"> Vorbereitung der Anweisung fehlgeschlagen: ' . $db->error . '</p>';
                }    
            }
        }

        $query->close();    
        mysqli_close($db);
        //print_r($_POST);
}


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css"> </link>
    
</head>
<body>

    <nav class="nav"> 
        <ul>
            <li class="li"><a href="index.html">Startseite</a></li>
            <li class="li"><a href ="login.php">Login</a></li>
    
        </ul>
    </nav>


    <div class="container">
        <h2>Registrieren</h2>
        
        <form action = "" method = post>
            <label >Benutzername</label>
            <input type="text" name = "name" class = "form-control" required>
            <label>E-Mail</label>
            <input type="email" name = "email" class = "form-control" required>
            <label>Passwort</label>
            <input type="password" name ="password" class = "form-control" required>
            <label>Passwort bestätigen</label>
            <input type="password" name ="confirm_password" class = "form-control" required>
            
    
            <input type="submit" name ="submit" class = "btn btn-primary" value = "Submit">
        </form>
    
    </div>
</body>
</html>
