<?php

require '../../../../3rdparty/autoload.php';

$baseUri = $argv[1];
$userName = $argv[2];
$password = $argv[3];
$file = $argv[4];

$client = new \Sabre\DAV\Client([
	'baseUri' => $baseUri,
	'userName' => $userName,
	'password' => $password
]);

$transfer = uniqid('transfer', true);

echo "Creating upload folder ($baseUri/uploads/$transfer)...." . PHP_EOL;
$result = $client->request('MKCOL', "$baseUri/uploads/$transfer");

$size = filesize($file);
$stream = fopen($file, 'r');

$chunks = [0,1,2,3,4];
foreach($chunks as $index) {
	$data = fread($stream, $size/5);
	echo "Upload chunk $baseUri/uploads/$transfer/$index" . PHP_EOL;
	$client->request('PUT', "$baseUri/uploads/$transfer/$index", $data);
}

$destination = pathinfo($file, PATHINFO_BASENAME);
echo "Moving $baseUri/uploads/$transfer/.file to it's final destination $baseUri/files/$destination" . PHP_EOL;
$client->request('MOVE', "$baseUri/uploads/$transfer/.file", null, [
	'Destination' => "$baseUri/files/$destination"
]);
