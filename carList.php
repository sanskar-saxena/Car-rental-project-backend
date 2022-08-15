<?php 
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


require __DIR__.'/classes/Database.php';
$obj1 = new Database();
$conn = $obj1->dbconnection();
$sql1 = "SELECT * FROM `cars`";
$res = $conn->prepare($sql1);
$res->execute();

$cars_array = array();
        $cars_array['data'] = array();

        while($row = $res->fetch(PDO::FETCH_ASSOC)) {
            extract($row);

            $car_item = array(
                'vehicle_model' => $vehicle_model,
                'vehicle_num' => $vehicle_num,
                'rent_per_day' => $rent_per_day,
                'seating_capacity' => $seating_capacity,
                'available'=> $availability,
                'car_id'=> $car_id
             );

            // push to "data"
            array_push($cars_array['data'], $car_item);
        }

        // Turn to JSON & OUTPUT
        echo json_encode($cars_array);

?>