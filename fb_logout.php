<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
require 'facebook/facebook.php';

$facebook = new Facebook(array(
  'appId'  => '417716448314092',
  'secret' => '414b0ae5116a8c04679455a5553795aa',
));

//ovewrites the cookie
$facebook->destroySession();

header('Location: mobile.php');

?>
