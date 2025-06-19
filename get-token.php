<?php
require 'vendor/autoload.php';

$client = new Google_Client();
$client->setAuthConfig(__DIR__ . '/storage/app/google/credentials.json');
$client->addScope(Google_Service_Calendar::CALENDAR);
$client->setAccessType('offline');
$client->setPrompt('select_account consent');

$authUrl = $client->createAuthUrl();
printf("Abre este enlace en tu navegador:\n%s\n", $authUrl);
print('Introduce el código de verificación: ');
$authCode = trim(fgets(STDIN));

$accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
file_put_contents(__DIR__ . '/storage/app/google/token.json', json_encode($accessToken));
echo "Token guardado correctamente.\n";