<?php
require 'vendor/autoload.php';

use GuzzleHttp\Client;

$app = new \Slim\Slim();

/**
 * Return Github.com user JSON object
 * @param  string $username Github.com username
 * @return object User object
 */
function getUserRepos ($username) {
  $client = new Client([
    'base_uri' => 'https://api.github.com/'
    ]);

  // get user repos
  $response = $client->request("GET", "users/$username/repos");

  return json_decode($response->getBody());
  
};

$app->get('/', function() use ($app) {
  echo "Welcome to the Slim Based Github API";
  $app->render('form.php');

});

$app->post('/', function() use ($app) {

  $req = $app->request();
  $uname = $req->params('uname');
  
  $repos = getUserRepos($uname);
  

  echo 'Current repos for user ' . $uname . ':<br>';

  foreach ($repos as $repo) {
    echo '<a href="/api/' . $uname . '/' . $repo->name . '">' . $repo->name . '</a><br>';
  }

  echo '<br><br><a href="/">Search Again</a>';

});

// group for api; preparing for when more methods are added
$app->group('/api',function() use ($app) {
  // print out repos for user provided
  $app->get('/:username', function ($username) {

    $repos = getUserRepos($username);

    echo 'Current repos for user ' . $username . ':<br>';

    foreach ($repos as $repo) {
      echo '<a href="/api/' . $username . '/' . $repo->name . '">' . $repo->name . '</a><br>';
    }

    echo '<br><br><a href="/">Search Again</a>';

  });

  // print out commits from the provided repo
  $app->get('/:username/:repo', function($username,$repo){
    $client = new Client([
      'base_uri' => 'https://api.github.com/'
      ]);

    // get user commits
    $response = $client->request("GET", "repos/$username/$repo/commits");

    $commits = json_decode($response->getBody());

    echo "Commits for the $repo repo by user $username:" . '<br><br>';

    foreach ($commits as $commit) {
      echo $commit->commit->message . '<br>';
    }

    echo '<br><br><a href="/api/' . $username . '">Back to Repos</a>';

    echo '<br><br><a href="/">Start Over</a>';

  });
});

$app->run();
