<?php
// auth.php
// 
// Include this php file to set up the Google OAuth token

function initSession() { 
	// Create all of tokens and whoziwhatsits that allow us
	// to talk with Google (and any others, later). 
	//
	// Uses $Developer and $Application from secrets.php

	if ( $DEBUG ) error_log("Doing session_start"); 
	session_start(); 
	
	if ( $DEBUG ) error_log("Beginning Google_Client()");
	global $GoogleClient = new Google_Client();
	$GoogleClient->setApplicationName($Application['GoogleAppName']); 
	$GoogleClient->setClientId($Application['GoogleClientId']); 
	$GoogleClient->setClientSecret($Application['GoogleClientSecret']);
	$GoogleClient->setRedirectUri($Application['GoogleRedirectURI']); 
	$GoogleClient->setDeveloperKey($Developer['GoogleDeveloperKey']); 
	
	// Sadly, right now, $scopes is going to be global. 
	$GoogleClient->setScopes($GoogleScopes);

	if ( $DEBUG ) error_log("Creaating Google_DriveService()");
	$drive = new Google_DriveService($GoogleClient);  
	
	// The 'code' is the auth from Google that we'll verify with Google
	if (isset($_GET['code'])) {
	   if ( $DEBUG ) error_log("Beginning authentication"); 

		// Sometimes, it takes a long time. I want to be able to debug it quickly.
	   $mtime = microtime(); 
	   $mtime = explode(" ",$mtime); 
	   $mtime = $mtime[1] + $mtime[0]; 
	   $starttime = $mtime; 
	   $GoogleClient->authenticate(); 
	 	$mtime = microtime(); 
	   $mtime = explode(" ",$mtime); 
	   $mtime = $mtime[1] + $mtime[0]; 
	   $endtime = $mtime; 
	   $totaltime = ($endtime - $starttime); 
	
	   if ( $DEBUG ) error_log("Authentication Complete (after $totaltime seconds). Redirecting."); 

	   $_SESSION['token'] = $GoogleClient->getAccessToken();

	   $redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
	   header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
	}
} // end initSession() 

