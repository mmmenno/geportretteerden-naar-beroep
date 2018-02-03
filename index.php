<!DOCTYPE html>
<html>
<head>
	
	<title>geportretteerden naar beroep</title>

	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	
	<link href="https://fonts.googleapis.com/css?family=Sura:400,700" rel="stylesheet">

	<script
  src="https://code.jquery.com/jquery-3.2.1.min.js"
  integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
  crossorigin="anonymous"></script>

    


	<style>
	body{
		padding: 30px 60px;
		text-align: center;
		font-family: 'Sura', serif;
		color: #4C44A5;
	}
	a{
		text-decoration: none;
		color: #4C44A5;
		display: inline-block;
		margin: 0;
		padding: 0;
	}
	</style>

	
</head>
<body>


	<?php

	$json = file_get_contents("occupations.json");
	$data = json_decode($json,true);

	function querySort ($x, $y) {
	    return strcasecmp($x['label'], $y['label']);
	}
	uasort($data, 'querySort');
	
	foreach ($data as $k => $v) {
		$size = ceil(($v['aantal']+30)/1.9);
		if($v['aantal']==1){
			$size = 12;
		}
		?>

			<a href="occupation.php?occupation=<?= $k ?>" style="font-size: <?= $size ?>px" title="<?= $v['aantal'] ?>"><?= $v['label'] ?></a>

		<?php
	}
	?>


</body>
</html>
