<?php

//start session
session_start();

require_once __DIR__ . "/vendor/autoload.php";

/*  From Client
		if token is provided
			get user info
			redirect_back
		redirect to Google
	From Google:
		get token
			get user info
			redirect_back
*/

// From Client
if($_GET["redirect"] && $_GET["api_key"] == "goaproxy1234") {
	$state = $_GET["redirect"];

	if($_GET["signout"]) {
		unset($_SESSION["token"]);
		token_to_redirect(null, $state);
	}

	if(isset($_SESSION['token'])) {
		error_log("Step 1");
		token_to_redirect($_SESSION["token"], $state);
	}

	redirect_to_google($state);
}

// From Google
if(isset($_GET['code'])) 
{ 
	if(!isset($_GET['state'])) die("Internal Error: Code present, but not state\n");
	$redirect_url = base64_decode($_GET['state']);
	$gClient = getGclient();
	$gClient->authenticate($_GET['code']);
	$token = $gClient->getAccessToken();
	token_to_redirect($token, $_GET['state']);
}

print "Unauthorized access\n";
exit(0);

function redirect_to_google($state)
{
	$gClient = getGclient();
	$authUrl = $gClient->createAuthUrl() . "&state=$state";
	header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));
	exit(0);
}

function token_to_redirect($token, $state)
{
	$redirect_url = base64_decode($state);
	if($token == null) 
	  	do_redirect_back($redirect_url, null, null);

	if(!$token)
		return false;
	$gClient = getGclient();
	$_SESSION["token"] = $token;
	$gClient->setAccessToken($token);

	$google_oauthV2 = new Google_Service_Oauth2($gClient);
	$userinfo = $google_oauthV2->userinfo->get();
  	$user = array( "name" => filter_var($userinfo['name'], FILTER_SANITIZE_SPECIAL_CHARS),
  				   "email" => filter_var($userinfo['email'], FILTER_SANITIZE_EMAIL),
  				   // "token" => $token 
  				  );
  	do_redirect_back($redirect_url, $user, null);
}

function do_redirect_back($redirect_url, $user, $error=null)
{
	if(preg_match("/\\?.*=/", $redirect_url))
		$redirect_url .= "&goa_proxy=1";
	else
		$redirect_url .= "?goa_proxy=1";
	if($user)
		$redirect_url .= "&user=" . (is_array($user) ? urlencode(base64_encode(json_encode($user))) : "");
	if($error)
		$redirect_url .= "&error=" . ($error ? urlencode($error) : "");
	header('Location: ' . filter_var($redirect_url, FILTER_SANITIZE_URL));
	exit(0);
}

function getGclient()
{
	########## Google Settings.. Client ID, Client Secret from https://cloud.google.com/console #############
	$google_client_id 		= '1080663858267-qtqpfp3p71367kbb5mitlshndq5fis8j.apps.googleusercontent.com';
	$google_client_secret 	= 'CzQGWpMd8ANJQnBoa3Fr-Ui4';
	$google_redirect_url 	= 'http://nondual.rsmoorthy.net/oauth2callback/index.php'; //path to your script
	$google_developer_key 	= 'AIzaSyAW9DOdKye_8aYdogGIGiu469PmOt1HgyA';

	$gClient = new Google_Client();
	$gClient->setApprovalPrompt('auto');
	$gClient->setApplicationName('Login to Journo');
	$gClient->setClientId($google_client_id);
	$gClient->setClientSecret($google_client_secret);
	$gClient->setRedirectUri($google_redirect_url);
	$gClient->setDeveloperKey($google_developer_key);
	$gClient->setScopes(array(
        'https://www.googleapis.com/auth/userinfo.email',
        'https://www.googleapis.com/auth/userinfo.profile',
    ));

	return $gClient;
}
?>