<?php
    //Config
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");

    require __DIR__ . '/bootstrap.php';
    use Entity\Brands;
    $request_method = $_SERVER["REQUEST_METHOD"];

    switch($request_method){
        case "GET": //If the request method is in GET
            try {
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
            } catch (Error $error) {
                echo json_encode(["error" => $error->getMessage()]);
                break;
            }
    }

?>