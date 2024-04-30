<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once "config.php";
require_once "session.php";

$error = '';

if($_SERVER["REQUEST_METHOD"] == "POST" && isset ($_POST['submit'])) {

    $email = trim($_POST ['email']);
    $password = trim($_POST ['password']);

    if (empty($email)) {
        $error .= '<p class = "error"> Bitte Email eingeben.</p>';
    }

    if (empty($password)) {
        $error .= '<p class = "error"> Bitte Passwort eingeben.</p>';
    }

    if (empty($error)) {
        if ($query = $db->prepare("SELECT * FROM users WHERE email = ?")) {
            $query->bind_param('s', $email);
            $query->execute();
            $result = $query->get_result();
            $row = $result->fetch_assoc();
            if ($row) {
                if(password_verify($password, $row['password'])) {
                    $_SESSION["userid"] = $row['id'];
                    $_SESSION["user"] = $row; 
                    header('Location: eingabe.php');
                    exit; 
                } else {
                    $error .= '<p class = "error"> Passwort ungültig. </p>'; 
                }
            } else {
                $error .= '<p class = "error"> Kein Nutzer mit dieser E-Mail gefunden. </p>'; 
            }
        }
        $query->close();
    }
    //Verbindung schließen
    mysqli_close($db);
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
            <li class="li"><a href="index.php">Startseite</a></li>
            <li class="li"><a href ="login.php">Login</a></li>
    
        </ul>
    </nav>


    <div class="container">
        <h2>Login</h2>
        <p>Bitte geben Sie Ihre E-Mail und Ihr Passwort ein</p>
        <?php echo $error; ?>
        <form action = "" method = post>
            <label >E-Mail</label>
            <input type="email" name = "email" class = "form-control" required>
            <label>Passwort</label>
            <input type="password" name ="password" class = "form-control" required>
          
    
            <input type="submit" name ="submit" class = "btn btn-primary" value = "Submit">
            <p>Haben Sie kein Konto? <a href="register.php" style="color: #00c;">Hier registrieren</a></p>
        </form>
    
    </div>
        <p id="fehlermeldung"></p>
    </div>
</body>
</html>