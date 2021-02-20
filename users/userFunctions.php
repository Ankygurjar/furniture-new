<?php

require_once('../DB.php');
include "../inc/functions.php";


if(session_status() == 1){

    session_start();

}

if(!(isset($_SESSION['user_id'])) && !(isset($_SESSION['adminId']))){
    header("Location: ../users_login.php");

}

class UserFunctions{

    private $CUR_USER_ID ;

    function getProduct($proId){

        $productQuery = "select * from products where productId = '$proId'";
        $product = $this->getConnection()->query($productQuery);
        return $product;

    }

    function __construct(){

        if(isset($_SESSION['user_id'])){
            $this->CUR_USER_ID = $_SESSION['user_id'];
        }

    }

    function getUserId(){
        return $this->CUR_USER_ID;
    }

    function viewAllStores(){

        $getAllStoresQuery = "select * from shopifybd";
        $getAllStores = $this->getConnection()->query($getAllStoresQuery);
        return $getAllStores;

    }

    function getCurStoreData($storeUrl){

        $getStoreDataQuery = "select store_url, access_token from shopifybd where store_url = '$storeUrl'";
        $getStoreData = $this->getConnection()->query($getStoreDataQuery);
        return $getStoreData;

    }

    function getConnection(){
        $db = new DB('PRODUCTS');
        return $db;
    }

    function productDeatails($pro_id){

        $get_product_details_query = "select * from products where productId = $pro_id";
        $get_product_details = $this->getConnection()->query($get_product_details_query);
        return $get_product_details;

    }

    function insertIntoWatchList($pro_id){

        $user_id = $this->getUserId();
        $product_details = $this->productDeatails($pro_id);

        $productId = $product_details[0]['productId'];
        $categoryId = $product_details[0]['categoryId'];
        $productCode = $product_details[0]['productCode'];
        $productName = $product_details[0]['productName'];
        $productStatus = $product_details[0]['productStatus'];
        $productImage = $product_details[0]['productImage'];
        $shippingDate = $product_details[0]['shippingDate'];
        $productColor = $product_details[0]['productColor'];
        $productSize = $product_details[0]['productSize'];
        $poroductPrice = $product_details[0]['productPrice'];
        $Stock = $product_details[0]['Stock'];
        $Description = $product_details[0]['Description'];
        $cat_title = $product_details[0]['cat_title'];
        $mat_id = $product_details[0]['mat_id'];
        $vendor_id = $product_details[0]['vendor_id'];
        $variant_inventory_tracker = $product_details[0]['variant_inventory_tracker'];
        $variant_inventory_policy = $product_details[0]['variant_inventory_policy'];
        $available = $product_details[0]['available'];

        $insert_watchlist_query = "insert into watchlist(user_id, productId,categoryId, productCode, productName, productStatus, productImage, shippingDate, productColor, productSize, productPrice, Stock, Description, cat_title, mat_id, vendor_id, variant_inventory_tracker, variant_inventory_policy, available)
            values('$user_id', '$productId','$categoryId', '$productCode', '$productName', '$productStatus', '$productImage', '$shippingDate', '$productColor', '$productSize', '$poroductPrice', '$Stock', '$Description','$cat_title', '$mat_id', '$vendor_id', '$variant_inventory_tracker', '$variant_inventory_policy', '$available')";

        print_r($insert_watchlist_query);

        $this->getConnection()->query($insert_watchlist_query);
        header("Location: ./importproduct.php");

    }

    function getWatchListProducts(){

        $user_id = $this->getUserId();

        $get_user_watchlist_query = "select * from watchlist where user_id = '$user_id'";
        $get_user_watchlist = $this->getConnection()->query($get_user_watchlist_query);
        return $get_user_watchlist;

    }

    function deleteWatchList($watchListId){

        $delete_watchlist_query = "delete from watchlist where watchListId = '$watchListId'";
        $this->getConnection()->query($delete_watchlist_query);

    }

    function isProductStore($proId, $userId){

        $getProductQuery = "select * from productlist where product_id ='$proId' and user_id = '$userId'";
        $getProduct = $this->getConnection()->query($getProductQuery);
        return sizeOf($getProduct);

    }

    function isStore($userId){

        $isStoreQuery = "select storeName from storeRetailer where user_id = '$userId'";
        $isStore = $this->getConnection()->query($isStoreQuery);

        if(isset($isStore[0]['storeName']) && $isStore[0]['storeName'] != ""){

            return $isStore;

        }else{
            return "false";
        }

    }

