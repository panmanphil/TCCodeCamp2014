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
	$host = "http://10.0.2.2";
	$file = file_get_contents("$host/api", false, $context);
	$root = json_decode($file, true);
	$links = $root['_links'];
	
	
	if (isset($_POST['bnSubmit'])) {
		if (isset($links['bn:register'])) {
			$registerLink = $links['bn:register'];
			$link = $registerLink['href'];
			$link = str_replace('{id}', '', $link);
		
			$opts = array(
				'http' => array(
					'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
					'method'  => 'POST',
					'content' => http_build_query($_POST),
				),
			);

			$context  = stream_context_create($opts);
			$result = file_get_contents("$host/$link", false, $context);

			//$vars = var_dump($result);
			return '<h2>Registration Successful</h2>';
		} else {
			$output = '<h2>Registration is temporarily not available</h2>';
			return $output;
		}
	}
	if (isset($links["bn:registration-form"])) {
		$formsLink = $links["bn:registration-form"];
		$link = $formsLink['href'];
		$file = file_get_contents("$host/$link", false, $context);
		global $post;
		$action = get_permalink( $post->ID );
		$file = str_replace('FORM_ACTION', $action, $file);
		$output = "<div class='registration'>";
		
		$output .= $file;
		$output .= "</div>";
		return $output;
	}
	if (isset($links["bn:closed"])) {
		$closedLink = $links["bn:closed"];
		$link = $closedLink['href'];
		$file = file_get_contents("$host/$link", false, $context);
		$output = "<div class='closed'>";
		
		$output .= $file;
		$output .= "</div>";
		return $output;
	}
}
add_shortcode( 'bnyouthform', 'bnyouthform_func' );