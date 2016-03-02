 <?php
session_start();
require_once '../../classes/google/autoload.php';
$cliente = new Google_Client();
$cliente->setApplicationName('CorreoPHP');
$cliente->setClientId('536060712172-mguuql9ce6jv77cu8c4kmheo4irc59sl.apps.googleusercontent.com');
$cliente->setClientSecret('m-VafCFc8svPQaG_X8Dh_zDk');
$cliente->setRedirectUri('https://usergest-jmroldan00.c9users.io/mail/oauth/guardar.php');
$cliente->setScopes('https://www.googleapis.com/auth/gmail.compose');
$cliente->setAccessType('offline');
if (!$cliente->getAccessToken()) {
   $auth = $cliente->createAuthUrl(); //solicitud
   header("Location: $auth");
}