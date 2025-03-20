<?php

include_once("userstorage.php");

session_start();

$user_session = $_SESSION["user"] ?? null;

$userSt = new UserStorage();

$email = null;
$password = null;
$error = [];

if(count($_POST) > 0 )
{

    if(isset($_POST["email"]))
    {
        if(trim(($_POST["email"])) !== "")
        {
            if(filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL) !== false)
            {
              $email = $_POST["email"];  
            }
            else
            {
                $error[] = "email is not valid";
            }
        }
        else
            {
                $error[] = "email cannot be empty";
            }
    }
    else
    {
        $error[] = "email is not set";
    }

    if(isset($_POST["password"]))
    {
        if(trim(($_POST["password"])) !== "")
        {
            $password = $_POST["password"];
        }
        else
        {
            $error[] = "password cannot be empty";
        }
    }
    else
    {
        $error[] = "password is not set";
    }
    
    if(count($error) === 0)
    {
        $user = array_values($userSt->getContactsByEmail($email))[0] ?? null;
        if($user)
        {
            if($password === $user["password"])
            {
                $_SESSION["user"] = $user["email"];
                $_SESSION["is_admin"] = $user["is_admin"];

                header("Location: user.php");
                exit;
            }
            else
            {
                $error[] = "incorrect password";
            }
        }
        else
        {
            $error[] = "user doesn't exist";
        }
    }

    
    
    
}

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #2c2c2c; /* Dark grey */
            color: #ffffff; /* White text */
            display: flex;
            flex-direction: column; /* Arrange header and main content in a column */
            align-items: center;
            min-height: 100vh; /* Ensure full viewport height */
        }

        header {
            background-color: #1f1f1f;
            color: #ffffff;
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            position: sticky;
            top: 0;
            height: 50px;
            margin-left: -40px;
        }
        

        header div {
            display: flex;
            align-items: center;
        }

        header a {
            color: #ffffff;
            text-decoration: none;
            margin-left: 15px;
            padding: 5px 10px;
            border: 1px solid #ffffff;
            border-radius: 4px;
            transition: background-color 0.2s, color 0.2s;
        }

        header a:hover {
            background-color: #ffffff;
            color: #1f1f1f;
        }

        .login-container {
            background-color: #3a3a3a; 
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.3); 
            text-align: center; 
            width: 100%;
            max-width: 400px; 
            margin: auto;
        }

        h1 {
            margin-bottom: 20px;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        input {
            padding: 10px;
            font-size: 16px;
            border: none;
            border-radius: 4px;
            width: 100%;
        }

        button {
            background-color: #ff9900; 
            color: #ffffff;
            padding: 10px;
            font-size: 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        button:hover {
            background-color: #cc7a00; 
        }

        .register-link {
            color: #ff9900;
            text-decoration: none;
            display: inline-block;
            margin-top: 10px;
            padding: 10px;
            border: 1px solid #ff9900;
            border-radius: 4px;
            transition: background-color 0.2s, color 0.2s;
        }

        .register-link:hover {
            background-color: #ff9900;
            color: #ffffff;
        }

        .homepage-link {
            color: #ffffff;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
            padding: 10px;
            background-color: #ff9900;
            border: 1px solid #ffffff;
            border-radius: 4px;
            transition: background-color 0.2s, color 0.2s;
        }

        .homepage-link:hover {
            background-color: #cc7a00;
            color: #ffffff;
        }
        .my-links
        {
            margin-right: 20px;
        }
        .my-text
        {
            margin-left:20px;
        }
        .my-text a
        {
            background-color: #ff9900;
        }
        .my-text a:hover{
            background-color: #1f1f1f;
            color: #1f1f1f; 
        }
    </style>
</head>
<body>
    <header>
    <div class="my-text"><a href="index.php" style="text-decoration: none; color: inherit;">IKarRental</a></div>
        <?php if($user_session): ?>
        <div>
            <a href="logout.php">Log Out</a>
            <a href="user.php">Profile</a>
        </div>
        <?php else: ?>
            <div>
            <a href="login.php">Log In</a>
            <a href="signup.php">Registration</a>
        </div>
        <?php endif ?>
    </header>
    <div class="login-container">
        <h1>Log In</h1>
        <form action="" method="post">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Log In</button>
            <?php if(count($error) !== 0) : ?>
            <p><?= implode(" and ", $error) ?></p>
            <?php endif ?>
        </form>
        <a class="register-link" href="signup.php">Don't have an account? Register</a>
        <a class="homepage-link" href="index.php">Go to Homepage</a>
    </div>
</body>
</html>
