<?php

// first, get all json data
$json = file_get_contents("occupations.json");
$data = json_decode($json,true);

// from the data, get all adamlink person uris with occupation asked
$personuris = $data[$_GET['occupation']]['personen'];
$occupationlabel = $data[$_GET['occupation']]['label'];


$sparqlquery = 'PREFIX dc: <http://purl.org/dc/elements/1.1/>
PREFIX sem: <http://semanticweb.cs.vu.nl/2009/11/sem/>
PREFIX skos: <http://www.w3.org/2004/02/skos/core#>
PREFIX foaf: <http://xmlns.com/foaf/0.1/>
SELECT * WHERE {
  ?pic dc:subject ?person . 
  ?person skos:prefLabel ?name .
  OPTIONAL {?pic sem:hasBeginTimeStamp ?start .}
  OPTIONAL {?pic dc:date ?datumstring .}
  ?pic <http://rdfs.org/ns/void#inDataset> ?set .
  ?pic foaf:depiction ?imgurl .
  FILTER ( ?person IN ('; 
  $i = 0;
  foreach ($personuris as $k => $v) {
	$i++;
	$sparqlquery .= '<' . $v . '>';
	if($i<count($personuris)){
		$sparqlquery .= ',';
	}
}
$sparqlquery .= '))} ORDER BY DESC(?start)';

// query might get to long for file_get_contents, do post with curl instead
$post = [
    'query' => $sparqlquery,
    'default-graph-uri' => NULL,
    'named-graph-uri' => NULL,
    'format'   => 'application/sparql-results+json'
];

$url = "https://api.data.adamlink.nl/datasets/AdamNet/all/services/endpoint/sparql";

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));

$json = curl_exec($ch);

curl_close($ch);

$data = json_decode($json,true);


?>
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
	.info{
		display: inline;
		height: 200px;
		border: 1px solid #4C44A5;
		padding: 5px;
		margin: 10px;
	}
	img{
		height: 200px;
		border: 1px solid #4C44A5;
		padding: 5px;
		margin: 10px;
	}
	</style>

	
</head>
<body>

	<h1>de <?= $occupationlabel ?> geportretteerd</h1>
	<?php

	foreach ($data['results']['bindings'] as $row) { 
		if($row['datumstring']['value']){
			$year = $row['datumstring']['value'];
		}elseif($row['start']['value']){
			$year = date("Y",strtotime($row['start']['value']));
		}else{
			$year = "????";
		}
		$from = array("https://data.adamlink.nl/saa/beeldbank/");
		$to = array("SAA");
		$instelling = str_replace($from, $to, $row['set']['value']);
		?>
		<a href="<?= $row['pic']['value'] ?>" target="_blank"><img src="<?= $row['imgurl']['value'] ?>" title="<?= $year ?> | <?= $row['name']['value'] ?> | <?= $instelling ?>" /></a>
	<?php
	}

	?>


</body>
</html>
