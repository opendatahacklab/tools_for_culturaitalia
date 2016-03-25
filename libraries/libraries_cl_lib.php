<?php
/**
 * To retrieve all the libraries from the MiBAC SPARQL endpoint. 
 * This dataset should conform the CIDOC-CRM data model.
 *
 * Cristiano Longo 2014
 *
 */

ini_set('display_errors',1); 
ini_set('max_execution_time',300); 
ini_set("memory_limit","1000M");
error_reporting(E_ALL);
require("../sparqllib.php");

/**
 * Point coordinates
 */
class Point{
	public $lat;
	public $long;
	
	function __construct($lat, $long){
		$this->lat=$lat;
		$this->long=$long;
	}
}

/**
 * A class representing a library. 
 */
class Library{
	private $uri;
	private $name;
	private $address;
	private $notes = array();
	private $coords;
	
	function __construct($uri, $name, $address){
		$this->uri=$uri;
		$this->name=$name;
		$this->address=$address;
	}
	
	function getURI(){
		return $this->uri;
	}
	
	function getName(){
		return $this->name;
	}
	
	function getAddress(){
		return $this->address;
	}

	function setAddress($newAddress){
		$this->address=$newAddress;
	}
	
	function addNote($note){
		if (!(in_array($note, $this->notes)))
			array_push($this->notes, $note);
	}
	
	function getNotes(){
		return $this->notes;
	}
	
	function setCoordinates($coords){
		$this->coords=$coords;
	}
	
	function getCoordinates(){
		return $this->coords;
	}
	
	function getLong(){
		return $this->long;
	}
}

/**
 * Retrieve a point [lat,long] from an address.
 * Return a string lat,long, or null if geocoding failed.
 **/
function retrievePoint($address){
	$request_url = "http://maps.googleapis.com/maps/api/geocode/xml?sensor=false&address=" . urlencode($address);
	$xml = simplexml_load_file($request_url) or die("url not loading");
	$lat = $xml->result->geometry->location->lat;
	$long = $xml->result->geometry->location->lng;
	
	if ($lat!=null && $long!=null)  return new Point($lat, $long);	
	echo "Unable to load $request_url";
	return null;
}

/*
 * Process a query result set by invoking the handler on each discovered library.
 */
function process($data, $handler){
	foreach($data as $d){
		$uri=$d['r'];
		$name=$d['name'];
		$address=$d['address'];
		if (isset($d['note']))  
			$note=$d['note'];
		else
			$note=null;
		if (!isset($actual)){
			$actual=new Library($uri, $name, $address);
			if (isset($note))
				$actual->addNote($note);
		} else if ($uri==$actual->getURI()){
			if (preg_match("/- Italia$/", $address))
				$actual->setAddress($address);
			if (isset($note))
				$actual->addNote($note);
		} else{
			$actualAddress=str_replace('-',',',$actual->getAddress());
			//if ($actualAddress!=null){
			//	$coords=retrievePoint($actualAddress);
			//	if ($coords!=null)
			//		$actual->setCoordinates($coords);
			//}
			$handler($actual);
			$actual=new Library($uri, $name, $address);
			if (isset($note))
				$actual->addNote($note);
		}
	}
}


define("LIBRARIES_QUERY", "PREFIX crm: <http://erlangen-crm.org/120111/>
PREFIX oai: <http://www.openarchives.org/OAI/2.0/>
 
select ?r ?name ?address ?note where{
?r crm:P2_has_type <http://culturaitalia.it/pico/thesaurus/4.1#biblioteche> .
?r crm:P1_is_identified_by ?i .
?i a oai:E82_Actor_Appellation .
?i rdf:value ?name .
?r crm:P74_has_current_or_former_residence ?pr .
?pr rdf:value ?address .
 
OPTIONAL{
?r crm:P3_has_note ?nr .
?nr rdf:value ?note.
}
}");
define("MiBAC_ENDPOINT","http://dati.culturaitalia.it/sparql/");

/**
 * Process all the libraries in the MiBAC dataset.
 *
 * @param hadler a function with a Library instance as solely parameter, which will perform the processing of the
 * retrieved libraries.
 */
function processLibraries($handler){
	$data = sparql_get(MiBAC_ENDPOINT, LIBRARIES_QUERY);
	if(!isset($data)) die(sparql_errno() . ": " . sparql_error()); 
	process($data, $handler);
}
?>