<?php
    //Config
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, Api");
    header("Content-Type: application/json; charset=UTF-8");
    
    // Définir votre clé API
    define('API_KEY', 'e8f1997c763');

    require __DIR__ . '/../bootstrap.php';
    use Entity\Categories;
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
            $catRepo = $entityManager->getRepository(Categories::class);

            //If action isn't get or the action is getAll, return all the categories
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
                //If the action is get, return the category with the id
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
                
                //If the action is search, return the category with the name
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