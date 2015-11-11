<?php

session_start();

// For testing GOA Proxy

print "<pre>";
$myurl = urlencode(base64_encode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']));
if($_GET["signin"]) {
	$goa_url = "http://nondual.rsmoorthy.net/oauth2callback/index.php?api_key=goaproxy1234&redirect=$myurl";
	header('Location: ' . filter_var($goa_url, FILTER_SANITIZE_URL));
	exit(0);
}

if($_GET["signout"]) {
	unset($_SESSION["name"]);
	unset($_SESSION["email"]);
	$goa_url = "http://nondual.rsmoorthy.net/oauth2callback/index.php?api_key=goaproxy1234&redirect=$myurl&signout=1";
	header('Location: ' . filter_var($goa_url, FILTER_SANITIZE_URL));
	exit(0);
}

if($_GET["user"]) {
	$userinfo = json_decode(base64_decode($_GET['user']), true);
	if($userinfo["name"] && $userinfo["email"]) {
		$_SESSION["name"] = $userinfo["name"];
		$_SESSION["email"] = $userinfo["email"];
	}
	print_r($userinfo);
	print_r($_GET["error"]);
	echo "<a href='?signout=1'>Sign out with Google via Google OAuth2 Proxy</a>";
}
elseif($_SESSION["email"] && $_SESSION["name"]) {
	print "Email: $_SESSION[email]\n";
	print "Name: $_SESSION[name]\n";
	echo "<a href='?signout=1'>Sign out with Google via Google OAuth2 Proxy</a>";
}
else 
	echo "<a href='?signin=1'>Sign in with Google via Google OAuth2 Proxy</a>";

$myurl = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
echo "<p><p><br><a href='$myurl'>Initial page</a>";

echo "<br><br><a href='?signin=1'>Sign in with Google via Google OAuth2 Proxy</a>";
