<?php
// secrets.php
//
// This file exists to hold the various developer and application 
// credentials used to set up the necessary sessions and so on. 
// It's primarily used by auth.php. 
//
// To set up a connection, you call initSession($Developer, $Application). 
// Because some of the different services may require an identical 'label' 
// for a credential, but will obviously have different values, prepend the
// name of the service to the key, as I did with GoogleClientId. 


// The developer key is called the "Server Key" in the API Cloud Console
$Developer = array(
	"GoogleDeveloperKey"	=> "DeveloperKeyGoesHere"
	); 

$Application = array(
	"GoogleAppName"			=> "Your App Name", 
	"GoogleRedirectURI"		=> "URL Goes Here", 
	"GoogleClientId"		=> "Google Client ID Goes Here", 
	"GoogleClientSecret"	=> "Google Client Secret Goes Here"
	); 

