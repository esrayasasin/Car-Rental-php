<?php

include_once("carstorage.php");

session_start();

$user_session = $_SESSION["user"] ?? null;
$CarStorage = new CarsStorage();

if(count($_GET) > 0)
{
    $carId = $_GET["car"] ?? null;
    $startDate = $_GET["startDate"] ?? null;
    $endDate = $_GET["endDate"] ?? null;
    $car = $CarStorage->findById($carId);

    $date1 = new DateTime($startDate);
    $date2 = new DateTime($endDate);
    $difference = ($date1->diff($date2))->days;
    $price = (int)$difference * (int)($car["daily_price_huf"]);
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Successful</title>
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

        .success-container {
            background-color: #3a3a3a;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.3); 
            text-align: center; 
            width: 100%;
            max-width: 400px; 
            margin: auto; 
            margin-top: 50px;
        }

        h1 {
            margin: 20px 0;
        }

        .success-icon {
            font-size: 80px;
            color: #00ff00;
            margin-bottom: 20px;
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

        .my-links {
            margin-right: 20px;
        }

        .my-text {
            margin-left: 20px;
        }

        .my-text a {
            background-color: #ff9900;
        }

        .my-text a:hover {
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
    <div class="success-container">
        <div class="success-icon">&#10003;</div>
        <h1>Booking Successful</h1>
        <p><?= $car["brand"]." ".$car["model"]?> succesfully booked between <?= $startDate ?> and <?= $endDate ?>.</p>
        Total price is: <?= $price ?> HUF
        <p>You can track the details from your profile page.</p>
        <!-- This part wasn't in the requirements so I didn't want to write additional css for it so I simply just used JS -->
        <button onclick="window.location.href='index.php'">Go to Homepage</button>
    </div>
</body>
</html>
