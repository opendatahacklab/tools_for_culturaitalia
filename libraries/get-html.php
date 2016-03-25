<?php
/**
 * Generate an HTML page with all the libraries retrieved by the
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
	//$coords=$l->getCoordinates();
	echo "<tr><td>$uri</td><td>$name</td><td>$address</td><td>";
	echo "<ul>";
	foreach($notes as $note){
		echo "<li>$note</li>";
	}

	echo "</ul></td></tr>";
	
//	echo "</ul></td><td>";
//	if (isset($coords)){
//		echo "$coords->lat, $coords->long";
//	} else echo "-";
//	echo "</td></tr>\n";
}

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>opendatahacklab - Tools for CulturaItalia - Religious Buildings</title>
<link rel="stylesheet" type="text/css"
href="http://opendatahacklab.github.io/odhl.css" />
</head>
<body>
<header class="main-header">
<img class="logo"
src="http://opendatahacklab.github.io//imgs/logo_cog4_ter.png"
alt="the opendatahacklab logo" />
<h1>opendatahacklab - tools for culturaitalia - public libraries</h1>
<p class="subtitle">
THE OPEN DATA HACKING LABORATORY - Powered by <a
href="http://github.com"><img alt="GitHub"
src="http://opendatahacklab.github.io/imgs/GitHub_Logo.png" /></a>
</p>
<nav>
<ol class="breadcrumb">
	<li><a href="http://opendatahacklab.github.io/index.html">home</a></li>
	<li><a href="http://opendatahacklab.github.io/projects.html">projects</a></li>
	<li><a href="../index.html">tools for culturaitalia</a></li>
	<li>libraries</li>
</ol>
<a
href="https://github.com/opendatahacklab/tools_for_culturaitalia.git"
alt="Source Code" title="GitHub repository"><img
src="http://opendatahacklab.github.io/imgs/GitHub-Mark-64px.png" /></a>
<a href="http://dati.culturaitalia.it/sparql/"><img
src="http://opendatahacklab.github.io/imgs/rdf.png" /></a>
</nav>
</header>
<section>
<?php 
echo "<table>\n";
processLibraries("printLibrary");
echo "</table>\n";
?> 
</section>
</body>
</html>