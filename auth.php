<?php
// auth.php
// 
// Include this php file to set up the Google OAuth token

function initSession($Application, $Developer, $GoogleScopes) { 
	// Create all of tokens and whoziwhatsits that allow us
	// to talk with Google (and any others, later). 
	//
	// Uses $Developer and $Application from secrets.php
	// returns $GoogleClient

	global $DEBUG; 
	
	session_start(); 
	
	$GoogleClient = new Google_Client();
	$GoogleClient->setApplicationName($Application['GoogleAppName']); 
	$GoogleClient->setClientId($Application['GoogleClientId']); 
	$GoogleClient->setClientSecret($Application['GoogleClientSecret']);
	$GoogleClient->setRedirectUri($Application['GoogleRedirectURI']); 
	$GoogleClient->setDeveloperKey($Developer['GoogleDeveloperKey']); 
	
	// Sadly, right now, $scopes is going to be global. 
	$GoogleClient->setScopes($GoogleScopes);

	$driveService = new Google_DriveService($GoogleClient);  
	
	// When Google redirects people back to our site (Using the RedirectURI), ?code=XXXXXXXXX is set, 
	// which is the user's token. We will then set $_SESSION['token'] to that value, and then when 
	// talking to Google, we'll refer to it with the $GoogleClient object.
	if (isset($_GET['code'])) {
	   if ( $DEBUG ) error_log("Beginning authentication"); 

		// Sometimes, it takes a long time. I want to be able to debug it quickly.
		// That's what the timing stuff is. 
	   $mtime = microtime(); 
	   $mtime = explode(" ",$mtime); 
	   $mtime = $mtime[1] + $mtime[0]; 
	   $starttime = $mtime; 
	   try { $GoogleClient->authenticate(); } 
	   catch (Exception $e) {
	   	 showCrit("Error trying to authenticate with GoogleClient:<br> " . $e->getMessage()); } 
	   $mtime = microtime(); 
	   $mtime = explode(" ",$mtime); 
	   $mtime = $mtime[1] + $mtime[0]; 
	   $endtime = $mtime; 
	   $totaltime = ($endtime - $starttime); 
	
	   // This is where we tie Google's 'code' in the URL to the $_SESSION['token'] variable	
	   $_SESSION['token'] = $GoogleClient->getAccessToken();

		// Now we redirect back to ourselves without the ?code=XXXX string
	   $redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
	   header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
	}
	
	// Now, once we're redirected back without ?code=XXXXXX (which means the above block
	// won't execute), we want to update the $GoogleClient object with the token (so we don't
	// have to go through everything above again)
	if (isset($_SESSION['token']) ) {
		$GoogleClient->setAccessToken($_SESSION['token']); 
	}
	
	return $GoogleClient; 
} // end initSession() 

