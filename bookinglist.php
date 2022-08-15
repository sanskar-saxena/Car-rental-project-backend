<?php 
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


require __DIR__.'/classes/Database.php';
$obj1 = new Database();
$conn = $obj1->dbconnection();
$sql1 = "SELECT * FROM `bookings`";
$res = $conn->prepare($sql1);
$res->execute();

$bookings_array = array();
        $bookings_array['data'] = array();

        while($row = $res->fetch(PDO::FETCH_ASSOC)) {
            extract($row);

            $booking_item = array(
                'booked_car_id' => $booked_car_id,
                'user_email' => $user_email,
                'booked_vehicle_model' => $booked_vehicle_model,
                'booked_vehicle_num' => $booked_vehicle_num,
                'start_date'=> $start_date,
                'no_of_days'=> $no_of_days
             );

            // push to "data"
            array_push($bookings_array['data'], $booking_item);
        }

        // Turn to JSON & OUTPUT
        echo json_encode($bookings_array);

?>