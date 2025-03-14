<?php
    //Config
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");

    // Définir votre clé API
    define('API_KEY', 'e8f1997c763');

    require __DIR__ . '/../bootstrap.php';
    use Entity\Stocks;
    use Entity\Products;
    use Entity\Stores;
    $request_method = $_SERVER["REQUEST_METHOD"];

    // Vérifier la clé API
    function validateApiKey() {
        $headers = getallheaders();        
        if($_SERVER["REQUEST_METHOD"] == "GET"){ //Si la méthode est GET, on ne vérifie pas la clé API
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
            $stockRepo = $entityManager->getRepository(Stocks::class);

            //If action isn't get or the action is getAll, return all the stocks of all the stores
            if(!isset($_REQUEST["action"]) || $_REQUEST["action"] == "getAll"){ 
                $stocks = $stockRepo->findAll();
                $stocksArray = [];
                foreach($stocks as $stock){
                    $stocksArray[] = $stock->jsonSerialize();
                }
                echo json_encode($stocksArray);
                break;
            }

            switch ($_REQUEST["action"]) {
                //If the action is get, return the stock with the id
                case 'get':
                    if(!isset($_REQUEST["id"])){
                        throw new Error("ID not found");
                    }
                    $stock = $stockRepo->find($_REQUEST["id"]);
                    if($stock == null){
                         throw new Error("Brand not found");
                    }
                    echo json_encode($stock->jsonSerialize());
                    break;
                
                //If the action is getFromStore, return the stock of the store with the store_id
                case 'getFromStore':
                    if(!isset($_REQUEST["store_id"])){
                        throw new Error("Store ID not found");
                    }
                    $store = $entityManager->getRepository(Stores::class)->find($_REQUEST["store_id"]);
                    if($store == null){
                        throw new Error("Store not found");
                    }
                    $stocks = $stockRepo->findBy(["store" => $store]);
                    $stocksArray = [];
                    foreach($stocks as $stock){
                        $stocksArray[] = $stock->jsonSerialize();
                    }
                    echo json_encode($stocksArray);
                    break;
                
                default:
                    throw new Error("Action not found");
                    break;
                }
                break;
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
        case "PUT":
            $data = json_decode(file_get_contents("php://input"), true);
            if(!isset($data["brand_name"]) || !isset($data["id"])){
                throw new Error("Brand name or ID not found");
                break;
            }

            $brandRepo = $entityManager->getRepository(Brands::class);
            $brand = $brandRepo->find($data["id"]);
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