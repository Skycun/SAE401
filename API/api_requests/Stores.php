<?php
    //Config
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, Api");
    header("Content-Type: application/json; charset=UTF-8");

    // Définir votre clé API
    define('API_KEY', 'e8f1997c763');

    require __DIR__ . '/../bootstrap.php';
    use Entity\Stores;
    $request_method = $_SERVER["REQUEST_METHOD"];

    // Vérifier la clé API
    function validateApiKey() {
        $headers = getallheaders();
        
        // Exempter les requêtes OPTIONS de la vérification API
        if($_SERVER["REQUEST_METHOD"] == "OPTIONS"){
            return;
        }
        
        // Pour l'action login et les requêtes GET, exempter de la vérification
        if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "login" || $_SERVER["REQUEST_METHOD"] == "GET"){
            return;
        }
        
        // Vérifier si la clé est présente dans les headers
        if (!isset($headers['Api']) || $headers['Api'] !== API_KEY) {
            header('HTTP/1.1 401 Unauthorized');
            echo json_encode(['error' => 'Invalid API Key']);
            exit();
        }
    }
    validateApiKey();

    try{
    switch($request_method){

        //If the request method is in GET
        case "GET":
            $storeRepo = $entityManager->getRepository(Stores::class);

            //If action isn't get or the action is getAll, return all the stores
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
                //If the action is get, return the store with the id
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
                
                //If the action is search, return the stores with the name
                case 'search':
                    if(!isset($_REQUEST["q"])){
                        throw new Error("Query not found");
                    }
                    
                    $searchTerm = '%' . $_REQUEST["q"] . '%';
                    
                    // QueryBuilder avec LIKE pour une recherche partielle
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
        case "PUT":
            $data = json_decode(file_get_contents("php://input"), true);
            if(!isset($data["id"])){
                throw new Error("Store name or ID not found");
                break;
            }
            $storeRepo = $entityManager->getRepository(Stores::class);
            $store = $storeRepo->find($data["id"]);
            if($store == null){
                throw new Error("Brand not found");
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