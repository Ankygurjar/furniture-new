<?php

require_once("inc/functions.php");
require_once("inc/connect.php");

// Set variables for our request
$api_key = "a4f7932a9baec550192e31ba154d426c";
$shared_secret = "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";
$params = $_GET; // Retrieve all request parameters
$hmac = $_GET['hmac']; // Retrieve HMAC request parameter

$shop_url = $params['shop'];

$params = array_diff_key($params, array('hmac' => '')); // Remove hmac from params
ksort($params); // Sort params lexographically

$computed_hmac = hash_hmac('sha256', http_build_query($params), $shared_secret);

// Use hmac data to check that the response is from Shopify or not
if (hash_equals($hmac, $computed_hmac)) {

	// Set variables for our request
	$query = array(
		"client_id" => $api_key, // Your API key
		"client_secret" => $shared_secret, // Your app credentials (secret key)
		"code" => $params['code'] // Grab the access key from the URL
	);

	// Generate access token URL
	$access_token_url = "https://" . $params['shop'] . "/admin/oauth/access_token";

	// Configure curl client and execute request
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_URL, $access_token_url);
	curl_setopt($ch, CURLOPT_POST, count($query));
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($query));
	$result = curl_exec($ch);
	curl_close($ch);

	// Store the access token
	$result = json_decode($result, true);
	$access_token = $result['access_token'];

	// Show the access token (don't do this in production!)
	//echo $access_token;

	$sql = "INSERT INTO shopifybd (store_url, access_token, install_date) VALUES ('". $shop_url ."', '". $access_token ."', Now())";
	if(mysqli_query($conn, $sql)){

		header('Location: https://'.$shop_url .'/admin/apps');
		exit();
		
	}else{
	echo "Error installation: " .mysqli_error($conn);
	
	
	}
} else {
	// Someone is trying to be shady!
	die('This request is NOT from Shopify!');
}