<?php
/**
 * @package Bay Nordic Youth Registration
 * @version 0.5
 */
/*
Plugin Name: Bay Nordic Youth Registration
Plugin URI: http://panmanphil.wordpress.org
Description: Get these kids out skiing by joining Bay Nordic's Youth program. 
Author: Philip Nelson
Author URI: http://panmanphil.wordpress.org
Version: .5
License: MIT
*/

//[bnyouthform]
function bnyouthform_func( $atts ){
	// Create a stream
	$opts = array(
	  'http'=>array(
		'method'=>"GET",
		'header'=>"Accept-language: en\r\n" .
				  "Content-Type: application/hal+json\r\n" .
				  "ApiKey: a36ea4a0-c2cf-4556-a5e7-29fb9088e7f0\r\n"
	  )
	);
	
	

	$context = stream_context_create($opts);

	// Open the file using the HTTP headers set above
	$id = '1e12acf5-bb68-490a-9b13-a1b9b3b78b09';
	$host = "http://bnapi.local";
	$file = file_get_contents("$host/api", false, $context);
	$root = json_decode($file, true);
	$links = $root['_links'];
	$registerLink = $links['bn:registration'];
	if (isset($registerLink)) {
		$registerLink = $registerLink['href'];
		$registerLink = str_replace('{id}', '', $registerLink);
	}
	
	if (isset($_POST['bnSubmit'])) {
		$opts = array(
			'http' => array(
				'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
				'method'  => 'POST',
				'content' => http_build_query($_POST),
			),
		);

		$context  = stream_context_create($opts);
		$result = file_get_contents("$host/$registerLink", false, $context);

		$vars = var_dump($result);
		return $vars . '<h2>Registration Successful</h2>';
	}
	$formsLink = $links["bn:registration-form"]["href"];
	$file = file_get_contents("$host/$formsLink", false, $context);
	$registrations = json_decode($file);
	$output = "<div class='registration'>";
	
	$output .= $file;
	$output .= "</div>";
	return $output;
}
add_shortcode( 'bnyouthform', 'bnyouthform_func' );