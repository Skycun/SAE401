<?php
/**
 * Employees API Endpoint
 *
 * This file handles API requests for the Employees entity.
 * Supported HTTP methods: GET, POST, PUT, DELETE.
 *
 * - GET:    Retrieve all employees, a specific employee by ID, search employees by name, or get employees by store.
 * - POST:   Create a new employee or login (requires API key for creation).
 * - PUT:    Update an existing employee (requires API key).
 * - DELETE: Delete an employee by ID (requires API key).
 *
 * API key is required for POST (except login), PUT, and DELETE requests.
 *
 * Headers:
 *   - Api: Your API key (required for POST [except login], PUT, DELETE)
 *   - Content-Type: application/json
 *
 * Query Parameters:
 *   - action: (getAll|get|search|store|login) [GET, POST]
 *   - id: Employee ID [GET, DELETE, PUT]
 *   - q: Employee name to search [GET, action=search]
 *   - store_id: Store ID [GET, action=store]
 *
 * Request Body (JSON):
 *   - employees_name: string [POST, PUT]
 *   - employees_email: string [POST, PUT]
 *   - employees_password: string [POST, PUT]
 *   - employees_role: string [POST, PUT]
 *   - store_id: int [POST, PUT]
 *   - id: int [PUT]
 *   - email: string [POST, action=login]
 *   - password: string [POST, action=login]
 *
 * Responses:
 *   - 200: Success, returns JSON data
 *   - 401: Unauthorized (invalid API key)
 *   - 400: Bad request (missing parameters)
 *   - 404: Not found (employee or store not found)
 *   - 500: Internal server error
 *
 * @package API\Requests
 */

//Config
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, Api");
header("Content-Type: application/json; charset=UTF-8");

// Define your API key
define('API_KEY', 'e8f1997c763');

require __DIR__ . '/../bootstrap.php';
use Entity\Employees;
use Entity\Stores;

$request_method = $_SERVER["REQUEST_METHOD"];

/**
 * Validate the API key from headers.
 * Exempts OPTIONS requests and GET/login actions.
 * Sends 401 Unauthorized and exits on failure.
 *
 * @return void
 */
function validateApiKey() {
    $headers = getallheaders();
    
    // Exempt OPTIONS requests from API key check
    if($_SERVER["REQUEST_METHOD"] == "OPTIONS"){
        return;
    }
    
    // Exempt login action and GET requests from API key check
    if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "login" || $_SERVER["REQUEST_METHOD"] == "GET"){
        return;
    }
    
    // Check if the API key is present and valid
    if (!isset($headers['Api']) || $headers['Api'] !== API_KEY) {
        header('HTTP/1.1 401 Unauthorized');
        echo json_encode(['error' => 'Invalid API Key']);
        exit();
    }
}
validateApiKey();

