<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once __DIR__ . '/../config/database.php';
include_once __DIR__ . '/../models/Tour.php';

$database = new Database();
$db = $database->getConnection();
$tour = new Tour($db);

$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'GET':
        $stmt = $tour->read();
        $num = $stmt->rowCount();
        
        if($num > 0) {
            $tours_arr = array();
            $tours_arr["data"] = array();
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $tour_item = array(
                    "id" => $id,
                    "country_id" => $country_id,
                    "country_name" => $country_name,
                    "name" => $name,
                    "description" => $description,
                    "start_date" => $start_date,
                    "end_date" => $end_date,
                    "price" => $price,
                    "max_people" => $max_people,
                    "available_spots" => $available_spots,
                    "created_at" => $created_at
                );
                array_push($tours_arr["data"], $tour_item);
            }
            
            http_response_code(200);
            echo json_encode($tours_arr);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Туры не найдены."));
        }
        break;
        
    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        
        if(!empty($data->country_id) && !empty($data->name) && !empty($data->start_date) && 
           !empty($data->end_date) && !empty($data->price) && isset($data->max_people)) {
            
            $tour->country_id = $data->country_id;
            $tour->name = $data->name;
            $tour->description = $data->description ?? '';
            $tour->start_date = $data->start_date;
            $tour->end_date = $data->end_date;
            $tour->price = $data->price;
            $tour->max_people = $data->max_people;
            $tour->available_spots = $data->available_spots ?? $data->max_people;
            
            if ($tour->tourExists()) {
                http_response_code(409); // Conflict
                echo json_encode(array("message" => "Тур с таким названием и датами уже существует."));
                return;
            }
            
            if($tour->create()) {
                http_response_code(201);
                echo json_encode(array("message" => "Тур успешно создан."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Невозможно создать тур. Ошибка сервера."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Невозможно создать тур. Недостаточно данных."));
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(array("message" => "Метод не поддерживается."));
        break;
}
?>