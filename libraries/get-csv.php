<?php
/**
 * Generate a CSV file with all the libraries retrieved by the
 * culturaitalia dataset.
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
 */
require("libraries_cl_lib.php");

function printLibrary($l){
	$uri=$l->getURI();
	$name=$l->getName();
	$address=$l->getAddress();
	$notes=$l->getNotes();
	echo "$uri\t$name\t$address\t";
	foreach($notes as $note){
		echo "\"$note\" ";
	}
	echo "\n";
}
header('Content-type: test/csv; charset=UTF-8');

processLibraries("printLibrary");
?>