try{
switch($request_method){

    // GET: Retrieve employees
    case "GET":
        $EmpRepo = $entityManager->getRepository(Employees::class);
        // If action isn't set or is getAll, return all employees
        if(!isset($_REQUEST["action"]) || $_REQUEST["action"] == "getAll"){ 
            $employees = $EmpRepo->findAll();
            $employeesArray = [];
            foreach($employees as $employee){
                $employeesArray[] = $employee->jsonSerialize();
            }
            echo json_encode($employeesArray);
            break;
        }
        switch ($_REQUEST["action"]) {
            // GET: Retrieve an employee by ID
            case 'get':
                if(!isset($_REQUEST["id"])){
                    throw new Error("ID not found");
                }
                $employee = $EmpRepo->find($_REQUEST["id"]);
                if($employee == null){
                     throw new Error("Product not found");
                }
                echo json_encode($employee->jsonSerialize());
                break;
            
            // GET: Search employees by name
            case 'search':
                if(!isset($_REQUEST["q"])){
                    throw new Error("Querry not found");
                }
                
                $employees = $EmpRepo->findBy(["employees_name" => $_REQUEST["q"]]);
                $employeesArray = [];
                foreach($employees as $employee){
                    $employeesArray[] = $employee->jsonSerialize();
                }
                if(count($employeesArray) == 0){
                    throw new Error("Employees not found");
                }
                echo json_encode($employeesArray);
                break;
            // GET: Retrieve employees by store
            case 'store':
                if(!isset($_REQUEST["store_id"])){
                    throw new Error("Store ID not found");
                }
                $store = $entityManager->getRepository(Stores::class)->find($_REQUEST["store_id"]);
                if($store == null){
                    throw new Error("Store not found");
                }
                $employees = $EmpRepo->findBy(["store" => $store]);
                $employeesArray = [];
                foreach($employees as $employee){
                    $employeesArray[] = $employee->jsonSerialize();
                }
                if(count($employeesArray) == 0){
                    throw new Error("Employees not found");
                }
                echo json_encode($employeesArray);
                break;
            default:
                throw new Error("Action not found");
                break;
            }
            break;
    // POST: Create a new employee or login
    case "POST":
        if(!isset($_REQUEST["action"]) || $_REQUEST["action"] == "create"){
            $data = json_decode(file_get_contents("php://input"), true);
            if(!isset($data["employees_name"]) || !isset($data["employees_email"]) || !isset($data["employees_password"]) || !isset($data["employees_role"]) || !isset($data["store_id"])){
                throw new Error("Some data is missing, check your : employees_name, employees_email, employees_password, employees_role, store_id");
                break;
            }

            // Create the employee
            $employee = new Employees();
            $employee->setEmployeesName($data["employees_name"]);
            $employee->setEmployeesEmail($data["employees_email"]);
            $employee->setEmployeesPassword($data["employees_password"]);
            $employee->setEmployeesRole($data["employees_role"]);
            $store = $entityManager->getRepository(Stores::class)->find($data["store_id"]);
            if($store == null){
                throw new Error("Store not found, change the store_id");
            }
            $employee->setStore($store);
            $entityManager->persist($employee);
            $entityManager->flush();
            $res = Array("state" => "success", "employee" => $employee->jsonSerialize());
            echo json_encode($res);
            break;
        }
        if($_REQUEST["action"] == "login"){
            $EmpRepo = $entityManager->getRepository(Employees::class);
            $data = json_decode(file_get_contents("php://input"), true);
            if(!isset($data["email"]) || !isset($data["password"])){
                throw new Error("Some data is missing, check your : email, password");
                break;
            }
            $employee = $EmpRepo->findOneBy(["employees_email" => $data["email"], "employees_password" => $data["password"]]);
            if($employee == null){
                throw new Error("Employee not found, check your email and password");
            }
            $employee->setEmployeesPassword("********");
            $res = Array("state" => "success", "message" => "You are connected", "employee" => $employee->jsonSerialize());
            echo json_encode($res);
            break;
        }


    // DELETE: Delete an employee by ID
    case "DELETE":
        $EmpRepo = $entityManager->getRepository(Employees::class);
        if(!isset($_REQUEST["id"])){
            throw new Error("ID not found");
        }
        $employee = $EmpRepo->find($_REQUEST["id"]);
        if($employee == null){
            throw new Error("Employee not found");
        }
        $entityManager->remove($employee);
        $entityManager->flush();
        $res = Array("state" => "success");
        echo json_encode($res);
        break;

    // PUT: Update an employee
    case "PUT":
        $data = json_decode(file_get_contents("php://input"), true);
        if(!isset($data["id"])){
            throw new Error("The ID is not found");
            break;
        }

        $EmpRepo = $entityManager->getRepository(Employees::class);
        $employee = $EmpRepo->find($data["id"]);
        if($employee == null){
            throw new Error("Employee not found");
        }

        $nbOfChange = 0;
        if(isset($data["employees_name"])){
            $employee->setEmployeesName($data["employees_name"]);
            $nbOfChange++;
        }
        if(isset($data["employees_email"])){
            $employee->setEmployeesEmail($data["employees_email"]);
            $nbOfChange++;
        }
        if(isset($data["employees_password"]) && $data["employees_password"] != ""){
            $employee->setEmployeesPassword($data["employees_password"]);
            $nbOfChange++;
        }
        if(isset($data["employees_role"])){
            $employee->setEmployeesRole($data["employees_role"]);
            $nbOfChange++;
        }
        if(isset($data["store_id"])){
            $store = $entityManager->getRepository(Stores::class)->find($data["store_id"]);
            if($store == null){
                throw new Error("Store not found, change the store_id");
            }
            $employee->setStore($store);
            $nbOfChange++;
        }

        $entityManager->flush();
        $res = Array("state" => "success", "employee" => $employee->jsonSerialize(), "changes" => $nbOfChange . " changes");
        echo json_encode($res);
        break;
    default:
        throw new Error("Method not found");
        break;
    }
} catch (Error $error) {
    echo json_encode(["error" => $error->getMessage()]);
}

?>