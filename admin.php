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

$x = $_SESSION["is_admin"] ?? null;

if(!$x)
{
    header("Location: index.php");
    exit;
}

$bookingSt = new BookingStorage();
$userSt = new UserStorage();
$newCarStorage = new CarsStorage();
$error = [];

$userData = array_values($userSt->getContactsByEmail($user_session))[0] ?? null;
if (!$userData || !$userData["is_admin"]) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["booking-id"])) {
    $bookingSt->delete($_POST["booking-id"]);
    header("Location: admin.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["car-id"])) {
    $newCarStorage->delete($_POST["car-id"]);
    header("Location: admin.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["new-car"])) {

    if(trim($_POST["brand"]) === "")
    {
        $error[] = "Brand name cannot be empty";
    }
    if(trim($_POST["model"]) === "")
    {
        $error[] = "Model name cannot be empty";
    }
    if(trim($_POST["fuel"]) === "")
    {
        $error[] = "Model name cannot be empty";
    }

    if((trim($_POST["year"]) === "") || (filter_var($_POST["year"],FILTER_VALIDATE_INT) === false ))
    {
        $error[] = "Model year is not valid";
    }
    if((trim($_POST["daily_price_huf"]) === "") || (filter_var($_POST["daily_price_huf"],FILTER_VALIDATE_INT) === false ))
    {
        $error[] = "Price is not valid";
    }
    if((trim($_POST["passengers"]) === "") || (filter_var($_POST["passengers"],FILTER_VALIDATE_INT) === false ))
    {
        $error[] = "Passenger seat is not valid";
    }

    if(empty($error))
    {
        $newCar = [
               "brand" => $_POST["brand"],
               "model" => $_POST["model"],
               "year" => $_POST["year"],
               "transmission"=> $_POST["gear"],
               "fuel_type" => $_POST["fuel"],
               "daily_price_huf" => $_POST["daily_price_huf"],
               "passengers" => $_POST["passengers"],
               "image"=> $_POST["image"],
           ];
           $newCarStorage->add($newCar);
           header("Location: admin.php");
           exit();
       
    }
}

$allBookings = $bookingSt->findAll();
$allCars = $newCarStorage->findAll();


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
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
        header p
        {
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

        .booking-list, .car-list {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }

        .card {
            background-color: #3a3a3a;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            width: 220px;
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.3);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.4);
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
        }

        button:hover {
            background-color: #cc7a00;
        }

        form input[type="text"], form input[type="number"] {
            width: 100%;
            max-width: 400px;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #555555;
            border-radius: 4px;
            background-color: #2c2c2c;
            color: #ffffff;
        }

        form input[type="text"]:focus,
        form input[type="number"]:focus {
            outline: none;
            border-color: #ff9900;
        }

        section form {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 10px;
        }

        section form button {
            align-self: flex-start;
        }
        .my-text a
    {
        background-color: #ff9900;
    }
    .my-text a:hover{
        background-color: #1f1f1f;
        color: white;
    }
    .booking-list button
    {
        margin-left: 44px;
    }
    .car-list button
    {
        margin-left: 60px;
    }
    </style>
</head>
<body>
    <header>
        <div class="my-text"><a href="index.php">IKarRental</a></div>
        <div>
            <a href="logout.php">Log Out</a>
            <a href="user.php">Profile</a>
        </div>
    </header>
    <main>
        <h1>Admin Panel</h1>

        <section>
            <h2>All Bookings</h2>
            <div class="booking-list">
                <?php foreach ($allBookings as $booking): 
                    $car = $newCarStorage->findById($booking["car_id"]); ?>
                    <div class="card">
                        <img src="<?= $car["image"] ?>" alt="Car Image" style="width:100%; border-radius:8px; margin-bottom:10px;">
                        <h3><?= $car["brand"] . " " . $car["model"] ?></h3>
                        <p>User: <?= $booking["email"] ?></p>
                        <p>Start Date: <?= $booking["from-date"] ?></p>
                        <p>End Date: <?= $booking["until-date"] ?></p>
                        <form method="post">
                            <input type="hidden" name="booking-id" value="<?= $booking["id"] ?>">
                            <button type="submit">Delete Booking</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section>
    <h2>All Cars</h2>
    <div class="car-list">
        <?php foreach ($allCars as $car): ?>
            <div class="card">
                <a href="car.php?id=<?= $car["id"] ?>">
                    <img src="<?= $car["image"] ?>" alt="Car Image" style="width:100%; border-radius:8px; margin-bottom:10px;">
                </a>
                <h3><?= $car["brand"] . " " . $car["model"] ?></h3>
                <p>Price: <?= $car["daily_price_huf"] ?> HUF</p>
                <p>Seats: <?= $car["passengers"] ?></p>
                <form method="post">
                    <input type="hidden" name="car-id" value="<?= $car["id"] ?>">
                    <button type="submit">Delete Car</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
    </section>


    <section>
    <h2>Add a New Car</h2>
    <form method="post" novalidate>
        <input type="hidden" name="new-car" value="1">
        <input type="text" name="brand" placeholder="Brand" required>
        <input type="text" name="model" placeholder="Model" required>
        <input type="text" name="fuel" placeholder="Fuel Type" required>
        <input type="number" name="year" placeholder="Year" required>
        <select 
            style="background-color:#3a3a3a; color:white; font-size:0.9rem; padding:5px; width:150px;" 
            id="gear" 
            name="gear"
        >
            <option value="Automatic">Automatic</option>
            <option value="Manual">Manual</option>
        </select>
        <input type="number" name="daily_price_huf" placeholder="Daily Price (HUF)" required>
        <input type="number" name="passengers" placeholder="Number of Passengers" required>
        <input type="text" name="image" placeholder="Image url" required>
        <button type="submit">Add Car</button>
    </form>
</section>

    </main>
</body>
</html>
