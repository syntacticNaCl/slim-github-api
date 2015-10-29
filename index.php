<?php
require 'vendor/autoload.php';

use GuzzleHttp\Client;

$app = new \Slim\Slim();

$app->get('/', function(){
  echo "Welcome to the Slim Based Github API";
});

// print out repos for user provided
$app->get('/api/:username', function ($username) {
  $client = new Client([
      'base_uri' => 'https://api.github.com/'
  ]);

  // get user repos
  $response = $client->request("GET", "users/$username/repos");

  $repos = json_decode($response->getBody());

  echo "Current repos for user $username:" . '<br><br>';

  foreach ($repos as $repo) {
    echo $repo->name . '<br>';
  }

});

// print out commits from the provided repo
$app->get('/api/:username/:repo', function($username,$repo){
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

});

// TODO add POST so user has a form to enter in their username and/or repo

$app->run();
