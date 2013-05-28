<?php
// Statistics for Twit-Stream
// Jan Krummrey

// include OAuth library and helper files
require_once('db/140dev_config.php');
require_once('db/db_lib.php');
$oDB = new db;

// Is the backend working?
// get date from last tweet and get the time difference
$query='SELECT MAX( created_at ) AS lastTweet
		FROM tweets';
$result = $oDB->select($query);
$row = mysqli_fetch_assoc($result);
$lastTweet_db = $row["lastTweet"];
$lastTweet = strtotime($lastTweet_db);
$now = date_timestamp_get(date_create());
$ago = $now-$lastTweet;

// Set up Status-Box
$box = "success";		// Bootstrap class name
$msg = "OK!";			// Message for Status Box
if ($ago > (60*60))		// No tweet in the last hour
{
	$box = "warning";
	$msg = "WARNING!";
}
else if ($ago > (60*60*24))	// No tweet in the last day
{
	$box = "error";
	$msg = "ERROR!";
}

// When was the first recorded Tweet?
$query='SELECT MIN( created_at ) AS firstTweet
		FROM tweets';
$result = $oDB->select($query);
$row = mysqli_fetch_assoc($result);
$firstTweet = $row["firstTweet"];
$systemRunDays = number_format(($lastTweet-(strtotime($firstTweet)))/86400, 0, ',', ' ');

// Get the number of Tweets in the system
$query='SELECT COUNT( * ) AS tweetsCount
		FROM tweets';
$result = $oDB->select($query);
$row = mysqli_fetch_assoc($result);
$tweetsCount = $row["tweetsCount"];

// Get the number of Tweets today
$query='SELECT COUNT( * ) AS tweetsToday
		FROM tweets 
		WHERE created_at > DATE_SUB(NOW(), INTERVAL 1 DAY)';        
$result = $oDB->select($query);
$row = mysqli_fetch_assoc($result);
$tweetsToday = $row["tweetsToday"];

// Get the number of Users in the system
$query='SELECT COUNT( * ) AS usersCount
		FROM users';
$result = $oDB->select($query);
$row = mysqli_fetch_assoc($result);
$usersCount = $row["usersCount"];

// Get the number of Mentions in the System
$query='SELECT COUNT( * ) AS mentionsCount
		FROM tweet_mentions';
$result = $oDB->select($query);
$row = mysqli_fetch_assoc($result);
$mentionsCount = $row["mentionsCount"];

// Get the number of Hashtags in the System
$query='SELECT COUNT( * ) AS hashtagsCount
		FROM tweet_tags';
$result = $oDB->select($query);
$row = mysqli_fetch_assoc($result);
$hashtagsCount = $row["hashtagsCount"];

// Get the number of URLs in the System
$query='SELECT COUNT( * ) AS URLsCount
		FROM tweet_urls';
$result = $oDB->select($query);
$row = mysqli_fetch_assoc($result);
$URLsCount = $row["URLsCount"];

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>Twit-Stream - Statistics</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="">
		<meta name="author" content="">
		<link href="css/bootstrap.css" rel="stylesheet">
		<style type="text/css">
			body {
				padding-top: 60px;
				padding-bottom: 40px;
			}
		</style>
		<link href="css/bootstrap-responsive.css" rel="stylesheet">
		<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
		<!--[if lt IE 9]>
			<script src="js/html5shiv.js"></script>
		<![endif]-->
	</head>
	<body>
		<div class="navbar navbar-inverse navbar-fixed-top">
			<div class="navbar-inner">
				<div class="container">
					<button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="brand" href="#">Twit-Stream</a>
					<div class="nav-collapse collapse">
						<ul class="nav">
							<li><a href="#">Home</a></li>
							<li><a href="#about">About</a></li>
							<li class="active"><a href="#contact">Statistics</a></li>
						</ul>
					</div><!--/.nav-collapse -->
				</div>
			</div>
		</div>
		<div class="container">
			<div class="row">
				<div class="span9">
					<div class="well well-small">
						<h1>Statistics</h1>
						<p>
							<br />The System has been collecting tweets for <strong><?php echo $systemRunDays; ?></strong> days now.
						</p>
					</div>
				</div>
				<div class="span3">
					<div class=" alert alert-<?php echo $box; ?>">
						<h1><?php echo $msg; ?></h1>
						<p>last update:<br /><strong><?php echo $ago; ?></strong> seconds ago</p>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="span3">
					<h3>Tweets</h3>
					<div class="well well-small">
						<h4><small>total</small></h4>
						<h1 class="text-right"><?php echo number_format($tweetsCount, 0, ',', ' '); ?></h1>
					</div>
				</div>
				<div class="span3">
					<h3>&nbsp;</h3>
					<div class="well well-small">
						<h4><small>today</small></h4>
						<h1 class="text-right"><?php echo $tweetsToday; ?></h2>
					</div>
				</div>		
				<div class="span3">
					<h3>&nbsp;</h3>
					<div class="well well-small">
						<h4><small>Average per day</small></h4>
						<h1 class="text-right"><?php echo number_format($tweetsCount/$systemRunDays, 0, ',', ' '); ?></h2>
					</div>
				</div>
			</div>
			<hr />
			<div class="row">
				<div class="span3">
					<h3>Users</h3>
					<div class="well well-small">
						<h4><small>total</small></h4>
						<h1 class="text-right"><?php echo number_format($usersCount, 0, ',', ' '); ?></h1>
					</div>
				</div>
				<div class="span3">
					<h3>Mentions</h3>
					<div class="well well-small">
						<h4><small>total</small></h4>
						<h1 class="text-right"><?php echo number_format($mentionsCount, 0, ',', ' '); ?></h1>
					</div>
				</div>
				<div class="span3">
					<h3>Hashags</h3>
					<div class="well well-small">
						<h4><small>total</small></h4>
						<h1 class="text-right"><?php echo number_format($hashtagsCount, 0, ',', ' '); ?></h1>
					</div>
				</div>

				<div class="span3">
					<h3>URLs</h3>
					<div class="well well-small">
						<h4><small>total</small></h4>
						<h1 class="text-right"><?php echo number_format($URLsCount, 0, ',', ' '); ?></h1>
					</div>
				</div>
			
			</div>
			<hr>
			<footer>
				<p><a href="https://github.com/krummrey/twit-stream">Twit-Stream</a></p>
			</footer>
		</div> <!-- /container -->
		<!-- Placed at the end of the document so the pages load faster -->
		<script src="js/jquery.js"></script>
	</body>
</html>
