<?php
/**
 * Stocks API Endpoint
 *
 * This file handles API requests for the Stocks entity.
 * Supported HTTP methods: GET, POST, PUT, DELETE.
 *
 * - GET:    Retrieve all stocks, a specific stock by ID, stocks by store, or paginated products with stocks.
 * - POST:   Create a new stock (requires API key).
 * - PUT:    Update an existing stock (requires API key).
 * - DELETE: Delete a stock by ID (requires API key).
 *
 * API key is required for POST, PUT, and DELETE requests.
 *
 * Headers:
 *   - Api: Your API key (required for POST, PUT, DELETE)
 *   - Content-Type: application/json
 *
 * Query Parameters:
 *   - action: (getAll|get|getFromStore|paginate) [GET]
 *   - id: Stock ID [GET, DELETE, PUT]
 *   - store_id: Store ID [GET, action=getFromStore]
 *   - page: Page number for pagination [GET, action=paginate]
 *   - limit: Number of items per page [GET, action=paginate]
 *
 * Request Body (JSON):
 *   - store_id: int [POST, PUT] - ID of the store
 *   - product_id: int [POST, PUT] - ID of the product
 *   - quantity: int [POST, PUT] - Quantity of the product in stock
 *   - id: int [PUT] - ID of the stock to update
 *
 * Responses:
 *   - 200: Success, returns JSON data
 *   - 401: Unauthorized (invalid API key)
 *   - 400: Bad request (missing parameters)
 *   - 404: Not found (stock, store, or product not found)
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
    use Entity\Stocks;
    use Entity\Products;
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

        // GET: Retrieve stocks with various filters
        case "GET":
            $stockRepo = $entityManager->getRepository(Stocks::class);

            // If action isn't set or is getAll, return all stocks
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
                // GET: Retrieve a stock by ID
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
                
                // GET: Retrieve stocks by store ID
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

                // GET: Paginated list of products with their stocks
                case "paginate":
                    if(!isset($_REQUEST["page"]) || !isset($_REQUEST["limit"])){
                        throw new Error("Page or limit not found");
                    }
                    
                    $page = intval($_REQUEST["page"]);
                    $limit = intval($_REQUEST["limit"]);
                    
                    // Validate parameters
                    if ($page < 1 || $limit < 1) {
                        throw new Error("Invalid page or limit values");
                    }
                    
                    // Calculate offset
                    $offset = ($page - 1) * $limit;
                    
                    // Get paginated products
                    $productRepo = $entityManager->getRepository(Entity\Products::class);
                    $products = $productRepo->createQueryBuilder('p')
                        ->orderBy('p.product_id', 'ASC')
                        ->setFirstResult($offset)
                        ->setMaxResults($limit)
                        ->getQuery()
                        ->getResult();
                    
                    // Get total products count for pagination metadata
                    $totalProducts = $productRepo->createQueryBuilder('p')
                        ->select('COUNT(p.product_id)')
                        ->getQuery()
                        ->getSingleScalarResult();
                    
                    $totalPages = ceil($totalProducts / $limit);
                    
                    // Prepare result array
                    $productsWithStocks = [];
                    
                    // For each product, get its stocks in all stores
                    foreach ($products as $product) {
                        $productData = $product->jsonSerialize();
                        
                        // Get all stocks for this product
                        $stocks = $stockRepo->createQueryBuilder('s')
                            ->where('s.product = :product')
                            ->setParameter('product', $product)
                            ->getQuery()
                            ->getResult();
                        
                        // Format stock data by store
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
                        
                        // Add stocks to product
                        $productData['stocks'] = $stocksByStore;
                        
                        // Add product to results
                        $productsWithStocks[] = $productData;
                    }
                    
                    // Include pagination metadata in response
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

        // POST: Create a new stock
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

        // DELETE: Delete a stock by ID
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

        // PUT: Update an existing stock
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