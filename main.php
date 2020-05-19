<?php


if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
  throw new Exception(sprintf('Please run "composer require google/apiclient:~2.0" in "%s"', __DIR__));
}
require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__, '/.env')->load();

$video_id = "";
$api_key = getenv('GOOGLE_API_KEY');
$auth_code = "";

$client = new Google_Client();
$client->setApplicationName('');
$client->setScopes([
    'https://www.googleapis.com/auth/youtube',
]);

$client->setAuthConfig(__DIR__ . '/client_secret.json');
$client->setAccessType('offline');

if(file_exists(__DIR__ . "/accesstoken.json")){
    $accessToken = json_decode(file_get_contents(__DIR__ . "/accesstoken.json"), true);
    $client->setAccessToken($accessToken);

    if ($client->isAccessTokenExpired()) {
      $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
      $accessToken = $client->getAccessToken();
    }
} else {
    // Exchange authorization code for an access token.
    $accessToken = $client->fetchAccessTokenWithAuthCode($auth_code);
}
file_put_contents(__DIR__ . "/accesstoken.json", json_encode($accessToken)); //contain access token and refresh token.




//to get view, like, comments count.
$response = json_decode(file_get_contents("https://www.googleapis.com/youtube/v3/videos?part=statistics&id={$video_id}&key={$api_key}"), true);

if (isset($response['error'])) {
    die("fetch error");
}

$view_count = $response['items'][0]['statistics']['viewCount'];
$like_count = $response['items'][0]['statistics']['likeCount'];
$comment_count = $response['items'][0]['statistics']['commentCount'];

//saving the result/loading previous result.
if(file_exists(__DIR__ . "/count.json")){
    $count_from_file = json_decode(file_get_contents(__DIR__ . "/count.json"), true);

    file_put_contents(__DIR__ . "/count.json", json_encode(array('view_count' => $view_count, 'like_count' => $like_count, 'comment_count' => $comment_count)));

    if ($count_from_file['view_count'] == $view_count && $count_from_file['like_count'] == $like_count && $count_from_file['comment_count'] == $comment_count) {
        echo "no need to update.";
        exit();
    }
} else {
    file_put_contents(__DIR__ . "/count.json", json_encode(array('view_count' => $view_count, 'like_count' => $like_count, 'comment_count' => $comment_count)));
}




// Define service object for making API requests.
$service = new Google_Service_YouTube($client);

// Define the $video object, which will be uploaded as the request body.
$video = new Google_Service_YouTube_Video();

// Add 'id' string to the $video object.
$video->setId($video_id);

// Add 'kind' string to the $video object.
$video->setKind('youtube#video');

// Add 'snippet' object to the $video object.
$videoSnippet = new Google_Service_YouTube_VideoSnippet();
$videoSnippet->setCategoryId('22');
$videoSnippet->setTitle("This video has {$view_count} views, {$like_count} likes, {$comment_count} comments.");
$video->setSnippet($videoSnippet);

$response = $service->videos->update('snippet', $video);
// print_r(json_encode($response));
echo "updated title.";
