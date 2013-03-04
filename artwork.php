<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', '1');
require_once('simple_html_dom.php');
header('Content-type: application/json');

$file = $_GET['uri'];

function get_album_art($file) {
	$track = substr($file, 14);
	$html = file_get_html('http://open.spotify.com/track/'.$track);
	foreach ($html->find('#big-cover') as $img) {
		return str_replace('/300/', '/140/', $img->src);
	}
}

$url = array('url' => get_album_art($file));
echo json_encode($url);

?>

