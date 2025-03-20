<?php

include_once("bookingstorage.php");
include_once("userstorage.php");
include_once("carstorage.php");

session_start();

$user_session = $_SESSION["user"] ?? null;

if(!$user_session)
{
    header("Location: index.php");
    exit;
}

$bookingSt = new BookingStorage();
$userSt = new UserStorage();
$newCarStorage = new CarsStorage();


$userData = array_values($userSt->getContactsByEmail($user_session))[0] ?? null;

$userBooking = $bookingSt->getContactsByEmail($user_session);


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["booking-id"])) {
    $bookingIdToCancel = $_POST["booking-id"];

    $allBookings = $bookingSt->findAll();

    if (isset($allBookings[$bookingIdToCancel])) {
        $bookingSt->delete($bookingIdToCancel);
        
        header("Location: user.php");
        exit();
    }
}

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <style>
    body {
        margin: 0;
        font-family: Arial, sans-serif;
        background-color: #2c2c2c;
        color: #ffffff;
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
        max-width: 1400px
    }

    header p {
        margin-left: 15px;
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

    main {
        padding: 20px;
        max-width: 1400px;
        margin: 0 auto;
    }

    section {
        background-color: #3a3a3a;
        padding: 20px;
        margin-bottom: 20px;
        border-radius: 8px;
    }

    .user-profile {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .user-profile img {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
    }

    .user-info {
        display: flex;
        flex-direction: column;
    }

    .user-info h2, .user-info p {
        margin: 5px 0;
    }

    .booking:hover {
        transform: translateY(-8px); 
        box-shadow: 0 10px 15px rgba(0, 0, 0, 0.4);
    }

    .booking img {
        max-width: 100%;
        border-radius: 8px;
        margin-bottom: 15px;
    }

    button {
        padding: 10px;
        font-size: 16px;
        border: none;
        border-radius: 4px;
        background-color: #ff9900;
        color: #ffffff;
        cursor: pointer;
        margin-top: 10px;
        align-self: flex-end;
    }

    button:hover {
        background-color: #cc7a00;
    }

    form input[type="text"] {
        display: none;
    }
    .my-text a
    {
        background-color: #ff9900;
    }
    .my-text a:hover{
        background-color: #1f1f1f;
        
    }

    .booking-list {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    justify-content: center; 
    padding: 20px;
}

.booking {
    background-color: #3a3a3a;
    padding: 20px;
    border-radius: 12px; 
    text-align: center;
    width: 220px; 
    box-shadow: 0 6px 10px rgba(0, 0, 0, 0.3); 
    transition: transform 0.2s, box-shadow 0.2s;
}

.booking:hover {
    transform: translateY(-8px);
    box-shadow: 0 10px 15px rgba(0, 0, 0, 0.4);
}

.booking img {
    max-width: 100%;
    border-radius: 8px;
    margin-bottom: 15px;
}
</style>
</head>
<body>
    <header>
    <div class="my-text"><a href="index.php" style="text-decoration: none; color: inherit;">IKarRental</a></div>
        <?php if($user_session): ?>
        <div>
            <a href="logout.php">Log Out</a>
            <?php if($userData["is_admin"] === true): ?>
            <a href="admin.php">Admin Panel</a>
            <?php endif ?>
        </div>
        <?php else: ?>
            <div>
            <a href="login.php">Log In</a>
            <a href="signup.php">Registration</a>
        </div>
        <?php endif ?>
    </header>
    <main>
        <h1>User Profile</h1>

        <section class="user-profile">
            <img src="icon.jpg" alt="User Picture">
            <div class="user-info">
                <h2>Name: <?= $userData["name"] ?></h2>
                <p>Email: <?= $userData["email"] ?></p>
                <p>
                    <?php if($userData["is_admin"] === false): ?>
                        User
                    <?php  else :?>
                        Admin
                        <?php endif ?>
                </p>
            </div>
        </section>

        <section>
            <h2>Your Bookings</h2>
            <div class="booking-list">
            <?php foreach($userBooking as $booking): 
                $car = $newCarStorage->findById($booking["car_id"])
                ?>
                <div class="booking">
                    <a href="car.php?id=<?=$car["id"]?>"> <img src="<?= $car["image"] ?>" alt="Car Image"> </a>
                    <h2><?= $car["brand"]." ".$car["model"] ?></h2>
                    <p>Daily Price: <?= $car["daily_price_huf"] ?></p>
                    <p>Seat Number: <?= $car["passengers"] ?></p>
                    <p>Type: <?= $car["transmission"] . "/". $car["fuel_type"]?></p>
                    <p>Start Date: <?= $booking["from-date"] ?></p>
                    <p>End Date: <?= $booking["until-date"]?></p>
                    <form action="" method="post">
                    <input type="hidden" name="booking-id" value="<?= $booking["id"] ?>">
                        <button type="submit">Cancel Booking</button>
                    </form>
                </div>
                <?php endforeach ?>
            </div>
        </section>
    </main>
</body>
</html>
