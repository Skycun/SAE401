<?php
    //Config
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, Api");
    header("Content-Type: application/json; charset=UTF-8");
    
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

            //If action isn't get or the action is getAll, return all the stocks of all the stocks
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
                         throw new Error("Stock not found");
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
                    // Ajoutez un nouveau case pour la pagination
                // Ajoutez un nouveau case pour la pagination
                case "paginate":
                    if(!isset($_REQUEST["page"]) || !isset($_REQUEST["limit"])){
                        throw new Error("Page or limit not found");
                    }
                    
                    $page = intval($_REQUEST["page"]);
                    $limit = intval($_REQUEST["limit"]);
                    
                    // Valider les paramètres
                    if ($page < 1 || $limit < 1) {
                        throw new Error("Invalid page or limit values");
                    }
                    
                    // Calculer l'offset
                    $offset = ($page - 1) * $limit;
                    
                    // Récupérer les produits paginés
                    $productRepo = $entityManager->getRepository(Entity\Products::class);
                    $products = $productRepo->createQueryBuilder('p')
                        ->orderBy('p.product_id', 'ASC')
                        ->setFirstResult($offset)
                        ->setMaxResults($limit)
                        ->getQuery()
                        ->getResult();
                    
                    // Obtenir le nombre total de produits pour calculer le nombre total de pages
                    $totalProducts = $productRepo->createQueryBuilder('p')
                        ->select('COUNT(p.product_id)')
                        ->getQuery()
                        ->getSingleScalarResult();
                    
                    $totalPages = ceil($totalProducts / $limit);
                    
                    // Préparer le tableau de résultats
                    $productsWithStocks = [];
                    
                    // Pour chaque produit, récupérer ses stocks dans tous les magasins
                    foreach ($products as $product) {
                        $productData = $product->jsonSerialize();
                        
                        // Récupérer tous les stocks pour ce produit
                        $stocks = $stockRepo->createQueryBuilder('s')
                            ->where('s.product = :product')
                            ->setParameter('product', $product)
                            ->getQuery()
                            ->getResult();
                        
                        // Formater les données de stock par magasin
                        $stocksByStore = [];
                        foreach ($stocks as $stock) {
                            $stocksByStore[] = [
                                'stock_id' => $stock->getStockId(),
                                'store' => [
                                    'store_id' => $stock->getStore()->getStoreId(),
                                    'store_name' => $stock->getStore()->getStoreName()
                                ],
                                'quantity' => $stock->getQuantity()
                            ];
                        }
                        
                        // Ajouter les stocks au produit
                        $productData['stocks'] = $stocksByStore;
                        
                        // Ajouter le produit au tableau de résultats
                        $productsWithStocks[] = $productData;
                    }
                    
                    // Inclure les métadonnées de pagination dans la réponse
                    $response = [
                        'data' => $productsWithStocks,
                        'pagination' => [
                            'page' => $page,
                            'limit' => $limit,
                            'totalItems' => $totalProducts,
                            'totalPages' => $totalPages
                        ]
                    ];
                    
                    echo json_encode($response);
                    break;
                default:
                    throw new Error("Action not found");
                    break;
                }
                break;
        case "POST":
            $data = json_decode(file_get_contents("php://input"), true);
            if(!isset($data["store_id"]) || !isset($data["product_id"]) || !isset($data["quantity"])){
                throw new Error("Stock ID, Store ID, Product ID or Quantity not found");
                break;
            }

            $stock = new Stocks();
            $store = $entityManager->getRepository(Stores::class)->find($data["store_id"]);
            if($store == null){
                throw new Error("Store not found");
            }
            $product = $entityManager->getRepository(Products::class)->find($data["product_id"]);
            if($product == null){
                throw new Error("Product not found");
            }
            $stock->setStore($store);
            $stock->setProduct($product);
            $stock->setQuantity($data["quantity"]);

            $entityManager->persist($stock);
            $entityManager->flush();
            $res = Array("state" => "success", "stock" => $stock->jsonSerialize());
            echo json_encode($res);
            break;
        case "DELETE":
            if(!isset($_REQUEST["id"])){
                throw new Error("ID not found");
            }
            $stockRepo = $entityManager->getRepository(Stocks::class);
            $stock = $stockRepo->find($_REQUEST["id"]);
            if($stock == null){
                throw new Error("Stock not found");
            }
            $entityManager->remove($stock);
            $entityManager->flush();
            $res = Array("state" => "success");
            echo json_encode($res);
            break;
        case "PUT":

            $data = json_decode(file_get_contents("php://input"), true);
            if(!isset($data["id"])){
                throw new Error("ID not found");
                break;
            }

            $stockRepo = $entityManager->getRepository(Stocks::class);

            $stock = $stockRepo->find($data["id"]);
            if($stock == null){
                throw new Error("stock not found");
            }

            $nbOfChange = 0;
            if(isset($data["store_id"])){
                $store = $entityManager->getRepository(Stores::class)->find($data["store_id"]);
                if($store == null){
                    throw new Error("Store not found, change the store_id");
                }
                $stock->setStore($store);
                $nbOfChange++;
            }

            if(isset($data["product_id"])){
                $product = $entityManager->getRepository(Products::class)->find($data["product_id"]);
                if($product == null){
                    throw new Error("Product not found, change the product_id");
                }
                $stock->setProduct($product);
                $nbOfChange++;
            }

            if(isset($data["quantity"])){
                $stock->setQuantity($data["quantity"]);
                $nbOfChange++;
            }

            $entityManager->persist($stock);
            $entityManager->flush();
            $res = Array("state" => "success", "stock" => $stock->jsonSerialize());
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