<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once __DIR__ . '/../config/database.php';
include_once __DIR__ . '/../models/Booking.php';

$database = new Database();
$db = $database->getConnection();
$booking = new Booking($db);

$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'GET':
        $stmt = $booking->read();
        $num = $stmt->rowCount();
        
        if($num > 0) {
            $bookings_arr = array();
            $bookings_arr["data"] = array();
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $booking_item = array(
                    "id" => $id,
                    "client_id" => $client_id,
                    "client_name" => $client_name,
                    "tour_id" => $tour_id,
                    "tour_name" => $tour_name,
                    "booking_date" => $booking_date,
                    "status" => $status,
                    "total_price" => $total_price,
                    "notes" => $notes,
                    "created_at" => $created_at
                );
                array_push($bookings_arr["data"], $booking_item);
            }
            
            http_response_code(200);
            echo json_encode($bookings_arr);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Бронирования не найдены."));
        }
        break;
        
    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        
        if(!empty($data->client_id) && !empty($data->tour_id) && !empty($data->booking_date)) {
            
            $booking->client_id = $data->client_id;
            $booking->tour_id = $data->tour_id;
            $booking->booking_date = $data->booking_date;
            $booking->status = $data->status ?? 'pending';
            $booking->total_price = $data->total_price ?? 0;
            $booking->notes = $data->notes ?? '';
            
            if($booking->create()) {
                http_response_code(201);
                echo json_encode(array("message" => "Бронирование успешно создано."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Невозможно создать бронирование."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Невозможно создать бронирование. Недостаточно данных."));
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(array("message" => "Метод не поддерживается."));
        break;
}
?>