    function isStoreExist($storeName){

        $userId = $this->getUserId();
        $storeExistsQuery = "select * from storeRetailer where storeName = '$storeName'";
        $storeExists = $this->getConnection()->query($storeExistsQuery);
        if(sizeof($storeExists) == 0){

            $assignStoreRetailerQuery = "insert into storeRetailer(user_id, storeName) values('$userId','$storeName')";
            $this->getConnection()->query($assignStoreRetailerQuery);
            header("Refresh:0");

        }
        return True;

    }

    function addToStore($product_name, $desc, $productImage, $encodedImage,$productImages, $productColor, $newPrice, $watchListId, $product_id, $profit, $stock, $collectionId,$add_tags){

        $user_id = $this->getUserId();

        $userProducts = $this->isProductStore($product_id, $user_id);
        if($userProducts === 0){
            $insertQuery = "insert into productlist(product_id, user_id, productName, productImage, productColor, productPrice, Description, profit, stock) 
                            values('$product_id', '$user_id', '$product_name', '$productImage', '$productColor', '$newPrice', '$desc', '$profit', '$stock')";


            $this->getConnection()->query($insertQuery);

            $this->addToShopify($this->getConnection()->lastInsertId(), $collectionId, $add_tags, $productImage, $encodedImage, $productImages);

            $this->deleteWatchList($watchListId);

            return true;

        }else{

            return False;

        }


    }

    function getStoreData(){

        $user_id = $this->getUserId();

        $get_storeData_query = "select * from productlist where user_id = $user_id";
        $get_storeData = $this->getConnection()->query($get_storeData_query);

        return $get_storeData;

    }

    function deleteFromStore($prod_id){

        $user_id = $this->getUserId();
        $deleteQuery = "delete from productlist where user_id ='$user_id' and productId='$prod_id'";
        $this->getConnection()->query($deleteQuery);

    }

    function getMarkup($storeName){

        $getMarkupQuery = "select user_id from storeRetailer where storeName = '$storeName.myshopify.com'";
        $userId = $this->getConnection()->query($getMarkupQuery)[0]['user_id'];
        $getGroupQuery = "select user_group_id from retailers where user_id = '$userId'";
        $getGroup = $this->getConnection()->query($getGroupQuery)[0]['user_group_id'];
        $getCommissionQuery = "select commission from groups where group_category = '$getGroup'";
        $getCommission = $this->getConnection()->query($getCommissionQuery)[0]['commission'];
        return $getCommission;

    }

    function fetchProducts($product_id){

        $query = "select * from productlist  WHERE productId = '$product_id'";
        $rows = $this->getConnection()->query($query);

        return $rows;

    }

    function createProductsShopify($data, $cost, $collectionId, $add_tags, $productImage, $encodedImage,$productImages) {


        $curStoreData = $this->getCurStoreUser();
        //print_r($curStoreData);
        //print_r($data);
        $storeUrl = $curStoreData[0]['storeName'];
        $api_key = "43c249091a227bea5ca0733355a3b05d";
        $storeData = $this->getCurStoreData($storeUrl);
        $storeTokken = $storeData[0]['access_token'];

        $product = [];

        $product['product'] = [
            'title'=> $data['productName'],
            'body_html'=>$data['Description'],
            'vendor'=>'clickrippleappfurniture',
            'product_type'=>$data['cat_title'],
            'tags' =>$add_tags ,
            'published'=>1,
            'available' => $data['available'],
            'collection_id' => $collectionId,
            'variants'=> [
                [
                    'cost' => $data['productPrice']- $data['profit'],
                    'price'=> $data['productPrice'] ,
                    'available' => $data['available'],
                    'fulfillment_service' => $data['inventory_management'],
                    'inventory_policy ' =>'continue',
                    'tracked' => 'true',
                    'inventoryManagement' =>'Clickripplefurniture',
                    'inventory_quantity' => $data['Stock'],
                    'sku' =>$data['Stock']
                ]
            ],
            'image'=>  [
                'attachment' => $encodedImage,
                'filename' => $productImage
            ],

        ];

        $url = "https://".$storeUrl."/admin/api/2021-01/products.json";

        //$url = "https://" . $api_key . ":" .$storeTokken . "@" . $storeUrl ."/admin/products.json";

        $curl = curl_init($url);
        //curl_setopt($curl, CURLOPT_HEADER, TRUE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($curl, CURLOPT_MAXREDIRS, 3);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        // curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 3);
        // curl_setopt($curl, CURLOPT_SSLVERSION, 3);
        curl_setopt($curl, CURLOPT_USERAGENT, 'My New Shopify App v.1');
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($product));

