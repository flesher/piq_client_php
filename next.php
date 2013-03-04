<?php 

if (isset($_REQUEST['admin'])) {
  if ($_REQUEST['admin'] == 'grant') {
    $admin = true;
  } else {
    die();
  }
}

require 'Predis/Autoloader.php';
Predis\Autoloader::register();
$redis = new Predis\Client(array(
    'scheme' => 'tcp',
    'host'   => 'localhost',
    'port'   => 6379,
));

$redis->publish('status:5110c1e2aca8b5cbd20a278285de6b91', 'ok');
$redis->publish('status:5110c1e2aca8b5cbd20a278285de6b91', 'next');

header("Location: mobile.php?admin=grant");

?>