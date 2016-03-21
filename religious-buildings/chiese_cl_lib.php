<?php
/**
 * To retrieve all the religious buildings from the MiBAC SPARQL endpoint. 
 * This dataset should conform the CIDOC-CRM data model.
 *
 * Copyright 2016 Cristiano Longo
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

ini_set('display_errors',1); 
ini_set('max_execution_time',300); 
ini_set("memory_limit","1000M");
error_reporting(E_ALL);
require("sparqllib.php");

/**
 * A class representing a library. 
 */
class POI{
	private $uri;
	private $name;
	private $address;
	private $notes = array();
	
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
			$actual=new POI($uri, $name, $address);
			if (isset($note))
				$actual->addNote($note);
		} else if ($uri==$actual->getURI()){
			if (preg_match("/- Italia$/", $address))
				$actual->setAddress($address);
			if (isset($note))
				$actual->addNote($note);
		} else{
			$actualAddress=str_replace('-',',',$actual->getAddress());
			$handler($actual);
			$actual=new POI($uri, $name, $address);
			if (isset($note))
				$actual->addNote($note);
		}
	}
}


define("QUERY", 'PREFIX crm: <http://erlangen-crm.org/120111/>
PREFIX oai: <http://www.openarchives.org/OAI/2.0/>
 
select ?r ?name ?address ?note where{
	?r crm:P2_has_type <http://culturaitalia.it/pico/thesaurus/4.1#edifici_religiosi> .
	?r crm:P1_is_identified_by ?i .
	?i a crm:E35_Title .
	?i rdf:value ?name .
	?r crm:P53_has_former_or_current_location  ?pr .
	?pr rdf:value ?address .
        FILTER regex(str(?address), "^[A-Z].*-") .
	
	OPTIONAL{
		?r crm:P3_has_note ?nr .
		?nr rdf:value ?note.
	}
}');
define("MiBAC_ENDPOINT","http://dati.culturaitalia.it/sparql/");

/**
 * Process all the libraries in the MiBAC dataset.
 *
 * @param hadler a function with a Library instance as solely parameter, which will perform the processing of the
 * retrieved libraries.
 */
function processPOIs($handler){
	$data = sparql_get(MiBAC_ENDPOINT, QUERY);
	if(!isset($data)) die(sparql_errno() . ": " . sparql_error()); 
	process($data, $handler);
}
?>