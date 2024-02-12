<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include_once('db_connection.php');

    $email = $_POST['email'];
    $password = $_POST['password'];
    $hashedPassword = md5($password);
    $query = "SELECT user_id, username FROM user WHERE email = '$email' AND password = '$hashedPassword'";
    $result = mysqli_query($connection, $query);

    if ($result && mysqli_num_rows($result) > 0) {
       
        $userData = mysqli_fetch_assoc($result);
         $_SESSION['user_id'] = $userData['user_id'];
        $_SESSION['username'] = $userData['username'];

        header("Location: upload.php");
        exit();
    } else {
        $error = "Invalid email or password. Please try again.";
    }

    mysqli_close($connection);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
         * {box-sizing: border-box}
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            padding: 16px;
            width: 300px; 
        }
        input[type=text], input[type=password] {
            width: 100%;
            padding: 15px;
            margin: 5px 0 22px 0;
            display: inline-block;
            border: none;
            background: #f1f1f1;
        }
        input[type=text]:focus, input[type=password]:focus {
            background-color: #ddd;
            outline: none;
        }
        hr {
            border: 1px solid #f1f1f1;
            margin-bottom: 25px;
        }
        .registerbtn {
            background-color: #04AA6D;
            color: white;
            padding: 16px 20px;
            margin: 8px 0;
            border: none;
            cursor: pointer;
            width: 100%;
            opacity: 0.9;
        }
        .registerbtn:hover {
            opacity:1;
        }
        a {
            color: dodgerblue;
        }
        .signin {
            background-color: #f1f1f1;
            text-align: center;
        }
    </style>
</head>
<body>
    <form action="" method="POST">
        <div class="container">
            <h2>Login</h2>
            <hr>
            <?php
            if (isset($error)) {
                echo '<div class="alert alert-danger" role="alert">' . $error . '</div>';
            }
            ?>
            <label for="email"><b>Email</b></label>
            <input type="text" placeholder="Enter Email" name="email" id="email">
            <label for="psw"><b>Password</b></label>
            <input type="password" placeholder="Enter Password" name="password" id="password">
            <hr>
            <button type="submit" class="registerbtn">Login</button>
        </div>
    </form>
</body>
</html>
