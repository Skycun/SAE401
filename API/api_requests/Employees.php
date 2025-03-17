<?php
    //Config
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");

    // Définir votre clé API
    define('API_KEY', 'e8f1997c763');

    require __DIR__ . '/../bootstrap.php';
    use Entity\Employees;
    use Entity\Stores;

    $request_method = $_SERVER["REQUEST_METHOD"];

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
    
    validateApiKey();
    
    try{
    switch($request_method){

        //If the request method is in GET
        case "GET":
            $EmpRepo = $entityManager->getRepository(Employees::class);
            //If action isn't get or the action is getAll, return all the employees
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
                //If the action is get, return the employee with the id
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
                
                //If the action is search, return the employee with the name
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
                //If the action is store, return the employees of a store
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
                case "login":
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

                default:
                    throw new Error("Action not found");
                    break;
                }
                break;
        case "POST":
            $data = json_decode(file_get_contents("php://input"), true);
            if(!isset($data["employees_name"]) || !isset($data["employees_email"]) || !isset($data["employees_password"]) || !isset($data["employees_role"]) || !isset($data["store_id"])){
                throw new Error("Some data is missing, check your : employees_name, employees_email, employees_password, employees_role, store_id");
                break;
            }

            //Créer l'employée
            $employee = new Employees();
            //Met le nom de l'employee
            $employee->setEmployeesName($data["employees_name"]);
            //Met l'email de l'employee
            $employee->setEmployeesEmail($data["employees_email"]);
            //Met le mot de passe de l'employee
            $employee->setEmployeesPassword($data["employees_password"]);
            //Met le role de l'employee
            $employee->setEmployeesRole($data["employees_role"]);
            //Met le store de l'employee
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
            if(isset($data["employees_password"])){
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