<?php
/**
 * Brands API Endpoint
 *
 * This file handles API requests for the Brands entity.
 * Supported HTTP methods: GET, POST, PUT, DELETE.
 * 
 * - GET:    Retrieve all brands, a specific brand by ID, or search brands by name.
 * - POST:   Create a new brand (requires API key).
 * - PUT:    Update an existing brand (requires API key).
 * - DELETE: Delete a brand by ID (requires API key).
 * 
 * API key is required for POST, PUT, and DELETE requests.
 * 
 * Headers:
 *   - Api: Your API key (required for POST, PUT, DELETE)
 *   - Content-Type: application/json
 * 
 * Query Parameters:
 *   - action: (getAll|get|search) [GET]
 *   - id: Brand ID [GET, DELETE, PUT]
 *   - q: Brand name to search [GET, action=search]
 * 
 * Request Body (JSON):
 *   - brand_name: string [POST, PUT]
 *   - brand_id: int [PUT]
 * 
 * Responses:
 *   - 200: Success, returns JSON data
 *   - 401: Unauthorized (invalid API key)
 *   - 400: Bad request (missing parameters)
 *   - 404: Not found (brand not found)
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
    use Entity\Brands;
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

        // GET: Retrieve brands
        case "GET":
            $brandRepo = $entityManager->getRepository(Brands::class);

            // If action isn't set or is getAll, return all brands
            if(!isset($_REQUEST["action"]) || $_REQUEST["action"] == "getAll"){ 
                $brands = $brandRepo->findAll();
                $brandsArray = [];
                foreach($brands as $brand){
                    $brandsArray[] = $brand->jsonSerialize();
                }
                echo json_encode($brandsArray);
                break;
            }

            switch ($_REQUEST["action"]) {
                // GET: Retrieve a brand by ID
                case 'get':
                    if(!isset($_REQUEST["id"])){
                        throw new Error("ID not found");
                    }
                    $brand = $brandRepo->find($_REQUEST["id"]);
                    if($brand == null){
                         throw new Error("Brand not found");
                    }
                    echo json_encode($brand->jsonSerialize());
                    break;
                
                // GET: Search brands by name
                case 'search':
                    if(!isset($_REQUEST["q"])){
                        throw new Error("Querry not found");
                    }
                    $brands = $brandRepo->findBy(["brand_name" => $_REQUEST["q"]]);
                    $brandsArray = [];
                    foreach($brands as $brand){
                        $brandsArray[] = $brand->jsonSerialize();
                    }
                    if(count($brandsArray) == 0){
                        throw new Error("Brand not found");
                    }
                    echo json_encode($brandsArray);
                    break;
                
                default:
                    throw new Error("Action not found");
                    break;
                }
                break;
        // POST: Create a new brand
        case "POST":
            $data = json_decode(file_get_contents("php://input"), true);
            if(!isset($data["brand_name"])){
                throw new Error("Brand name not found");
                break;
            }

            $brand = new Brands();
            $brand->setBrandName($data["brand_name"]);
            $entityManager->persist($brand);
            $entityManager->flush();
            $res = Array("state" => "success", "brand" => $brand->jsonSerialize());
            echo json_encode($res);
            break;
        // DELETE: Delete a brand by ID
        case "DELETE":
            if(!isset($_REQUEST["id"])){
                throw new Error("ID not found");
            }
            $brandRepo = $entityManager->getRepository(Brands::class);
            $brand = $brandRepo->find($_REQUEST["id"]);
            if($brand == null){
                throw new Error("Brand not found");
            }
            $entityManager->remove($brand);
            $entityManager->flush();
            $res = Array("state" => "success");
            echo json_encode($res);
            break;
        // PUT: Update a brand
        case "PUT":
            $data = json_decode(file_get_contents("php://input"), true);
            if(!isset($data["brand_name"]) || !isset($data["brand_id"])){
                throw new Error("Brand name or ID not found");
                break;
            }

            $brandRepo = $entityManager->getRepository(Brands::class);
            $brand = $brandRepo->find($data["brand_id"]);
            if($brand == null){
                throw new Error("Brand not found");
            }
            $brand->setBrandName($data["brand_name"]);
            $entityManager->persist($brand);
            $entityManager->flush();
            $res = Array("state" => "success", "brand" => $brand->jsonSerialize());
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