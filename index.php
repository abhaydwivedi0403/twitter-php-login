<?php
//start session
session_start();

//destroy session when logout by passring logout parameter in url
if(isset($_GET["logout"]) && $_GET["logout"]==1)
{
	session_destroy();
	header('Location: ./index.php');
}

// Include config file and twitter PHP Library
include_once("config.php");
include_once("api/twitteroauth.php");
?>
<html>
<head>
<title>Login with Twitter</title>

</head>
<body>
<div style="background-color:#AAA; color:white; margin:20px; padding:20px;">
<?php


if(isset($_SESSION['status']) && $_SESSION['status']=='verified') 
{	//Success, redirected back from process.php with varified status.
	//retrive variables
	$screenname 		= $_SESSION['request_vars']['screen_name'];
	$twitterid 			= $_SESSION['request_vars']['user_id'];
	$oauth_token 		= $_SESSION['request_vars']['oauth_token'];
	$oauth_token_secret = $_SESSION['request_vars']['oauth_token_secret'];

	
	echo '<p> Welcome <strong>'.$screenname.'</strong>. <a href="index.php?logout=1">Logout</a>!</div>';
	$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $oauth_token, $oauth_token_secret);
	
	//see if user wants to tweet using form.
	print_r($_POST);
	if(isset($_POST["updateme"])) 
	{
		//Post text to twitter
		$my_update = $connection->post('statuses/update', array('status' => $_POST["updateme"]));
		die('<script type="text/javascript">window.top.location="index.php"</script>'); //redirect back to index.php
		
	}
	
	//show tweet form
	echo '<div class="tweet_box">';
	echo '<form method="post" action="index.php"><table width="200" border="0" cellpadding="3">';
	echo '<tr>';
	echo '<td><textarea name="updateme" cols="60" rows="4"></textarea></td>';
	echo '</tr>';
	echo '<tr>';
	echo '<td><input type="submit" value="Tweet" /></td>';
	echo '</tr></table></form>';
	echo '</div>';
	
	
		//Get latest tweets
		$my_tweets = $connection->get('statuses/user_timeline', array('screen_name' => $screenname, 'count' => 5));
		
		
}else{
	
	// twitter login button when no session is set and redirecting to control proces to initiate login process through twitter
	echo '<center><a href="control_process.php"><button> Click Here To Login with Twitter</button></a></center>';
}

?>
</div>
</body>
</html>
