<!DOCTYPE html>
<html>
  <head>
    <title>Twit-Stream User Lookup</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/bootstrap.min.css" rel="stylesheet" media="screen">  </head>
    <style type="text/css">
      body {
        padding: 20px;
      }
    </style>
  <body>
  <div class="container">
    
<?php
// If you have a lot of users to look up, you can automatically reload the script by uncommenting the next line
// header("Refresh: 1;");

error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

require 'app_tokens.php';
require 'tmhOAuth.php';

require_once('db/140dev_config.php');
require_once('db/db_lib.php');
$oDB = new db;

// You'd be surprised by how many users disappear if the script returns less and less users you can increase
// the skip value. It will then skip the X results from the db-query
$skip = 0;

$query='SELECT tweet_mentions.target_user_id
		FROM tweet_mentions
		LEFT JOIN users ON ( tweet_mentions.target_user_id = users.user_id ) 
		WHERE users.user_id IS NULL 
		GROUP BY tweet_mentions.target_user_id
		LIMIT '.$skip.' , 100';
			
$result = $oDB->select($query);
while($row = mysqli_fetch_assoc($result)) {
	$ids = $ids . $row['target_user_id'].", ";
}
$ids = rtrim($ids, " ");
$ids = rtrim($ids, ",");

print '<div class="well well-small">'.PHP_EOL;
print '<h2>Skipping: '.$skip.'</h2>'.PHP_EOL;
print '<small>'.$ids.'</small>'.PHP_EOL;

$connection = new tmhOAuth(array(
  'consumer_key'    => $consumer_key,
  'consumer_secret' => $consumer_secret,
  'user_token'      => $user_token,
  'user_secret'     => $user_secret
));

$connection->request('GET', $connection->url('1.1/users/lookup'), array(
  'user_id' => $ids
));

// Get the HTTP response code for the API request
$response_code = $connection->response['code'];

// Get Twitter rate limits
$headers = $connection->response['raw'];
$headers = explode("\n", $headers);
foreach ($headers as $header) {
    list($key, $value) = explode(':', $header, 2);
    $headers[trim($key)] = trim($value);
}

// Convert the JSON response into an array
$response_data = json_decode($connection->response['response'],true);

// A response code of 200 is a success
if ($response_code <> 200) {
  print "Error: $connection\n";
}

// Display the response array
print "<h2>Rate-Limit: ".$headers['x-rate-limit-remaining']." / ".$headers['x-rate-limit-limit'];
print '<span class="pull-right">Responses: '.count($response_data).' / 100</span></h2>'.PHP_EOL;
print '</div>'.PHP_EOL;

print '<ul class="thumbnails">';

for ($i = 0; $i < count($response_data); $i++)
{
	$user_id = $response_data[$i]['id_str'];
	$screen_name = $response_data[$i]['screen_name'];
	$name = $response_data[$i]['name'];
	$profile_image_url = $response_data[$i]['profile_image_url'];
	$location = $response_data[$i]['location']; 
	$url = $response_data[$i]['url'];
	$description = $response_data[$i]['description'];
	$created_at = $response_data[$i]['created_at'];
	$followers_count = $response_data[$i]['followers_count'];
	$friends_count = $response_data[$i]['friends_count'];
	$statuses_count = $response_data[$i]['statuses_count'];
	$time_zone = $response_data[$i]['time_zone'];
	$last_update = $response_data[$i]['status']['created_at'];
	
	print '<li class="span2">'.PHP_EOL;
	print '  <div class="">'.PHP_EOL;
	print '    <a href="https://twitter.com/'.$screen_name.'/">'.PHP_EOL;
	print '      <img class="" width="48" height="48" src="'.$profile_image_url.'" /><br />'.PHP_EOL;
	print '      <strong>'.$screen_name.'</strong>'.PHP_EOL;
	print '    </a><br /><small class="muted">'.$user_id.'</small>'.PHP_EOL;
	print '  </div>'.PHP_EOL;
	print '</li>'.PHP_EOL;
	// Add a new user row or update an existing one
	$field_values = 'user_id = ' . $user_id . ', ' .
		'screen_name = "' . $screen_name . '", ' .
		'name = "' . $name . '", ' .
		'profile_image_url = "' . $profile_image_url . '", ' .
		'location = "' . $location . '", ' . 
		'url = "' . $url . '", ' .
		'description = "' . $description . '", ' .
		'created_at = "' . date("Y-m-d H:i:s", strtotime ($created_at)) . '", ' .
		'followers_count = ' . $followers_count . ', ' .
		'friends_count = ' . $friends_count . ', ' .
		'statuses_count = ' . $statuses_count . ', ' . 
		'time_zone = "' . $time_zone . '", ' .
		'last_update = "' . date("Y-m-d H:i:s", strtotime ($last_update)) . '"' ;

	if ($oDB->in_table('users','user_id="' . $user_id . '"')) {
		$oDB->update('users',$field_values,'user_id = "' .$user_id . '"');
	} else {      
		$oDB->insert('users',$field_values);
	}
}
?>

	</ul>
	</div>
    <script src="http://code.jquery.com/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script>
  </body>
</html>
