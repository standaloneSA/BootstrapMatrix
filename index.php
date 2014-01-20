<?php

// Include this for the authentication part... 
require_once 'googleAPI/src/Google_Client.php'; 

// Then include whichever of these you actually need. 
// require_once 'googleAPI/src/contrib/Google_AdExchangeSellerService.php';
// require_once 'googleAPI/src/contrib/Google_AdSenseService.php';
// require_once 'googleAPI/src/contrib/Google_AdexchangebuyerService.php';
// require_once 'googleAPI/src/contrib/Google_AdsensehostService.php';
// require_once 'googleAPI/src/contrib/Google_AnalyticsService.php';
// require_once 'googleAPI/src/contrib/Google_AndroidpublisherService.php';
// require_once 'googleAPI/src/contrib/Google_AppstateService.php';
// require_once 'googleAPI/src/contrib/Google_AuditService.php';
// require_once 'googleAPI/src/contrib/Google_BigqueryService.php';
// require_once 'googleAPI/src/contrib/Google_BloggerService.php';
// require_once 'googleAPI/src/contrib/Google_BooksService.php';
// require_once 'googleAPI/src/contrib/Google_CalendarService.php';
// require_once 'googleAPI/src/contrib/Google_CivicInfoService.php';
// require_once 'googleAPI/src/contrib/Google_ComputeService.php';
// require_once 'googleAPI/src/contrib/Google_CoordinateService.php';
// require_once 'googleAPI/src/contrib/Google_CustomsearchService.php';
// require_once 'googleAPI/src/contrib/Google_DatastoreService.php';
// require_once 'googleAPI/src/contrib/Google_DfareportingService.php';
// require_once 'googleAPI/src/contrib/Google_DirectoryService.php';
require_once 'googleAPI/src/contrib/Google_DriveService.php';
// require_once 'googleAPI/src/contrib/Google_FreebaseService.php';
// require_once 'googleAPI/src/contrib/Google_FusiontablesService.php';
// require_once 'googleAPI/src/contrib/Google_GamesManagementService.php';
// require_once 'googleAPI/src/contrib/Google_GamesService.php';
// require_once 'googleAPI/src/contrib/Google_GanService.php';
// require_once 'googleAPI/src/contrib/Google_GroupssettingsService.php';
// require_once 'googleAPI/src/contrib/Google_LicensingService.php';
// require_once 'googleAPI/src/contrib/Google_MirrorService.php';
// require_once 'googleAPI/src/contrib/Google_ModeratorService.php';
require_once 'googleAPI/src/contrib/Google_Oauth2Service.php';
// require_once 'googleAPI/src/contrib/Google_OrkutService.php';
// require_once 'googleAPI/src/contrib/Google_PagespeedonlineService.php';
// require_once 'googleAPI/src/contrib/Google_PlusDomainsService.php';
// require_once 'googleAPI/src/contrib/Google_PlusService.php';
// require_once 'googleAPI/src/contrib/Google_PredictionService.php';
// require_once 'googleAPI/src/contrib/Google_ReportsService.php';
// require_once 'googleAPI/src/contrib/Google_ResellerService.php';
// require_once 'googleAPI/src/contrib/Google_SQLAdminService.php';
// require_once 'googleAPI/src/contrib/Google_ShoppingService.php';
// require_once 'googleAPI/src/contrib/Google_SiteVerificationService.php';
// require_once 'googleAPI/src/contrib/Google_StorageService.php';
// require_once 'googleAPI/src/contrib/Google_TaskqueueService.php';
// require_once 'googleAPI/src/contrib/Google_TasksService.php';
// require_once 'googleAPI/src/contrib/Google_TranslateService.php';
// require_once 'googleAPI/src/contrib/Google_UrlshortenerService.php';
// require_once 'googleAPI/src/contrib/Google_WebfontsService.php';
// require_once 'googleAPI/src/contrib/Google_YouTubeAnalyticsService.php';
// require_once 'googleAPI/src/contrib/Google_YouTubeService.php';

// This is the PHP library for Google Spreadsheets, since you can't get 
// content out of them with the GoogleAPI.
require_once 'php-google-spreadsheet-client/src/Google/Spreadsheet/Autoloader.php';

// Here begins our stuff

// This contains the developer and app keys for talking to $whatever
require_once 'secrets.php'; 

// Some helper functions to make getting data out of Google easier
require_once 'googleDriveFuncs.php'; 

// Sets up, verifies, and tears down the connection(s), as needed. 
require_once 'auth.php'; 

global $DEBUG; 

$DEBUG=0; 
if (isset($_GET['DEBUG'])) {
	$DEBUG=1; 
}

