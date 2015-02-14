<?php
session_start();
include_once("config.php");
include_once("api/twitteroauth.php");

if (isset($_REQUEST['oauth_token']) && $_SESSION['token']  !== $_REQUEST['oauth_token']) {

	// if oauth token is old clear session and redirection to login page

	session_destroy();
	header('Location: ./index.php');
	
}elseif(isset($_REQUEST['oauth_token']) && $_SESSION['token'] == $_REQUEST['oauth_token']) {

	// intialize the class create connection object of TwitterOAuth c
	$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $_SESSION['token'] , $_SESSION['token_secret']);
	$access_token = $connection->getAccessToken($_REQUEST['oauth_verifier']);
	if($connection->http_code=='200')
	{
		//st status and token in session and redirect user to login page, as logged in sucessfully
		$_SESSION['status'] = 'verified';
		$_SESSION['request_vars'] = $access_token;
		header('Location: ./index.php');
	}else{
		die("error, try again later!");
	}
		
}else{

	if(isset($_GET["denied"]))
	{
		header('Location: ./index.php');
		die();
	}

	
	// intialize the twitter class
	$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET);

	//get the requesst token and store in session
	$request_token = $connection->getRequestToken(OAUTH_CALLBACK);
	$_SESSION['token'] 			= $request_token['oauth_token'];
	$_SESSION['token_secret'] 	= $request_token['oauth_token_secret'];
	
	// any value other than 200 is failure, so continue only if http code is 200
	if($connection->http_code=='200')
	{
		//redirect user to twitter
		$twitter_url = $connection->getAuthorizeURL($request_token['oauth_token']);
		header('Location: ' . $twitter_url); 
	}else{
		die("error connecting to twitter! try again later!");
	}
}
?>

