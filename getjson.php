<?php


$sparqlquery = 'PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
PREFIX dc: <http://purl.org/dc/elements/1.1/>
PREFIX schema: <http://schema.org/>
PREFIX owl: <http://www.w3.org/2002/07/owl#>
PREFIX wdt: <http://www.wikidata.org/prop/direct/>
PREFIX wd: <http://www.wikidata.org/entity/>
SELECT DISTINCT ?uri ?wd ?occ ?occlabel WHERE {
		{
			SELECT ?uri ?wd WHERE {
			?sub dc:subject ?uri .
			?uri rdf:type schema:Person .
			?uri owl:sameAs ?wd .
		} 
		GROUP BY ?uri
		#LIMIT 10
	}
	SERVICE <https://query.wikidata.org/sparql> { 
		?wd wdt:P106 ?occ.
		?occ rdfs:label ?occlabel .
		FILTER (LANG(?occlabel) = "nl") .
	}
}';


//echo $sparqlquery;

$url = "https://api.data.adamlink.nl/datasets/AdamNet/all/services/endpoint/sparql?default-graph-uri=&query=" . urlencode($sparqlquery) . "&format=application%2Fsparql-results%2Bjson&timeout=120000&debug=on";

$json = file_get_contents($url);

$data = json_decode($json,true);

$occs = array();

foreach ($data['results']['bindings'] as $row) {
	$wdid = str_replace("http://www.wikidata.org/entity/", "", $row['occ']['value']);
	if(!isset($occs[$wdid])){
		$occs[$wdid] = array(
							"label" => $row['occlabel']['value'],
							"aantal" => 1,
							"personen" => array($row['uri']['value'])
						);
	}else{
		$occs[$wdid]['aantal']++;
		$occs[$wdid]['personen'][] = $row['uri']['value'];
	}
}

$json = json_encode($occs);

file_put_contents('occupations.json', $json);

die($json);