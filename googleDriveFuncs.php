<?php 
// googleDriveFuncs.php
//
// This file holds the functions for managing Google Docs. 
// From simple things like 'listing available docs' to 'whatever else'. 

function listFilesGDrive($drive) {
// This function will list the available files from Google Drives
   $parameters = array(); 
   $arrFiles = $drive->files->listFiles($parameters);
   $arrFileArrs = $arrFiles->items; 
   foreach ($arrFileArrs as $curFile) {
      print "Title: " . $curFile->title . " (" . $curFile->id . ")<br>";
   }   
} // end listFilesGDrive

function getGDriveContents($serivce, $file) {
// from https://developers.google.com/drive/v2/reference/files/get
// Returns the contents of a file on GDrive. Does not work if the 
// file was not created with Google Drive (as is the case with forms)
   $downloadURL = $file->getDownloadUrl(); 
   if ($downloadURL) {
      $request = new Google_HttpRequest($downloadURL, 'GET', null, null); 
      $httpRequest = Google_Client::$io->authenticatedRequest($request); 
      if ( $httprequest->getResponseHttpCode() == 200) {
         return $httpRequest->getResponseBody(); 
      } else {
         return "HTTP response code: " . $httpRequest->getResponseHttpCode(); 
      }   
   } else {
      return "Sorry, no download URL returned"; 
   }   
} // end getGDriveContents() 

function googleSSConnect() { 
// Connects to the Google Spreadsheet service, returns the service obj. 

	global $GoogleClient; 
	global $DEBUG; 
	
	if (isset($_SESSION['token'])) {
      if ( $DEBUG ) error_log("Found token.");
      $GoogleClient->setAccessToken($_SESSION['token']);

      $SESSIONobj = json_decode($_SESSION['token']);
      if ( $DEBUG ) error_log("Creating new Google Spreadsheet Request"); 

      $SSRequest = new Google\Spreadsheet\Request($SESSIONobj->access_token); 

      if ( $DEBUG ) error_log("Creating new default service request"); 
      $serviceRequest = new Google\Spreadsheet\DefaultServiceRequest($SSRequest); 
		if ( ! $serviceRequest ) throw new Exception('Unable to create new service request'); 

      if ( $DEBUG ) error_log("Setting Service Request Factory Instance"); 
      Google\Spreadsheet\ServiceRequestFactory::setInstance($serviceRequest); 

      if ( $DEBUG ) error_log("Creating new spreadsheet service"); 
      $ssService = new Google\Spreadsheet\SpreadsheetService(); 
		if ( ! $ssService ) throw new Exception('Unable to create new Spreadsheet Service'); 

		return $ssService; 
	} else { 
		throw new Exception('Token Not Set'); 
	}
} // end googleSSConnect

function getWorksheet($GoogleClient, $wbName, $wsName) { 
// Wrapper function for getWorksheets() and getWSContents()

	$worksheetFeed = getWorksheets($GoogleClient, $wbName); 
	if ( $worksheetFeed ) { 
		return getWSContents($worksheetFeed, $wsName); 
	} else {
		return 0; 
	}
} // end getWorksheet()

function getWorksheets($GoogleClient, $wbName) {
// Retrieves worksheets in $wbName (which is assumed to be 
// a text string of the name of a workbook) 
// returns the worksheet feed object. 

	global $DEBUG;
	
	// Make sure we're logged in and everything is set up correctly. 
	if ($GoogleClient->getAccessToken()) {
		try {
			$ssService = googleSSConnect(); 
			$ssFeed = $ssService->getSpreadsheets();
		} catch (Exception $e) {
			if ( $e->getCode() == "401" ) {
				showCrit("Error with token. Must relog in.");
				session_unset();
				$redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
				header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
			} else {
				// If we're not sure, punt and hopefully someone can figure it out. 
				showCrit("Error: " . $e->getMessage());
				return 0; 
			}
		}

		if ( ! $ssFeed ) {
			 showCrit('Unable to obtain spreadsheet feed');
			return 0; 
		} 
		
		// $ssFeed is an object which contains a feed of all of the spreadsheets 
		// we have access to. It's not an array, so we can't just iterate through it
		// Instead, we have to use its methods to pull the information out that we need.
		$spreadsheet = $ssFeed->getByTitle($wbName); 

		if ( ! $spreadsheet ) {
			showCrit("Error: Unable to find spreadsheet"); 
			return 0; 
		} 

		// Likewise, a spreadsheet (or workbook) consists of one or more worksheets. 
		// These worksheets also form feed, and you query them in much the same way. 
		$worksheetFeed = $spreadsheet->getWorksheets();
		if ( ! $worksheetFeed ) { 
			showCrit("Error: Unable to list worksheets");
			return 0; 
		} else {
			// In this case, everything went well (seemingly), so we'll 
			// return the $worksheetFeed object, which is a series of 
			// associative arrays. Read the official Google Spreadsheets API
			// for more info: https://developers.google.com/google-apps/spreadsheets/ 
			return $worksheetFeed; 
		}
	} else { 
		showCrit("Unable to find Google access token");
		return 0;  
	}

} // end getWorksheets() 

function getWSContents($wsFeed, $wsName) {
// Needs a worksheet feed object (which is the object containing the individual 
// worksheets in a spreadsheet or workbook) and the name of the worksheet you
// want the contents of. 
// Will return only the worksheet cells with headers, as outlined in the 
// Google Spreadsheets API v3 doc here: 
// https://developers.google.com/google-apps/spreadsheets/

	$ws = $wsFeed->getByTitle($wsName); 
	if ( ! $ws ) { 
		showCrit("Unable to find worksheet");
		return 0; 
	} 

	$listFeed = $ws->getListFeed(); 
	if ( ! $listFeed ) {
		showCrit("Unable to create feed from worksheet");	
		return 0; 
	} 

	return $listFeed->getEntries(); 

} //end getWSFeed()

function displayGoogleAuth($width=250) {
// Displayes the normal "sign in with Google" image
// defaults to width=250

	global $GoogleClient;  
	print '<center><a href="' . $GoogleClient->createAuthUrl() . '"><img src="https://developers.google.com/accounts/images/sign-in-with-google.png" border=0 width=' . $width . '></a>'; 
}


function displayRecord($record) { 
 // This function is what can be used to display a single record from a Google Spreadsheet. 
 // It needs to be crafted to each use case, but it's farcically simple to do when you
 // use Bootstrap's methods: 
 // http://getbootstrap.com/components/

	
	// By default, lets just do a print_r() to see what the record contains. 
	print "<pre>";
	print_r($record);
	print "</pre>";
	print "<br><hr><br>";

} // end displayRecord()

function showCrit($string) {
	print "<div class='alert alert-danger'>$string</div>"; 
	error_log($string);
} // end showCrit()

function showWarn($string) {
	print "<div class='alert alert-warning'>$string</div>"; 
	error_log($string);
} // end showWarn()


function showSuccess($string) {
	print "<div class='alert alert-success'>$string</div>"; 
} // end showSuccess()
