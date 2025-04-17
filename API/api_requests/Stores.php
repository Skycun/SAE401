<?php
/**
 * Stores API Endpoint
 *
 * This file handles API requests for the Stores entity.
 * Supported HTTP methods: GET, POST, PUT, DELETE.
 *
 * - GET:    Retrieve all stores, a specific store by ID, or search stores by name.
 * - POST:   Create a new store (requires API key).
 * - PUT:    Update an existing store (requires API key).
 * - DELETE: Delete a store by ID (requires API key).
 *
 * API key is required for POST, PUT, and DELETE requests.
 *
 * Headers:
 *   - Api: Your API key (required for POST, PUT, DELETE)
 *   - Content-Type: application/json
 *
 * Query Parameters:
 *   - action: (getAll|get|search) [GET]
 *   - id: Store ID [GET, DELETE, PUT]
 *   - q: Store name to search [GET, action=search]
 *
 * Request Body (JSON):
 *   - store_name: string [POST, PUT] - Name of the store
 *   - phone: string [POST, PUT] - Phone number of the store
 *   - email: string [POST, PUT] - Email address of the store
 *   - street: string [POST, PUT] - Street address of the store
 *   - city: string [POST, PUT] - City where the store is located
 *   - state: string [POST, PUT] - State where the store is located
 *   - zip_code: string [POST, PUT] - Zip code of the store
 *   - id: int [PUT] - ID of the store to update
 *
 * Responses:
 *   - 200: Success, returns JSON data
 *   - 401: Unauthorized (invalid API key)
 *   - 400: Bad request (missing parameters)
 *   - 404: Not found (store not found)
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

        // GET: Retrieve stores with various filters
        case "GET":
            $storeRepo = $entityManager->getRepository(Stores::class);

            // If action isn't set or is getAll, return all stores
            if(!isset($_REQUEST["action"]) || $_REQUEST["action"] == "getAll"){ 
                $stores = $storeRepo->findAll();
                $storesArray = [];
                foreach($stores as $store){
                    $storesArray[] = $store->jsonSerialize();
                }
                echo json_encode($storesArray);
                break;
            }

            switch ($_REQUEST["action"]) {
                // GET: Retrieve a store by ID
                case 'get':
                    if(!isset($_REQUEST["id"])){
                        throw new Error("ID not found");
                    }
                    $store = $storeRepo->find($_REQUEST["id"]);
                    if($store == null){
                         throw new Error("Store not found");
                    }
                    echo json_encode($store->jsonSerialize());
                    break;
                
                // GET: Search stores by name (partial match)
                case 'search':
                    if(!isset($_REQUEST["q"])){
                        throw new Error("Query not found");
                    }
                    
                    $searchTerm = '%' . $_REQUEST["q"] . '%';
                    
                    // Use QueryBuilder with LIKE for partial matching
                    $qb = $entityManager->createQueryBuilder();
                    $qb->select('s')
                       ->from('Entity\Stores', 's')
                       ->where('s.store_name LIKE :searchTerm')
                       ->setParameter('searchTerm', $searchTerm)
                       ->orderBy('s.store_name', 'ASC');
                    
                    $stores = $qb->getQuery()->getResult();
                    
                    $storesArray = [];
                    foreach($stores as $store){
                        $storesArray[] = $store->jsonSerialize();
                    }
                    
                    if(count($storesArray) == 0){
                        throw new Error("No stores found matching '" . $_REQUEST["q"] . "'");
                    }
                    
                    echo json_encode($storesArray);
                    break;
                
                default:
                    throw new Error("Action not found");
                    break;
                }
                break;

        // POST: Create a new store
        case "POST":
            $data = json_decode(file_get_contents("php://input"), true);
            if(!isset($data["store_name"]) || !isset($data["phone"]) || !isset($data["email"]) || !isset($data["street"]) || !isset($data["city"]) || !isset($data["state"]) || !isset($data["zip_code"])){
                throw new Error("Store data not found");
                break;
            }

            $store = new Stores();
            $store->setStoreName($data["store_name"]);
            $store->setPhone($data["phone"]);
            $store->setEmail($data["email"]);
            $store->setStreet($data["street"]);
            $store->setCity($data["city"]);
            $store->setState($data["state"]);
            $store->setZipCode($data["zip_code"]);
            $entityManager->persist($store);
            $entityManager->flush();
            $res = Array("state" => "success", "store" => $store->jsonSerialize());
            echo json_encode($res);
            break;

        // DELETE: Delete a store by ID
        case "DELETE":
            if(!isset($_REQUEST["id"])){
                throw new Error("ID not found");
            }
            $storeRepo = $entityManager->getRepository(Stores::class);
            $store = $storeRepo->find($_REQUEST["id"]);
            if($store == null){
                throw new Error("Store not found");
            }
            $entityManager->remove($store);
            $entityManager->flush();
            $res = Array("state" => "success");
            echo json_encode($res);
            break;

        // PUT: Update an existing store
        case "PUT":
            $data = json_decode(file_get_contents("php://input"), true);
            if(!isset($data["id"])){
                throw new Error("Store ID not found");
                break;
            }
            $storeRepo = $entityManager->getRepository(Stores::class);
            $store = $storeRepo->find($data["id"]);
            if($store == null){
                throw new Error("Store not found");
            }

            $nbOfChange = 0;
            if(isset($data["store_name"])){
                $store->setStoreName($data["store_name"]);
                $nbOfChange++;
            }
            if(isset($data["phone"])){
                $store->setPhone($data["phone"]);
                $nbOfChange++;
            }
            if(isset($data["email"])){
                $store->setEmail($data["email"]);
                $nbOfChange++;
            }
            if(isset($data["street"])){
                $store->setStreet($data["street"]);
                $nbOfChange++;
            }
            if(isset($data["city"])){
                $store->setCity($data["city"]);
                $nbOfChange++;
            }
            if(isset($data["state"])){
                $store->setState($data["state"]);
                $nbOfChange++;
            }
            if(isset($data["zip_code"])){
                $store->setZipCode($data["zip_code"]);
                $nbOfChange++;
            }

            $entityManager->persist($store);
            $entityManager->flush();
            $res = Array("state" => "success", "changes" => $nbOfChange, "store" => $store->jsonSerialize());
        
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