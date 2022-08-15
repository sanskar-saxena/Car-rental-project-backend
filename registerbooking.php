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
    !isset($data->booked_car_id)
    || !isset($data->user_email)
    || !isset($data->booked_vehicle_model)
    || !isset($data->booked_vehicle_num)
    || !isset($data->start_date) 
    || !isset($data->no_of_days)
    || empty($data->booked_car_id)
    || empty($data->user_email)
    || empty($data->booked_vehicle_model)
    || empty($data->booked_vehicle_num)
    || empty($data->start_date) 
    || empty($data->no_of_days)
) :

    $fields = ['fields' => ['booked_car_id', 'user_email', 'booked_vehicle_model', 'booked_vehicle_num', 'no_of_days']];
    $returnData = msg(0, 422, 'Please Fill in all Required Fields!', $fields);

// IF THERE ARE NO EMPTY FIELDS THEN-
else :

    $booked_car_id = trim($data->booked_car_id);
    $user_email =  trim($data->user_email);
    $booked_vehicle_model = trim($data->booked_vehicle_model);
    $booked_vehicle_num = trim($data->booked_vehicle_num);
    $start_date = trim($data->start_date);
    $no_of_days = trim($data->no_of_days);

    try {

                $insert_query = "INSERT INTO `bookings`(`booked_car_id`, `user_email`, `booked_vehicle_model`, `booked_vehicle_num`, `start_date`, `no_of_days`) VALUES(:booked_car_id, :user_email, :booked_vehicle_model, :booked_vehicle_num, :start_date, :no_of_days)";

                $insert_stmt = $conn->prepare($insert_query);

                // DATA BINDING
                $insert_stmt->bindValue(':booked_car_id', $booked_car_id, PDO::PARAM_INT);
                $insert_stmt->bindValue(':user_email', $user_email, PDO::PARAM_STR);
                $insert_stmt->bindValue(':booked_vehicle_model', htmlspecialchars(strip_tags($booked_vehicle_model)), PDO::PARAM_STR);
                $insert_stmt->bindValue(':booked_vehicle_num', $booked_vehicle_num, PDO::PARAM_STR);
                $insert_stmt->bindValue(':start_date', $start_date, PDO::PARAM_INT);
                $insert_stmt->bindValue(':no_of_days', $no_of_days, PDO::PARAM_INT);
                $insert_stmt->execute();

                $returnData = msg(1, 201, 'You have successfully booked a car.');

        } catch (PDOException $e) {
            $returnData = msg(0, 500, $e->getMessage());
        }
    endif;

echo json_encode($returnData);