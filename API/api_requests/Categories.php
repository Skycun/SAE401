<?php
/**
 * Categories API Endpoint
 *
 * This file handles API requests for the Categories entity.
 * Supported HTTP methods: GET, POST, PUT, DELETE.
 * 
 * - GET:    Retrieve all categories, a specific category by ID, or search categories by name.
 * - POST:   Create a new category (requires API key).
 * - PUT:    Update an existing category (requires API key).
 * - DELETE: Delete a category by ID (requires API key).
 * 
 * API key is required for POST, PUT, and DELETE requests.
 * 
 * Headers:
 *   - Api: Your API key (required for POST, PUT, DELETE)
 *   - Content-Type: application/json
 * 
 * Query Parameters:
 *   - action: (getAll|get|search) [GET]
 *   - id: Category ID [GET, DELETE, PUT]
 *   - q: Category name to search [GET, action=search]
 * 
 * Request Body (JSON):
 *   - category_name: string [POST, PUT]
 *   - id: int [PUT]
 * 
 * Responses:
 *   - 200: Success, returns JSON data
 *   - 401: Unauthorized (invalid API key)
 *   - 400: Bad request (missing parameters)
 *   - 404: Not found (category not found)
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
use Entity\Categories;
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

    // GET: Retrieve categories
    case "GET":
        $catRepo = $entityManager->getRepository(Categories::class);

        // If action isn't set or is getAll, return all categories
        if(!isset($_REQUEST["action"]) || $_REQUEST["action"] == "getAll"){ 
            $categories = $catRepo->findAll();
            $categoriesArray = [];
            foreach($categories as $category){
                $categoriesArray[] = $category->jsonSerialize();
            }
            echo json_encode($categoriesArray);
            break;
        }

        switch ($_REQUEST["action"]) {
            // GET: Retrieve a category by ID
            case 'get':
                if(!isset($_REQUEST["id"])){
                    throw new Error("ID not found");
                }
                $category = $catRepo->find($_REQUEST["id"]);
                if($category == null){
                     throw new Error("Category not found");
                }
                echo json_encode($category->jsonSerialize());
                break;
            
            // GET: Search categories by name
            case 'search':
                if(!isset($_REQUEST["q"])){
                    throw new Error("Querry not found");
                }
                $categories = $catRepo->findBy(["category_name" => $_REQUEST["q"]]);
                $categoriesArray = [];
                foreach($categories as $category){
                    $categoriesArray[] = $category->jsonSerialize();
                }
                if(count($categoriesArray) == 0){
                    throw new Error("Category not found");
                }
                echo json_encode($categoriesArray);
                break;
            
            default:
                throw new Error("Action not found");
                break;
            }
            break;
    // POST: Create a new category
    case "POST":
        $data = json_decode(file_get_contents("php://input"), true);
        if(!isset($data["category_name"])){
            throw new Error("category_name not found");
            break;
        }

        $category = new Categories();
        $category->setCategoryName($data["category_name"]);
        $entityManager->persist($category);
        $entityManager->flush();
        $res = Array("state" => "success", "category" => $category->jsonSerialize());
        echo json_encode($res);
        break;
    // DELETE: Delete a category by ID
    case "DELETE":
        $catRepo = $entityManager->getRepository(Categories::class);
        if(!isset($_REQUEST["id"])){
            throw new Error("ID not found");
        }
        $category = $catRepo->find($_REQUEST["id"]);
        if($category == null){
            throw new Error("Category not found");
        }
        $entityManager->remove($category);
        $entityManager->flush();
        $res = Array("state" => "success");
        echo json_encode($res);
        break;
    // PUT: Update a category
    case "PUT":
        $data = json_decode(file_get_contents("php://input"), true);
        if(!isset($data["category_name"]) || !isset($data["id"])){
            throw new Error("category_name or ID not found");
            break;
        }

        $catRepo = $entityManager->getRepository(Categories::class);
        $category = $catRepo->find($data["id"]);
        if($category == null){
            throw new Error("Category not found");
        }
        $category->setCategoryName($data["category_name"]);
        $entityManager->flush();
        $res = Array("state" => "success", "category" => $category->jsonSerialize());
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