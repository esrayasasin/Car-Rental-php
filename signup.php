<?php

include_once("userstorage.php");

session_start();

$user_session = $_SESSION["user"] ?? null;

$userSt = new UserStorage();
$name = null;
$email = null;
$password = null;
$confirmPassword = null;
$error = [];

if(count($_POST) > 0 )
{
    if(isset($_POST["name"]))
    {
        if(trim(($_POST["name"])) !== "")
        {
            $name = $_POST["name"];
        }
        else
        {
            $error[] = "Name cannot be empty";
        }
    }
    else
    {
        $error[] = "Name is not set";
    }

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
                $error[] = "Email is not valid";
            }
        }
        else
        {
            $error[] = "Email cannot be empty";
        }
    }
    else
    {
        $error[] = "Email is not set";
    }

    if(isset($_POST["password"]))
    {
        if(trim(($_POST["password"])) !== "")
        {
            $password = $_POST["password"];
        }
        else
        {
            $error[] = "Password cannot be empty";
        }
    }
    else
    {
        $error[] = "Password is not set";
    }

    if(isset($_POST["confirm-password"]))
    {
        if(trim(($_POST["confirm-password"])) !== "")
        {
            $confirmPassword = $_POST["confirm-password"];
        }
        else
        {
            $error[] = "Confirm password cannot be empty";
        }
    }
    else
    {
        $error[] = "Confirm password is not set";
    }
    
    if($password !== $confirmPassword)
    {
        $error[] = "Your passwords are not matching";
    }

    $allUsers = $userSt->findAll();
    $emails = array_map(function($user)
    {
        return $user["email"];
        
    },$allUsers);

    if(in_array($email, $emails))
    {
        $error[] = "An account with this email is already exist, try to log in";
    }
    if(count($error) === 0)
    {
        $newuser = 
        [
            "name" => $name,
            "email" => $email,
            "password" => $password,
            "is_admin" => false
        ];
        $userSt->add($newuser);
        
        $_SESSION["user"] = $newuser["email"];
        $_SESSION["is_admin"] = $newuser["is_admin"];

        header("Location: user.php");
        exit;


    }
    
}


?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #2c2c2c;
            color: #ffffff; 
            display: flex;
            flex-direction: column; 
            align-items: center;
            min-height: 100vh; 
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
        header p
        {
            margin-left: 20px;
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

        .signup-container {
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

        .login-link {
            color: #ff9900;
            text-decoration: none;
            display: inline-block;
            margin-top: 10px;
            padding: 10px;
            border: 1px solid #ff9900;
            border-radius: 4px;
            transition: background-color 0.2s, color 0.2s;
        }

        .login-link:hover {
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
    <div class="signup-container">
        <h1>Sign Up</h1>
        <form action="" method="post" novalidate>
            <input type="text" name="name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="confirm-password" placeholder="Confirm Password" required>
            <button type="submit">Sign Up</button>
            <?php if(count($error) !== 0) : ?>
            <p><?= implode(" & ", $error) ?></p>
            <?php endif ?>
        </form>
        <a class="login-link" href="login.php">Already have an account? Log In</a>
        <a class="homepage-link" href="index.php">Go to Homepage</a>
    </div>
</body>
</html>