        $request_headers[] = "X-Shopify-Access-Token: " . $storeTokken;
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("X-Shopify-Access-Token: " . $storeTokken, "content-type:" . "application/json"));

        $response = curl_exec($curl);
        $error_number = curl_errno($curl);
        $error_message = curl_error($curl);
        // Close cURL to be nice
        curl_close($curl);



        // Return an error is cURL has a problem
        if ($error_number) {
            return $error_message;
        } else {

            // No error, return Shopify's response by parsing out the body and the headers
            $response = preg_split("/\r\n\r\n|\n\n|\r\r/", $response, 2);

            // Convert headers into an array
            $headers = array();
            $header_data = explode("\n",$response[0]);
            $headers['status'] = $header_data[0]; // Does not contain a key, have to explicitly set
            array_shift($header_data); // Remove status, we've already set it above
            foreach($header_data as $part) {
                $h = explode(":", $part);
                $headers[trim($h[0])] = trim($h[1]);
            }

            // Return headers and Shopify's response
            //$products = $jsonFormat['products'];
            //print_r($products);

            $this->addShopifyIdCost($response, $cost);
            $this->addProductCollection($collectionId, $response[0]);
            $this->addProductImageToShopify($response[0],$productImage, $encodedImage, $productImages);
            return array('headers' => $headers, 'response' => $response[0]);

        }
    }
    function addProductImageToShopify($response,$productImage, $encodedImage, $productImages){

        $curStoreData = $this->getCurStoreUser();
        //print_r($curStoreData);
        //print_r($data);
        $storeUrl = $curStoreData[0]['storeName'];
        $api_key = "43c249091a227bea5ca0733355a3b05d";
        $storeData = $this->getCurStoreData($storeUrl);
        $storeTokken = $storeData[0]['access_token'];

        $encodedData = json_decode($response,true);
        //print_r($encodedData['product']['id']);
        $productId = $encodedData['product']['id'];

        $productImg = [];
        $productImg['image'] = [
                'position' => 1,
                'attachment' => $encodedImage,
                'filename' => $productImage
        ];
//        $productImg['image'] = [
//            'src' => 'https://st3.depositphotos.com/2673929/13031/i/950/depositphotos_130310048-stock-photo-side-view-of-a-bedroom.jpg'
//        ];

//        echo $encodedImage;
//        die();
        $url = "https://".$storeUrl."/admin/api/2021-01/products/".$productId."/images.json";

        $curl = curl_init($url);
        //curl_setopt($curl, CURLOPT_HEADER, TRUE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($curl, CURLOPT_MAXREDIRS, 3);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        // curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 3);
        // curl_setopt($curl, CURLOPT_SSLVERSION, 3);
        curl_setopt($curl, CURLOPT_USERAGENT, 'My New Shopify App v.1');
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($productImg));

        $request_headers[] = "X-Shopify-Access-Token: " . $storeTokken;
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("X-Shopify-Access-Token: " . $storeTokken, "content-type:" . "application/json"));

        $response = curl_exec($curl);
        $error_number = curl_errno($curl);
        $error_message = curl_error($curl);
        // Close cURL to be nice
        curl_close($curl);



        // Return an error is cURL has a problem
        if ($error_number) {
            return $error_message;
        } else {

            // No error, return Shopify's response by parsing out the body and the headers
            $response = preg_split("/\r\n\r\n|\n\n|\r\r/", $response, 2);

            // Convert headers into an array
            $headers = array();
            $header_data = explode("\n",$response[0]);
            $headers['status'] = $header_data[0]; // Does not contain a key, have to explicitly set
            array_shift($header_data); // Remove status, we've already set it above
            foreach($header_data as $part) {
                $h = explode(":", $part);
                $headers[trim($h[0])] = trim($h[1]);
            }

            // Return headers and Shopify's response
            //$products = $jsonFormat['products'];
            //print_r($products);
            $this->addProductImagesToShopify($productId, $productImages);
            return array('headers' => $headers, 'response' => $response[0]);

        }
    }
    function addProductImagesToShopify($productId, $productImages){
        $curStoreData = $this->getCurStoreUser();
        //print_r($curStoreData);
        //print_r($data);
        $storeUrl = $curStoreData[0]['storeName'];
        $api_key = "43c249091a227bea5ca0733355a3b05d";
        $storeData = $this->getCurStoreData($storeUrl);
        $storeTokken = $storeData[0]['access_token'];

        if($productImages != ""){
            $productImagesArray = unserialize($productImages);
            foreach ($productImagesArray as $proImg) {

                $imageName = $proImg;
                // Get the image and convert into string
                $img = file_get_contents('images/' . $proImg);
                $encodedImg = base64_encode($img);  // Encode the image string data into base64
                //echo $productId."<br>".$imageName."<br>".$encodedImg;
                //die();
                $productImg2 = [];
                $productImg2['image'] = [
                    'attachment' => $encodedImg,
                    'filename' => $imageName
                ];

                $url = "https://".$storeUrl."/admin/api/2021-01/products/".$productId."/images.json";

                $curl = curl_init($url);
                //curl_setopt($curl, CURLOPT_HEADER, TRUE);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
                curl_setopt($curl, CURLOPT_MAXREDIRS, 3);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
                // curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 3);
                // curl_setopt($curl, CURLOPT_SSLVERSION, 3);
                curl_setopt($curl, CURLOPT_USERAGENT, 'My New Shopify App v.1');
                curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
                curl_setopt($curl, CURLOPT_TIMEOUT, 30);
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($productImg2));

                $request_headers[] = "X-Shopify-Access-Token: " . $storeTokken;
                curl_setopt($curl, CURLOPT_HTTPHEADER, array("X-Shopify-Access-Token: " . $storeTokken, "content-type:" . "application/json"));

                $response = curl_exec($curl);
                $error_number = curl_errno($curl);
                $error_message = curl_error($curl);
                // Close cURL to be nice
                curl_close($curl);



                // Return an error is cURL has a problem
                if ($error_number) {
                    return $error_message;
                } else {

                    // No error, return Shopify's response by parsing out the body and the headers
                    $response = preg_split("/\r\n\r\n|\n\n|\r\r/", $response, 2);

                    // Convert headers into an array
                    $headers = array();
                    $header_data = explode("\n",$response[0]);
                    $headers['status'] = $header_data[0]; // Does not contain a key, have to explicitly set
                    array_shift($header_data); // Remove status, we've already set it above
                    foreach($header_data as $part) {
                        $h = explode(":", $part);
                        $headers[trim($h[0])] = trim($h[1]);
                    }

                }

            }
            //return array('headers' => $headers, 'response' => $response[0]);
            return true;
        }
    }
    function addProductCollection($collectionId, $product)
    {

        $productData = json_decode($product, true);
        $productId = $productData['product']['id'];

        $curStoreData = $this->getCurStoreUser();
        $storeUrl = $curStoreData[0]['storeName'];
        $api_key = "43c249091a227bea5ca0733355a3b05d";
        $storeData = $this->getCurStoreData($storeUrl);
        $storeTokken = $storeData[0]['access_token'];

        foreach ($collectionId as $collId) {  //to save multiple collections in this function, added this loop only

            $collect = [];

            $collect['collect'] = [

                "product_id" => $productId,
                "collection_id" => $collId

            ];

            $url = "https://" . $storeUrl . "/admin/api/2021-01/collects.json";

            //$url = "https://" . $api_key . ":" .$storeTokken . "@" . $storeUrl ."/admin/products.json";

            $curl = curl_init($url);
            //curl_setopt($curl, CURLOPT_HEADER, TRUE);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
            curl_setopt($curl, CURLOPT_MAXREDIRS, 3);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
            // curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 3);
            // curl_setopt($curl, CURLOPT_SSLVERSION, 3);
            curl_setopt($curl, CURLOPT_USERAGENT, 'My New Shopify App v.1');
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
            curl_setopt($curl, CURLOPT_TIMEOUT, 30);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($collect));

            $request_headers[] = "X-Shopify-Access-Token: " . $storeTokken;
            curl_setopt($curl, CURLOPT_HTTPHEADER, array("X-Shopify-Access-Token: " . $storeTokken, "content-type:" . "application/json"));

            $response = curl_exec($curl);
            $error_number = curl_errno($curl);
            $error_message = curl_error($curl);
            // Close cURL to be nice
            curl_close($curl);


            // Return an error is cURL has a problem
            if ($error_number) {
                return $error_message;
            } else {

                // No error, return Shopify's response by parsing out the body and the headers
                $response = preg_split("/\r\n\r\n|\n\n|\r\r/", $response, 2);

                // Convert headers into an array
                $headers = array();
                $header_data = explode("\n", $response[0]);
                $headers['status'] = $header_data[0]; // Does not contain a key, have to explicitly set
                array_shift($header_data); // Remove status, we've already set it above
                foreach ($header_data as $part) {
                    $h = explode(":", $part);
                    $headers[trim($h[0])] = trim($h[1]);
                }


            }
        }
    }

    function getCurStoreUser(){

        $curUser = $this->getUserId();
        $getCurUserDataQuery = "select * from storeRetailer where user_id = '$curUser'";
        $getAllStores = $this->getConnection()->query($getCurUserDataQuery);
        return $getAllStores;

    }

    function addToShopify($productList_id, $collectionId, $add_tags, $productImage, $encodedImage,$productImages){

        $this->processProductsMigration($productList_id, $collectionId, $add_tags, $productImage, $encodedImage,$productImages);

    }

    function processProductsMigration($productList_id, $collectionId, $add_tags, $productImage, $encodedImage,$productImages){

        $results = array();
        $results['start-time'] = date('h:i:s');
        $products = $this->fetchProducts($productList_id);
        //foreach($products as $product){
        $data = array();
        $data['productId'] = $products[0]['productId'];
        $data['cat_title']= $products[0]['categoryId'];
        $data['productCode'] = $products[0]['productCode'];
        $data['productName'] = $products[0]['productName'];
        $data['productStatus'] = $products[0]['productStatus'];
        $data['productImage'] = $products[0]['productImage'];
        $data['shippingDate'] = $products[0]['shippingDate'];
        $data['productColor'] = $products[0]['productColor'];
        $data['productSize'] = $products[0]['productSize'];
        $data['productPrice'] = $products[0]['productPrice'];
        $data['profit'] = $products[0]['profit'];
        $data['Stock'] = $products[0]['Stock'];
        $data['Description'] =$products[0]['Description'];
        $data['inventory_management'] = $products[0]['variant_inventory_tracker'];
        $data['tracked'] = $products[0]['variant_inventory_policy'];
        $data['available'] = $products[0]['available'];


        //$this->createProductsShopify($data, $storeName);
        $response= $this->createProductsShopify($data, $products[0]['profit'], $collectionId, $add_tags, $productImage, $encodedImage,$productImages);

        //
        //  $orders = $json['product'];

        //print_r($json);
        //wait for half second to compensate 2 calls per second shopify bucket limit (40)
        //usleep(500000);

        //}


    }

    function addShopifyIdCost($response, $cost){

        $json = json_decode($response[0], true);

        $shopifyId = $json['product']['id'];

        $insertShopifyIdCostQuery = "insert into shopifyCostId(shopifyId, cost) values('$shopifyId', '$cost')";
        $this->getConnection()->query($insertShopifyIdCostQuery);

    }

    function getProfitForProduct($productId){

        $getProfiftQuery = "select cost from shopifyCostId where shopifyId = '$productId'";
        $getProfit = $this->getConnection()->query($getProfiftQuery);
        return $getProfit;

    }


    function getOrderEarnings($created_at_min=null,$created_at_max=null,$store=null,$orderNumber=null,$paymentType=null,$status='any'){ //fulfilled



        if($store == null){

            $storeData = $this->viewAllStores();
            $store = $storeData[0]['store_url'];
            $storeToken = $storeData[0]['access_token'];


        }else{

            $storeData = $this->getCurStoreData($store);
            $storeToken = $storeData[0]['access_token'];


        }

        //$url = "https://" . $store  . "/admin/orders/".$orderId."/cancel.json";
        $url = "https://".$store."/admin/orders.json?status={$status}";

        //$url = "https://43c249091a227bea5ca0733355a3b05d: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@clickrippleappfurniture.myshopify.com/admin/orders.json?status={$status}";
        if($created_at_min != null && $created_at_max != null)
        {
            $url .= "&created_at_min={$created_at_min}&created_at_max={$created_at_max}";
        }
        if($store != null)
        {
            $url .= "&={$store}";
        }
        if($orderNumber != null)
        {
            $url .= "&ids={$orderNumber}";
        }
        if($paymentType != null)
        {
            $url .= "&={$paymentType}";
        }

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, TRUE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($curl, CURLOPT_MAXREDIRS, 3);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        // curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 3);
        // curl_setopt($curl, CURLOPT_SSLVERSION, 3);
        curl_setopt($curl, CURLOPT_USERAGENT, 'My New Shopify App v.1');
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("X-Shopify-Access-Token: " . $storeToken));
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
        //curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($cancelProduct));
        //$request_headers[] = "X-Shopify-Access-Token: " . $storeToken;
        $response = curl_exec($curl);
        $error_number = curl_errno($curl);
        $error_message = curl_error($curl);

        // Close cURL to be nice
        curl_close($curl);

        // Return an error is cURL has a problem
        if ($error_number) {
            return $error_message;
        } else {

            // No error, return Shopify's response by parsing out the body and the headers
            $response = preg_split("/\r\n\r\n|\n\n|\r\r/", $response, 2);

            // Convert headers into an array
            $headers = array();
            $header_data = explode("\n",$response[0]);
            $headers['status'] = $header_data[0]; // Does not contain a key, have to explicitly set
            array_shift($header_data); // Remove status, we've already set it above
            foreach($header_data as $part) {
                $h = explode(":", $part);
                $headers[trim($h[0])] = trim($h[1]);
            }

            // Return headers and Shopify's response
            return array('headers' => $headers, 'response' => $response[1]);

        }
    }

    function getAllOrders($created_at_min=null,$created_at_max=null,$store=null,$orderNumber=null,$paymentType=null,$status=''){ //fulfilled



//        if($store == null){
//
//            $storeData = $this->viewAllStores();
//            $store = $storeData[0]['store_url'];
//            $storeToken = $storeData[0]['access_token'];
//
//
//        }else{
//
//            $storeData = $this->getCurStoreData($store);
//            $storeToken = $storeData[0]['access_token'];
//
//
//        }
//
//        //$url = "https://" . $store  . "/admin/orders/".$orderId."/cancel.json";
//        $url = "https://".$store."/admin/orders.json?status={$status}";

        $url = "https://43c249091a227bea5ca0733355a3b05d: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@clickrippleappfurniture.myshopify.com/admin/orders.json?status={$status}";
        if($created_at_min != null && $created_at_max != null)
        {
            $url .= "&created_at_min={$created_at_min}&created_at_max={$created_at_max}";
        }
        if($store != null)
        {
            $url .= "&={$store}";
        }
        if($orderNumber != null)
        {
            $url .= "&ids={$orderNumber}";
        }
        if($paymentType != null)
        {
            $url .= "&={$paymentType}";
        }

        $storeToken="";
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, TRUE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($curl, CURLOPT_MAXREDIRS, 3);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        // curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 3);
        // curl_setopt($curl, CURLOPT_SSLVERSION, 3);
        curl_setopt($curl, CURLOPT_USERAGENT, 'My New Shopify App v.1');
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("X-Shopify-Access-Token: " . $storeToken));
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
        //curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($cancelProduct));
        //$request_headers[] = "X-Shopify-Access-Token: " . $storeToken;
        $response = curl_exec($curl);
        $error_number = curl_errno($curl);
        $error_message = curl_error($curl);

        // Close cURL to be nice
        curl_close($curl);

        // Return an error is cURL has a problem
        if ($error_number) {
            return $error_message;
        } else {

            // No error, return Shopify's response by parsing out the body and the headers
            $response = preg_split("/\r\n\r\n|\n\n|\r\r/", $response, 2);

            // Convert headers into an array
            $headers = array();
            $header_data = explode("\n",$response[0]);
            $headers['status'] = $header_data[0]; // Does not contain a key, have to explicitly set
            array_shift($header_data); // Remove status, we've already set it above
            foreach($header_data as $part) {
                $h = explode(":", $part);
                $headers[trim($h[0])] = trim($h[1]);
            }

            // Return headers and Shopify's response
            return array('headers' => $headers, 'response' => $response[1]);

        }
    }


    function getOrders(){

        $url = "https://43c249091a227bea5ca0733355a3b05d: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@clickrippleappfurniture.myshopify.com/admin/orders.json?status=any";

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_VERBOSE, 0);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $response['data'] = curl_exec ($curl);
        if(curl_errno($curl))
        {
            $response['error'] = 'Curl error: ' . curl_error($curl);
        }
        curl_close ($curl);
        return $response;

    }

    function getOrdersFromAllStores($created_at_min=null,$created_at_max=null,$store=null,$orderNumber=null,$paymentType=null,$status='any'){

        $url = "";
        if($created_at_min != null && $created_at_max != null)
        {
            $url .= "&created_at_min={$created_at_min}&created_at_max={$created_at_max}";
        }
        if($store != null)
        {
            $url .= "&={$store}";
        }
        if($orderNumber != null)
        {
            $url .= "&ids={$orderNumber}";
        }
        if($paymentType != null)
        {
            $url .= "&={$paymentType}";
        }

        $allStores = $this->viewAllStores();
        $allStoreData = [];
        foreach($allStores as $store){

            $storeData = $this->getOrdersFromStore($url, $store['store_url']);
            array_push($allStoreData, $storeData);

        }

        return $allStoreData;

    }

    function getProBoxes($id){

        $getProBoxesQuery = "select * from product_boxes where productId = '$id'";
        $getProBoxes = $this->getConnection()->query($getProBoxesQuery);
        return $getProBoxes;

    }

    function getOrdersFromStore($url, $store){

        $curStoreData = $this->getCurStoreData($store);
        $storeUrl = $curStoreData[0]['store_url'];
        $storeTokken = $curStoreData[0]['access_token'];

        $orders = shopify_call($storeTokken, $store, "/admin/api/2021-01/orders.json?".$url, array(), "GET");
        return $orders['response'];

    }

    function getOrderProductsIds(){

        $data = $this->getFullOrders();

        $json = json_decode($data['response'], 1);

        $orders = $json['orders'];

        foreach($orders as $order){

            $orderProductIds[] = $order['line_items'][0]['product_id'];

        }

        return $orderProductIds;

    }

    function getProducts(){

        $curUserStore = $this->getCurStoreUser()[0]['storeName'];
        $storeTokken = $this->getCurStoreData($curUserStore)[0]['access_token'];

        $url = "https://".$curUserStore."/admin/products.json";

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, TRUE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($curl, CURLOPT_MAXREDIRS, 3);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        // curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 3);
        // curl_setopt($curl, CURLOPT_SSLVERSION, 3);
        curl_setopt($curl, CURLOPT_USERAGENT, 'My New Shopify App v.1');
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("X-Shopify-Access-Token: " . $storeTokken));
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
        //curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($cancelProduct));
        //$request_headers[] = "X-Shopify-Access-Token: " . $storeToken;
        $response = curl_exec($curl);
        $error_number = curl_errno($curl);
        $error_message = curl_error($curl);

        // Close cURL to be nice
        curl_close($curl);

        // Return an error is cURL has a problem
        if ($error_number) {
            return $error_message;
        } else {

            // No error, return Shopify's response by parsing out the body and the headers
            $response = preg_split("/\r\n\r\n|\n\n|\r\r/", $response, 2);

            // Convert headers into an array
            $headers = array();
            $header_data = explode("\n",$response[0]);
            $headers['status'] = $header_data[0]; // Does not contain a key, have to explicitly set
            array_shift($header_data); // Remove status, we've already set it above
            foreach($header_data as $part) {
                $h = explode(":", $part);
                $headers[trim($h[0])] = trim($h[1]);
            }

            // Return headers and Shopify's response
            return array('headers' => $headers, 'response' => $response[1]);

        }
    }

    function getInventoryIdOfProduct($productId){

        $allProducts = $this->getProducts();

        $jsonFormat = json_decode($allProducts['response'], 1);

        $products = $jsonFormat['products'];


        foreach($products as $product){

            if($product['id'] == $productId){

                return $product['variants'][0]['inventory_item_id'];

            }

        }
        return false;


    }

    function getInventoryIds(){


        $allProducts = $this->getProducts();

        $jsonFormat = json_decode($allProducts['response'], 1);

        $products = $jsonFormat['products'];
        $orderedProducts = $this->getOrderProductsIds();

        //print_r($products);
        print_r($orderedProducts);
        //print_r($products[0]['variants'][0]['inventory_item_id']);

        for($i = 0; $i < sizeof($orderedProducts); $i++){

            //print($products[$i]['id'].",");
            //print($orderedProducts[$i]."<br>");

        }

        //print_r($inventoryIds);

    }

    function getCollections(){

        $curUserStore = $this->getCurStoreUser()[0]['storeName'];
        $storeTokken = $this->getCurStoreData($curUserStore)[0]['access_token'];

        $url = "https://".$curUserStore."/admin/custom_collections.json";
        $curl = curl_init($url);
        //curl_setopt($curl, CURLOPT_HEADER, TRUE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($curl, CURLOPT_MAXREDIRS, 3);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        // curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 3);
        // curl_setopt($curl, CURLOPT_SSLVERSION, 3);
        curl_setopt($curl, CURLOPT_USERAGENT, 'My New Shopify App v.1');
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("X-Shopify-Access-Token: " . $storeTokken));
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
        //curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($cancelProduct));
        //$request_headers[] = "X-Shopify-Access-Token: " . $storeToken;
        $response = curl_exec($curl);
        $error_number = curl_errno($curl);
        $error_message = curl_error($curl);

        // Close cURL to be nice
        curl_close($curl);

        // Return an error is cURL has a problem
        if ($error_number) {
            return $error_message;
        } else {

            // No error, return Shopify's response by parsing out the body and the headers
            $response = preg_split("/\r\n\r\n|\n\n|\r\r/", $response, 2);

            // Convert headers into an array
            $headers = array();
            $header_data = explode("\n",$response[0]);
            $headers['status'] = $header_data[0]; // Does not contain a key, have to explicitly set
            array_shift($header_data); // Remove status, we've already set it above
            foreach($header_data as $part) {
                $h = explode(":", $part);
                $headers[trim($h[0])] = trim($h[1]);
            }

            // Return headers and Shopify's response
            return $response;
        }

    }

    function getFullOrders(){

        $curUserStore = $this->getCurStoreUser()[0]['storeName'];
        $storeTokken = $this->getCurStoreData($curUserStore)[0]['access_token'];
        //$api_key = "43c249091a227bea5ca0733355a3b05d";

        $url = "https://".$curUserStore."/admin/orders.json";

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, TRUE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($curl, CURLOPT_MAXREDIRS, 3);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        // curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 3);
        // curl_setopt($curl, CURLOPT_SSLVERSION, 3);
        curl_setopt($curl, CURLOPT_USERAGENT, 'My New Shopify App v.1');
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("X-Shopify-Access-Token: " . $storeTokken));
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
        //curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($cancelProduct));
        //$request_headers[] = "X-Shopify-Access-Token: " . $storeToken;
        $response = curl_exec($curl);
        $error_number = curl_errno($curl);
        $error_message = curl_error($curl);

        // Close cURL to be nice
        curl_close($curl);

        // Return an error is cURL has a problem
        if ($error_number) {
            return $error_message;
        } else {

            // No error, return Shopify's response by parsing out the body and the headers
            $response = preg_split("/\r\n\r\n|\n\n|\r\r/", $response, 2);

            // Convert headers into an array
            $headers = array();
            $header_data = explode("\n",$response[0]);
            $headers['status'] = $header_data[0]; // Does not contain a key, have to explicitly set
            array_shift($header_data); // Remove status, we've already set it above
            foreach($header_data as $part) {
                $h = explode(":", $part);
                $headers[trim($h[0])] = trim($h[1]);
            }

            // Return headers and Shopify's response
            return array('headers' => $headers, 'response' => $response[1]);

        }
    }

    function getNumOfRetailers(){

        $getNumOfRetailerQuery = "select * from retailers";
        $getNumOfRetailer = $this->getConnection()->query($getNumOfRetailerQuery);
        $numOfRetailer = sizeof($getNumOfRetailer);
        return $numOfRetailer;

    }

    function getNumOfProducts(){

        $getNumOfProductsQuery = "select * from products";
        $getNumOfProducts = $this->getConnection()->query($getNumOfProductsQuery);
        $numOfProducts = sizeof($getNumOfProducts);
        return $numOfProducts;

    }

    function cancelOrder($orderId, $cancellationReason, $refundAmount, $store){

        $curStoreData = $this->getCurStoreData($store);
        $storeTokken = $curStoreData[0]['access_token'];


        $url = "https://" . $store  . "/admin/orders/".$orderId."/cancel.json";

        $cancelProduct = array();

        $cancelProduct= [
            "cancel_reason" => $cancellationReason,
            "amount" => $refundAmount,
            "currency" => "CAD"
        ];

        //print($url);
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, TRUE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($curl, CURLOPT_MAXREDIRS, 3);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        // curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 3);
        // curl_setopt($curl, CURLOPT_SSLVERSION, 3);
        curl_setopt($curl, CURLOPT_USERAGENT, 'My New Shopify App v.1');
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($cancelProduct));

        $request_headers[] = "";
        if (!is_null($storeTokken)) $request_headers[] = "X-Shopify-Access-Token: " . $storeTokken;
        curl_setopt($curl, CURLOPT_HTTPHEADER, $request_headers);

        $response = curl_exec($curl);
        $error_number = curl_errno($curl);
        $error_message = curl_error($curl);
        // Close cURL to be nice
        curl_close($curl);

    }

    function getStoreUrl($storeToken, $storeName){

        $apiKey = "43c249091a227bea5ca0733355a3b05d";
        $url = "https://".$apiKey.":".$storeToken."@".$storeName."/admin";
        return $url;

    }

    function deleteOrder($orderId, $store){

        $curStoreData = $this->getCurStoreData($store);
        $storeTokken = $curStoreData[0]['access_token'];
        $url = "https://" . $store  . "/admin/orders/".$orderId.".json";
        //print($url);
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, TRUE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($curl, CURLOPT_MAXREDIRS, 3);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);

        curl_setopt($curl, CURLOPT_USERAGENT, 'My New Shopify App v.1');
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");

        $request_headers[] = "";
        if (!is_null($storeTokken)) $request_headers[] = "X-Shopify-Access-Token: " . $storeTokken;
        curl_setopt($curl, CURLOPT_HTTPHEADER, $request_headers);

        $response = curl_exec($curl);
        $error_number = curl_errno($curl);
        $error_message = curl_error($curl);
        //print($response);
        curl_close($curl);

    }

    function getEarnings(){

        $allOrders = $this->getOrders();
        $json = json_decode($allOrders['data'], 1);

        $ordersStatus = $json['orders'];

        foreach ($ordersStatus as $rkey => $orderStatus){
            if ($orderStatus['fulfillment_status'] == 'fulfilled'){
                $fullOrders[] = $orderStatus['total_price'];
            }
        }

        return $fullOrders[0];

    }


    function getMargin(){

        $userId = $this->getUserId();
        $getCurUserGroupQuery = "select user_group_id from retailers where user_id = $userId";
        $getCurUserGroup = $this->getConnection()->query($getCurUserGroupQuery)[0]['user_group_id'];
        $getCurGroupMarginQuery = "select commission from groups where group_category = '$getCurUserGroup'";
        $getCurGroupMargin = $this->getConnection()->query($getCurGroupMarginQuery)[0]['commission'];

        return $getCurGroupMargin;

    }

}



?>