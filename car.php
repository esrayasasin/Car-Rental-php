<?php
include_once("carstorage.php");
include_once("bookingstorage.php");

session_start();
$bookingSt = new BookingStorage();

$user_session = $_SESSION["user"] ?? null;
$car = null;

$car_id = $_GET['id'] ?? null; 
$cs = new CarsStorage();

$error = [];

if (!$car_id)
{
    $error[] = "NO CAR FOUND";
}

if(count($error) === 0)
{
    $car = $cs->findById($car_id);

    if(!$car)
    {
        $error[] = "NO CAR FOUND";
    }
}

if(count($_POST) > 0)
    {

    $carId = isset($_POST['car_id']) && (trim($_POST['car_id'])) ? trim($_POST['car_id']) : null;
    $startDate = isset($_POST['from-date']) && strtotime(trim($_POST['from-date'])) ? trim($_POST['from-date']) : null;
    $endDate = isset($_POST['until-date']) && strtotime(trim($_POST['until-date'])) ? trim($_POST['until-date']) : null;

        $currentDate = strtotime(date('Y-m-d'));

    if (!$startDate || !$endDate || strtotime($startDate) < $currentDate || strtotime($endDate) < strtotime($startDate)) {

        $error[] = "Invalid date interval" . " " . $startDate . " " . $endDate ;
    }

    if (!$carId) {
        $error[] = "Car ID is not set";
    }

    if (!$user_session) {
        $error[] = "User is not signed in";
    }

  
    $car = $cs->findById($carId);

    if (!$car) {
        $error[] = "Invalid car ID";
    }

    if(empty($error))
    {
    $newBooking = 
    [
        "id" => 0,
        "from-date" => $startDate,
        "until-date" => $endDate,
        "email" => $user_session,
        "car_id" => $carId
    ];

    $bookingSt->add($newBooking);
       
    header("Location: success.php?car=$carId&startDate=$startDate&endDate=$endDate ");
    exit;
    }
    else
    {
        $fail = implode(",", $error);
        header("Location: fail.php?error=$fail");
        exit;
    }
    }


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Details</title>
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
            
        }

        section {
            background-color: #3a3a3a;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
        }

        form {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }

        form div {
            display: flex;
            flex-direction: column;
        }

        input, select, button {
            padding: 10px;
            font-size: 16px;
            border: none;
            border-radius: 4px;
        }

        button {
            background-color: #ff9900; 
            color: #ffffff;
            cursor: pointer;
        }

        button:hover {
            background-color: #cc7a00; 
        }

    .car {
    background-color: #3a3a3a;
    padding: 20px;
    border-radius: 12px; 
    text-align: center;
    width: 220px; 
    box-shadow: 0 6px 10px rgba(0, 0, 0, 0.3);
    transition: transform 0.2s, box-shadow 0.2s;
    }

    .car:hover {
    transform: translateY(-8px);
    box-shadow: 0 10px 15px rgba(0, 0, 0, 0.4); 
    }

    .car img {
    max-width: 100%;
    border-radius: 8px;
    margin-bottom: 15px; 
    }
    header, main {
    max-width: 1400px; 
    margin: 0 auto;
    }

    .my-text a
    {
        background-color: #ff9900;
    }
    .my-text a:hover{
        background-color: #1f1f1f;
        
    }
    form {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    align-items: center; 
    }

    form div {
    display: flex;
    flex-direction: column;
    justify-content: center;
    }

    button {
    align-self: flex-end;
    padding: 10px;
    font-size: 16px;
    border: none;
    border-radius: 4px;
    background-color: #ff9900;
    color: #ffffff;
    cursor: pointer;
    margin-top: 17px;
    }

        .car-details {
            display: flex;
            align-items: flex-start;
            gap: 20px;
            background-color: #3a3a3a;
            padding: 20px;
            border-radius: 8px;
        }

        .car-image img {
            max-width: 400px;
            border-radius: 8px;
        }

        .car-info {
            display: flex;
            flex-direction: column;
            gap: 10px;
            color: #fff;
        }

        .booking-form {
            margin-top: 20px;
            height: 300px;
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
    <main>
        <h1>Car Details</h1>
        <?php if($car) : ?>
        <section class="car-details">
            <div class="car-image">
                <img src="<?= $car['image'] ?>" alt="<?= $car['brand'] . ' ' . $car['model'] ?>">
            </div>
            <div class="car-info">
                <h2><?= $car['brand'] . ' ' . $car['model'] ?></h2>
                <p><strong>Year:</strong> <?= $car['year'] ?></p>
                <p><strong>Transmission:</strong> <?= $car['transmission'] ?></p>
                <p><strong>Fuel Type:</strong> <?= $car['fuel_type'] ?></p>
                <p><strong>Seats:</strong> <?= $car['passengers'] ?></p>
                <p><strong>Daily Price:</strong> <?= $car['daily_price_huf'] ?> HUF</p>
            </div>
        </section>
            <?php else : ?>
                <h1 style="color:red"><?= implode(" / ", $error) ?></h1>
            <?php endif?>

        <section class="booking-form">
            <h2>Book This Car</h2>
            <form action="" method="post">
                <input type="hidden" name="car_id" value="<?= $car['id'] ?>" readonly>
                <div>
                    <label for="from-date">From</label>
                    <input type="date" id="from-date" name="from-date" required>
                </div>
                <div>
                    <label for="until-date">Until</label>
                    <input type="date" id="until-date" name="until-date" required>
                </div>
                <button type="submit">Book It</button>
            </form>
        </section>
    </main>
</body>
</html>
