<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require __DIR__ . '/classes/Database.php';
$db_connection = new Database();
$conn = $db_connection->dbConnection();

function msg($success, $status, $message, $extra = [])
{
    return array_merge([
        'success' => $success,
        'status' => $status,
        'message' => $message
    ], $extra);
}

// DATA FORM REQUEST
$data = json_decode(file_get_contents("php://input"));
$returnData = [];

if ($_SERVER["REQUEST_METHOD"] != "POST") :

    $returnData = msg(0, 404, 'Page Not Found!');

elseif (
    !isset($data->vehicle_model)
    || !isset($data->vehicle_num)
    || !isset($data->seating_capacity)
    || !isset($data->rent_per_day)  ||  empty($data->vehicle_model)
    ||empty($data->vehicle_num)
    ||empty($data->seating_capacity)
    ||empty($data->rent_per_day)
) :

    $fields = ['fields' => ['vehicle_model', 'vehicle_num', 'seating_capacity','rent_per_day']];
    $returnData = msg(0, 422, 'Please Fill in all Required Fields!', $fields);

// IF THERE ARE NO EMPTY FIELDS THEN-
else :

    $vehicle_model = trim($data->vehicle_model);
    $vehicle_num = trim($data->vehicle_num);
    $seating_capacity = trim($data->seating_capacity);
    $rent_per_day = trim($data->rent_per_day);
    
    if (strlen($seating_capacity) < 1) :
        $returnData = msg(0, 422, 'Enter Valid Seating Capacity!');

    else :
        try {

            $check_model = "SELECT `vehicle_model` FROM `cars` WHERE `vehicle_model`=:vehicle_model";
            $check_model_stmt = $conn->prepare($check_model);
            $check_model_stmt->bindValue(':vehicle_model', $vehicle_model, PDO::PARAM_STR);
            $check_model_stmt->execute();

            if ($check_model_stmt->rowCount()) :
                $returnData = msg(0, 422, 'This Model already exist!');

            else :
                $insert_query = "INSERT INTO `cars`(`vehicle_model`, `vehicle_num`, `seating_capacity`,`rent_per_day`) VALUES(:vehicle_model, :vehicle_num, :seating_capacity,:rent_per_day)";

                $insert_stmt = $conn->prepare($insert_query);

                // DATA BINDING
                $insert_stmt->bindValue(':vehicle_model', htmlspecialchars(strip_tags($vehicle_model)), PDO::PARAM_STR);
                $insert_stmt->bindValue(':vehicle_num', $vehicle_num, PDO::PARAM_STR);
                $insert_stmt->bindValue(':seating_capacity', $seating_capacity, PDO::PARAM_INT);
                $insert_stmt->bindValue(':rent_per_day', $rent_per_day, PDO::PARAM_INT);
                

                $insert_stmt->execute();

                $returnData = msg(1, 201, 'You have successfully added a car.');

            endif;
        } catch (PDOException $e) {
            $returnData = msg(0, 500, $e->getMessage());
        }
    endif;
endif;

echo json_encode($returnData);