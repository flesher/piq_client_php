<?php 
$song_id = $_REQUEST['song_id'];
?>
<!DOCTYPE html> 
<html>
	<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1"> 
	<title>Vote Dialog</title> 
  <link rel="stylesheet" href="http://code.jquery.com/mobile/1.2.0/jquery.mobile-1.2.0.min.css" />
  <script src="http://code.jquery.com/jquery-1.8.2.min.js"></script>
  <script src="http://code.jquery.com/mobile/1.2.0/jquery.mobile-1.2.0.min.js"></script>
</head> 
<body> 

<div data-role="dialog">
	
		<div data-role="header" data-theme="d">
			<h1>Vote for this song</h1>

		</div>

		<div data-role="content" data-theme="c">
			<a href="vote.php?song_id=<?php echo $song_id ?>&vote_value=1" data-role="button" data-ajax="false" data-theme="b">Vote Up</a>
			<a href="vote.php?song_id=<?php echo $song_id ?>&vote_value=-1" data-role="button" data-ajax="false" data-theme="c">Vote Down</a>
		</div>
	</div>

</body>
</html>