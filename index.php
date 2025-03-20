<?php
include_once("bookingstorage.php");
include_once("userstorage.php");
include_once("carstorage.php");

session_start();

$user_session = $_SESSION["user"] ?? null;
$bookingSt = new BookingStorage();
$userSt = new UserStorage();


$userData = array_values($userSt->getContactsByEmail($user_session))[0] ?? null;

$cs = new CarsStorage();
$list = $cs->findAll();

$result = $list;

$errors = [];
$error = [];
$seats = null;
$gears = null;
$min = null;
$max = null;


if(count($_GET) > 0 )
{
    if(isset($_GET["seats"]))
    {
        if(trim($_GET["seats"]) !== 0)
        {
            if(filter_var(trim($_GET["seats"]), FILTER_VALIDATE_INT) !== false)
            {
                $seats = (int)$_GET["seats"];
            }
        }

    }

    if(isset($_GET["gear"]))
    {
        if(trim($_GET["gear"]) !== "")
        {
            if(in_array($_GET["gear"], ['Automatic', 'Manual'], true))
            {
                $gears = $_GET["gear"];
            }
        }

    }


    if(isset($_GET["min-price"]))
    {
        if(trim($_GET["min-price"]) !== 0)
        {
            if(filter_var(trim($_GET["min-price"]), FILTER_VALIDATE_INT) !== false)
            {
                $min = (int)$_GET["min-price"];
            }
        }

    }

    if(isset($_GET["max-price"]))
    {
        if(trim($_GET["max-price"]) !== 0)
        {
            if(filter_var(trim($_GET["max-price"]), FILTER_VALIDATE_INT) !== false)
            {
                $max = (int)$_GET["max-price"];
            }
        }

    }
    if($seats !== null && $seats < 1)
    {
        $errors[] = "number of seats cannot be less than 1";
    }
    if($min !== null && $max !== null && $min > $max)
    {
        $errors[] = "Minimum price cannot be more than maximum price";
    }

    

    $filtered_cars = array_filter($list, function($car) use($seats, $gears, $min, $max) {
        $match = true;

        if($seats !== null)
        {
            $match = $match && $car['passengers'] === $seats;
        }
        if($gears !== null)
        {
            $match = $match && $car['transmission'] === $gears;
        }
        if($min !== null)
        {
            $match = $match && $car['daily_price_huf'] >= $min;
        }
        if($max !== null)
        {
            $match = $match && $car['daily_price_huf'] <= $max;
        }
        return $match;
    }
    );

    if(!empty($filtered_cars))
    {
        usort($filtered_cars, fn($a, $b) => (int)$a['daily_price_huf'] <=> (int)($b['daily_price_huf']));
        $result = $filtered_cars;
    }
    else
    {
        usort($list, fn($a, $b) => (int)$a['daily_price_huf'] <=> (int)($b['daily_price_huf']));
        $result = $list;
        $errors[] = "Unfortunately there is no available car matching your needs, see other options below";
    }

    }
    if(count($_POST) > 0)
    {

    $carId = isset($_POST['car_id']) && (trim($_POST['car_id'])) ? trim($_POST['car_id']) : null;
    $startDate = isset($_POST['from-date']) && strtotime(trim($_POST['from-date'])) ? trim($_POST['from-date']) : null;
    $endDate = isset($_POST['until-date']) && strtotime(trim($_POST['until-date'])) ? trim($_POST['until-date']) : null;

        $currentDate = strtotime(date('Y-m-d'));

    if (!$startDate || !$endDate || strtotime($startDate) < $currentDate || strtotime($endDate) < strtotime($startDate)) {

        $error[] = "Invalid date input";
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
    <title>IKarRental</title>
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

        .car-listing {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        justify-content: center;
        padding: 20px;
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
        <h1>Rent a Car Easily!</h1>

        <section>
            <h2>Filter Cars</h2>
            <form action="" method="get">
                <div>
                    <label for="seats">Seats</label>
                    <input type="text" id="seats" name="seats" placeholder="Number of seats">
                </div>
                <div>
                    <label for="gear">Gear Type</label>
                    <select id="gear" name="gear">
                        <option value="Automatic">Automatic</option>
                        <option value="Manual">Manual</option>
                    </select>
                </div>
                
                <div>
                    <label for="min-price">Min Price</label>
                    <input type="number" id="min-price" name="min-price" placeholder="Min price">
                </div>
                <div>
                    <label for="max-price">Max Price</label>
                    <input type="number" id="max-price" name="max-price" placeholder="Max price">
                </div>
                <div>
            <label for="from-date-filter">From Date</label>
            <input type="date" id="from-date-filter" name="from-date">
        </div>
        <div>
            <label for="until-date-filter">Until Date</label>
            <input type="date" id="until-date-filter" name="until-date">
        </div>
                <div>
                    <button type="submit">Filter</button>
                </div>
            </form>
        </section>

        <section>
            <h2>Arrange Renting Time</h2>
            <form>
                <div>
                    
                </div>
                <div>
                    
                </div>
                <?php if(count($errors) !== 0) :  ?>
                    <h1><?= implode(" / ", $errors) ?></h1>
                <?php endif ?>
            </form>
        </section>

        <section>
            <h2>Car Listings</h2>
            <div class="car-listing">
            <?php foreach($result as $car): ?>
                
                <div class="car">
                  <a href="car.php?id=<?=$car["id"]?>"> <img src="<?= $car["image"] ?>" alt="Car Image"> </a>    
                    
                    <h2><?= $car["brand"]." ".$car["model"] ?></h2>
                    <p>Daily Price: <?= $car["daily_price_huf"] ?></p>
                    <p>Seat Number: <?= $car["passengers"] ?></p>
                    <p>Type: <?= $car["transmission"] . "/". $car["fuel_type"]?></p>
                    <form action="" method="post">
                    <label for="from-date">From</label>
                    <input type="date" id="from-date" name="from-date">
                    <label for="until-date">Until</label>
                    <input type="date" id="until-date" name="until-date">
                    <input readonly hidden type="text" name="car_id" value="<?= $car["id"] ?>">
                    <button>Book</button>
                    </form>
                   
                </div>
                <?php endforeach ?>
            </div>
        </section>
    </main>
</body>
</html>
