<?php
    //Config
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");

    // Définir votre clé API
    define('API_KEY', 'e8f1997c763');

    // Vérifier la clé API
    function validateApiKey() {
        $headers = getallheaders();        
        // Vérifier si la clé est présente dans les headers
        if (!isset($headers['Api']) || $headers['Api'] !== API_KEY) {
            header('HTTP/1.1 401 Unauthorized');
            echo json_encode(['error' => 'Invalid API Key']);
            exit();
        }
    }


    require __DIR__ . '/bootstrap.php';
    use Entity\Brands;
    $request_method = $_SERVER["REQUEST_METHOD"];

    validateApiKey();

    try{
    switch($request_method){

        //If the request method is in GET
        case "GET":
            $brandRepo = $entityManager->getRepository(Brands::class);

            //If action isn't get or the action is getAll, return all the brands
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
                //If the action is get, return the brand with the id
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
                
                //If the action is search, return the brands with the name
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
    }

    } catch (Error $error) {
        echo json_encode(["error" => $error->getMessage()]);
    }

?>