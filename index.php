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
// require_once 'googleAPI/src/contrib/Google_DriveService.php';
// require_once 'googleAPI/src/contrib/Google_FreebaseService.php';
// require_once 'googleAPI/src/contrib/Google_FusiontablesService.php';
// require_once 'googleAPI/src/contrib/Google_GamesManagementService.php';
// require_once 'googleAPI/src/contrib/Google_GamesService.php';
// require_once 'googleAPI/src/contrib/Google_GanService.php';
// require_once 'googleAPI/src/contrib/Google_GroupssettingsService.php';
// require_once 'googleAPI/src/contrib/Google_LicensingService.php';
// require_once 'googleAPI/src/contrib/Google_MirrorService.php';
// require_once 'googleAPI/src/contrib/Google_ModeratorService.php';
// require_once 'googleAPI/src/contrib/Google_Oauth2Service.php';
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

global $DEBUG=0; 
if ($_GET['DEBUG']) {
	$DEBUG=1; 
}

// Add in whichever Google scopes  you need for your application
// Available scopes can be found here: 
// https://www.googleapis.com/discovery/v1/apis/
// (with an explanation here: https://developers.google.com/discovery/) 
global $GoogleScopes = array(
	"https://www.googleapis.com/auth/userinfo.email",
	"https://www.googleapis.com/auth/userinfo.profile",
	); 

function printHeaderBar( ) {
// This function creates a header bar at the top of the page. 
// Customize however you need. 
	print '	<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
			<div class="container">
				<div class="navbar-header">
					<a class="navbar-brand" href="?#">Title Goes Here</a>
				</div>
			</div>
		</div>
	'; 
} // end printHeaderBar()


// Before displaying the page, lets get set up (from auth.php):
initSession.php(); 

// Set our spreadsheet (getWorksheet is in googleDriveFuncs.php):
$arrContents = getWorksheet("Spreadsheet Name", "Sheet 1"); 
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>TITLE GOES HERE</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
		
		<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css">
		<!-- To use the local version: <link href="css/bootstrap.css" rel="stylesheet"/> -->
		
		<link rel="stylesheet" href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css">
		<!-- To use the local version: <link href="css/font-awesome.css" rel="stylesheet"/> -->
		
		<!-- Add in our custom style sheet (to be modified as needed, per app) -->
		<link href="css/AppName.css" rel="stylesheet"/>
		
	</head>
	<body>
			
		
<?php 

printHeaderBar(); 

foreach ( $arrContents as $curRecord ) { 
	// displayRecord() is in googleDriveFuncs.php
	displayRecord($curRecord); 
}


?>

		<script src="js/jquery.min.js"></script>

		<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.3/js/bootstrap.min.js"></script>
		<!-- For local version: <script src="js/bootstrap.min.js"></script> --> 

		<!-- Add a js file for the curent app (to be modified as needed) -->
		<script src="js/AppName.js"></script>
	</body>
</html>