// Add in whichever Google scopes  you need for your application
// Available scopes can be found here: 
// https://www.googleapis.com/discovery/v1/apis/
// (with an explanation here: https://developers.google.com/discovery/) 
$GoogleScopes = array(
		"https://www.googleapis.com/auth/drive.readonly", 
		"https://www.googleapis.com/auth/userinfo.email",
		"https://www.googleapis.com/auth/userinfo.profile",
		"https://spreadsheets.google.com/feeds"
	); 


function openPage() {
	print ' 
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>TITLE GOES HERE</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
		
		<script src="http://code.jquery.com/jquery-2.0.3.js"></script>

		<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.3/js/bootstrap.min.js"></script>
		<!-- For local version: <script src="js/bootstrap.min.js"></script> --> 

		<!-- Add a js file for the curent app (to be modified as needed) -->
		<script src="js/AppName.js"></script>
		
		<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css"/>
		<!-- To use the local version: <link href="css/bootstrap.css" rel="stylesheet"/> -->
		
		<link rel="stylesheet" href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css"/>
		<!-- To use the local version: <link href="css/font-awesome.css" rel="stylesheet"/> -->
		
		<link rel="stylesheet" href="//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css"/>
		<script src="//code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
		
		<!-- Add in our custom style sheet (to be modified as needed, per app) -->
		<link href="css/AppName.css" rel="stylesheet"/>
		
	</head>
	<body>
	'; 
} // end openPage()

function printHeaderBar($GoogleClient=0) {
// This function creates a header bar at the top of the page. 
// Customize however you need. 
	print '	<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
			<div class="container">
				<div class="navbar-header">
					<a class="navbar-brand" href="?#">Title Goes Here</a>
				</div>
			'; 
			if ($GoogleClient) {
				$loggedInUser = getUserEmail($GoogleClient); 
				if ($loggedInUser) {
					print '<div class="collapse navbar-collapse">'; 
					print '<ul class="nav navbar-nav navbar-right">'; 
					print '<p class="navbar-text">Logged in as ' . $loggedInUser . '</p>'; 
					print '<li class="active pull-right"><a href="?logout=1">Logout</a></li>';
					print '</ul>';  
					print '</div>'; 
				}
			} 
			
			print '
				</div>
			</div>
		</div>
	'; 
} // end printHeaderBar()
		
function closePage() {
	print ' 
	
		<script src="js/jquery.min.js"></script>

		<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.3/js/bootstrap.min.js"></script>
		<!-- For local version: <script src="js/bootstrap.min.js"></script> --> 

		<!-- Add a js file for the curent app (to be modified as needed) -->
		<script src="js/AppName.js"></script>
	</body>
</html>'; 

} // end closePage()


openPage(); 

// This block sets up the connection that we'll use for the 
// rest of theq queries. $googleClient is the object that 
// everything else will need. 
try {
	// $Application and $Developer are arrays full of keys and credentials that are stored in secrets.php
	// We need to grab the $GoogleClient that initSession() creates, because the various Google Docs and 
	// Google Drive calls need it. It stores the user's token. 
	 $GoogleClient = initSession($Application, $Developer, $GoogleScopes);
} catch (Exception $e) {

	showCrit("There was an error initializing the connection to Google: <br>" . $e->getMessage()); 
	closePage(); 
	exit; 
} 

$Workbook = "Spreadsheet Name"; 
$Worksheet = "Sheet1"; 

// If the client doesn't have an access token, then they need to log in 
// with Google and get one. Once they've done that, there will be a 
// a hashed string returned from getAccessToken. 
// Code in initSession() is responsible for making sure that 
// $GoogleClient->getAccessToken() sets $_SESSION['token']  
if ( ! $GoogleClient->getAccessToken() ) {
	printHeaderBar(); 
	showWarn("Please log in with your Google Drive credentials"); 
	displayGoogleAuth();
	closePage(); 
	exit;  
} 

printHeaderBar($GoogleClient); 
	
// Now we're free to draw the page. First we get the contents of the worksheet we're interested in
try {
	 $arrContents = getWorksheet($GoogleClient, $Workbook, $Worksheet); 
} catch (Exception $e) {
	showCrit('Error: ' . $e->getMessage()); 
}  

if (isset($_GET['logout'])) {
	// We're killing the session
	setcookie('PHPSESSID', "");  
	session_destroy(); 
	print '<meta http-equiv="refresh" content="0; url=index.php">' ;	
	exit; 
} 


if (!$arrContents) {
	print '<h3>No records found. Perhaps you\'ve logged in as the wrong user?</h3>'; 
	printHeaderBar($GoogleClient); 
	closePage(); 
	exit; 
}  



// Then we loop through the results: 
	foreach ( $arrContents as $curRecord ) { 
		// displayRecord() is in googleDriveFuncs.php
		displayRecord($curRecord); 
	}

// End with closePage() so the javascript includes and various tags get closed. 
closePage(); 
