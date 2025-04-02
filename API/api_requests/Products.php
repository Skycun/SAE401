<?php
    //Config
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, Api");
    header("Content-Type: application/json; charset=UTF-8");

    // Définir votre clé API
    define('API_KEY', 'e8f1997c763');

    require __DIR__ . '/../bootstrap.php';
    use Entity\Products;
    use Entity\Brands;
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
            $productRepo = $entityManager->getRepository(Products::class);

            //If action isn't get or the action is getAll, return all the products
            if(!isset($_REQUEST["action"]) || $_REQUEST["action"] == "getAll"){ 
                $products = $productRepo->findAll();
                $productsArray = [];
                foreach($products as $product){
                    $productsArray[] = $product->jsonSerialize();
                }
                echo json_encode($productsArray);
                break;
            }

            switch ($_REQUEST["action"]) {
                //If the action is get, return the products with the id
                case 'get':
                    if(!isset($_REQUEST["id"])){
                        throw new Error("ID not found");
                    }
                    $product = $productRepo->find($_REQUEST["id"]);
                    if($product == null){
                         throw new Error("Product not found");
                    }
                    echo json_encode($product->jsonSerialize());
                    break;
                
                //If the action is search, return the product with the name
                case 'search':
                    if(!isset($_REQUEST["q"])){
                        throw new Error("Query not found");
                    }
                    
                    // Recherche plus flexible avec LIKE %query%
                    $products = $productRepo->createQueryBuilder('p')
                        ->where('p.product_name LIKE :query')
                        ->setParameter('query', '%' . $_REQUEST["q"] . '%')
                        ->getQuery()
                        ->getResult();
                    
                    $productsArray = [];
                    foreach($products as $product){
                        $productsArray[] = $product->jsonSerialize();
                    }
                    
                    if(count($productsArray) == 0){
                        throw new Error("No products found matching your search");
                    }
                    
                    echo json_encode($productsArray);
                    break;
                case "advancedSearch":
                    $data = json_decode(file_get_contents("php://input"), true);
                    $priceMin = isset($data["priceMin"]) ? $data["priceMin"] : 0;
                    $priceMax = isset($data["priceMax"]) ? $data["priceMax"] : 10000;
                    $modelYear = isset($data["model_year"]) ? $data["model_year"] : null;
                    $brand = isset($data["brand_id"]) ? $entityManager->getRepository(Brands::class)->find($data["brand_id"]) : null;
                    $category = isset($data["category_id"]) ? $entityManager->getRepository(Categories::class)->find($data["category_id"]) : null;
                    $querry = isset($data["q"]) ? $data["q"] : null;
                    $products = $productRepo->createQueryBuilder('p')
                        ->where('p.list_price BETWEEN :priceMin AND :priceMax AND p.product_name LIKE :query')
                        ->setParameter('query', '%' . $querry . '%')
                        ->setParameter('priceMin', $priceMin)
                        ->setParameter('priceMax', $priceMax)
                        ->andWhere('p.model_year = :modelYear OR :modelYear IS NULL')
                        ->setParameter('modelYear', $modelYear)
                        ->andWhere('p.brand = :brand OR :brand IS NULL')
                        ->setParameter('brand', $brand)
                        ->andWhere('p.category = :category OR :category IS NULL')
                        ->setParameter('category', $category)
                        ->getQuery()
                        ->getResult();
                    $productsArray = [];
                    foreach($products as $product){
                        $productsArray[] = $product->jsonSerialize();
                    }
                    if(count($productsArray) == 0){
                        throw new Error("No products found matching your search");
                    }
                    echo json_encode($productsArray);
                    break;
                //f the action is brand, return the product of the brand
                case 'brand':
                    if(!isset($_REQUEST["brand_id"])){
                        throw new Error("Brand ID not found");
                    }
                    $brand = $entityManager->getRepository(Brands::class)->find($_REQUEST["brand_id"]);
                    if($brand == null){
                        throw new Error("Brand not found");
                    }

                    if(isset($_REQUEST["limit"])  && $_REQUEST["limit"] > 0){
                        $products = $productRepo->findBy(["brand" => $brand], null, $_REQUEST["limit"]);
                        foreach($products as $product){
                            $productsArray[] = $product->jsonSerialize();
                        }
                        echo json_encode($productsArray);
                        break;
                    }
                    $brand->getProducts();
                    $productsArray = [];
                    foreach($brand->getProducts() as $product){
                        $productsArray[] = $product->jsonSerialize();
                    }
                    echo json_encode($productsArray);
                    break;
                case 'category':
                    if(!isset($_REQUEST["category_id"])){
                        throw new Error("Category ID not found");
                    }
                    $category = $entityManager->getRepository(Categories::class)->find($_REQUEST["category_id"]);
                    if($category == null){
                        throw new Error("Category not found");
                    }
                    $category->getProducts();
                    $productsArray = [];
                    foreach($category->getProducts() as $product){
                        $productsArray[] = $product->jsonSerialize();
                    }
                    echo json_encode($productsArray);
                    break;
                
                default:
                    throw new Error("Action not found");
                    break;
                }
                break;
        case "POST":
            $data = json_decode(file_get_contents("php://input"), true);
            if(!isset($data["product_name"]) || !isset($data["brand_id"]) || !isset($data["category_id"]) || !isset($data["model_year"]) || !isset($data["list_price"])){
                throw new Error("Some data is missing, check your : product_name, brand_id, category_id, model_year, list_price");
                break;
            }

            //Créer le produit
            $product = new Products();
            //Met le nom du produit
            $product->setProductName($data["product_name"]);

            //Met le brand
            $brand = $entityManager->getRepository(Brands::class)->find($data["brand_id"]);
            if($brand == null){
                throw new Error("Brand not found, change the brand_id");
            }
            $product->setBrand($brand);

            //Met la category
            $category = $entityManager->getRepository(Categories::class)->find($data["category_id"]);
            if($category == null){
                throw new Error("Category not found, change the category_id");
            }
            $product->setCategory($category);

            //Met l'année de création
            $product->setModelYear($data["model_year"]);

            //Met le prix
            $product->setListPrice($data["list_price"]);
            $entityManager->persist($product);
            $entityManager->flush();
            $res = Array("state" => "success", "product" => $product->jsonSerialize());
            echo json_encode($res);
            break;
        case "DELETE":
            $productRepo = $entityManager->getRepository(Products::class);
            if(!isset($_REQUEST["id"])){
                throw new Error("ID not found");
            }
            $product = $productRepo->find($_REQUEST["id"]);
            if($product == null){
                throw new Error("Category not found");
            }
            $entityManager->remove($product);
            $entityManager->flush();
            $res = Array("state" => "success");
            echo json_encode($res);
            break;
        case "PUT":
            $data = json_decode(file_get_contents("php://input"), true);
            if(!isset($_REQUEST["id"])){
                throw new Error("The ID is not found");
                break;
            }

            $productRepo = $entityManager->getRepository(Products::class);
            $product = $productRepo->find($data["id"]);
            if($product == null){
                throw new Error("Product not found");
            }

            $nbOfChange = 0;
            if(isset($data["product_name"])){
                $product->setProductName($data["product_name"]);
                $nbOfChange++;
            }

            if(isset($data["brand_id"])){
                $brand = $entityManager->getRepository(Brands::class)->find($data["brand_id"]);
                if($brand == null){
                    throw new Error("Brand not found, change the brand_id");
                }
                $product->setBrand($brand);
                $nbOfChange++;
            }

            if(isset($data["category_id"])){
                $category = $entityManager->getRepository(Categories::class)->find($data["category_id"]);
                if($category == null){
                    throw new Error("Category not found, change the category_id");
                }
                $product->setCategory($category);
                $nbOfChange++;
            }

            if(isset($data["model_year"])){
                $product->setModelYear($data["model_year"]);
                $nbOfChange++;
            }

            if(isset($data["list_price"])){
                $product->setListPrice($data["list_price"]);
                $nbOfChange++;
            }

            $entityManager->flush();
            $res = Array("state" => "success", "product" => $product->jsonSerialize(), "changes" => $nbOfChange . " changes");
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