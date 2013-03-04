<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
require 'Predis/Autoloader.php';
Predis\Autoloader::register();
$redis = new Predis\Client(array(
    'scheme' => 'tcp',
    'host'   => 'localhost',
    'port'   => 6379,
));

// Checkout the current Que, but don't get the track that is currently playing aka 0
$queue = $redis->lrange('queue:5110c1e2aca8b5cbd20a278285de6b91', 1, -1);

// Makes Refrerence Values from Array
function refValues($arr)
{ 
    if (strnatcmp(phpversion(),'5.3') >= 0) //Reference is required for PHP 5.3+ 
    { 
        $refs = array(); 
        foreach($arr as $key => $value) 
            $refs[$key] = &$arr[$key]; 
         return $refs; 
     } 
     return $arr; 
}

//Open The MySQLI Connection
$mysqli = new mysqli('localhost','piq','piq13','piq');

// creates a string containing ?,?,? 
$clause = implode(',', array_fill(0, count($queue), '?'));

// Makes a string containing s,s,s
$type = implode('', array_fill(0, count($queue), 's'));

//Fetch the Votes in the Queue and order
$stmt = $mysqli->prepare('SELECT song_id, SUM(vote) as popularity FROM piq_votes WHERE song_id IN (' . $clause . ') GROUP BY song_id ORDER BY popularity DESC;');
//call_user_func_array(array($stmt,'bind_param'), array_merge(array($type, $queue)));
call_user_func_array('mysqli_stmt_bind_param', array_merge(array($stmt, $type), refValues($queue)));
$stmt->execute();
$result = $stmt->get_result();

// Make the stupid result into something useable
while($row = $result->fetch_row()) {
  $rows[] = $row;
}

// Overwrite the list items in redis
foreach ($rows as $idx => $row)
{
  $redis_idx = $idx + 1;
  $redis->lset('queue:5110c1e2aca8b5cbd20a278285de6b91', $redis_idx, $row[0]);
  echo $redis_idx." - ".$row[0]."\n";
}

?>
