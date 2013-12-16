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

function getWorksheet($ssName, $wsName) { 
// Wrapper function for getWorksheets() and getWSContents()

return getWSContents($wsName, getWorksheets($ssName)); 

} // end getWorksheet()

function getWorksheets($ssName) {
	global $DEBUG;
	global $GoogleClient;  
	
	if ($DEBUG) error_log("In getWorksheets()");
// Retrieves worksheets in $ssName 
// returns the worksheet feed object
	if ($GoogleClient->getAccessToken()) {
		if ($DEBUG) error_log("Getting spreadsheet list");
		try {
			$ssFeed = $ssService->getSpreadsheets();
		} catch (Exception $e) {
			if ( $e->getCode() == "401" ) {
				error_log("Error with token. Must relog in.");
				session_unset();
				$redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
				header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
			} else {
				throw $e; 
			}
		}
		if ( ! $ssFeed ) throw new Exception('Unable to obtain spreadsheet feed'); 
		$spreadsheet = $ssFeed->getByTitle($ssName); 
		if ( ! $spreadsheet )  throw new Exception("Error: Unable to find spreadsheet"); 

		$worksheetFeed = $spreadsheet->getWorksheets();
		if ( ! $worksheetFeed ) { 
			throw new Exception("Error: Unable to list worksheets");
		} else { 
			return $worksheetFeed; 
		}
	} else { 
		throw new Exception("Unable to find access token"); 
	}

} // end getWorksheets() 

function getWSContents($wsName, $wsFeed) { 
// Will return only the worksheet cells, in an assoc array

	$ws = $wsFeed->getByTitle($wsName); 
	if ( ! $ws ) throw new Exception("Unable to find worksheet"); 

	$listFeed = $ws->getListFeed(); 
	if ( ! $listFeed ) throw new Exception("Unable to create feed from worksheet"); 

	return $listFeed->getEntries(); 

} //end getWSFeed()

function displayGoogleAuth() {
	// Displayes the normal "sign in with Google" image

	global $GoogleClient;  
	print '<center><a href="' . $GoogleClient->createAuthUrl() . '"><img src="https://developers.google.com/accounts/images/sign-in-with-google.png" border=0 width=250></a>'; 
}


function displayRecord($record) { 
   // This function is what can be used to display a single record from a Google Spreadsheet. 
   // It needs to be crafted to each use case, but it's farcically simple to do when you
   // use Bootstrap's methods: 
   // http://getbootstrap.com/components/

   if ($DEBUG) {
      // A lot of times, you really just want to see what you get from the spreadsheet. 
      print "<pre>";
      print_r($record);
      print "</pre>";
   }
}
