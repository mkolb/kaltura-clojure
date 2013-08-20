<?php

require_once("ClientGeneratorFromXml.php");
require_once("ClojureClientGenerator.php");

$clientName = 'clojure';

//get latest schema file from kaltura.com
$contents = file_get_contents("http://www.kaltura.com/api_v3/api_schema.php");
file_put_contents('schema.xml', $contents);

//create client generator instance passing local path to saved schema file
$clientGenerator = new ClojureClientGenerator('schema.xml');

//generate client library
$clientGenerator->generate();
//add static output files of client library
$outputFiles = $clientGenerator->getOutputFiles();

//save created client library
$outputPath = "output".DIRECTORY_SEPARATOR.$clientName;
if (!is_dir($outputPath))
	mkdir($outputPath, "0777", true);

$total = count($outputFiles);
$i = 0;
//loop thriugh the files and save on the disk
foreach($outputFiles as $file => $data)
{
	$filePath = realpath($outputPath).DIRECTORY_SEPARATOR.$file;
	$dirName = pathinfo($filePath, PATHINFO_DIRNAME);
	if (!file_exists($dirName))
		mkdir($dirName, "0777", true);
	echo 'File: '.(++$i).' out of '.$total."\r\n";
	file_put_contents($filePath, $data);
}
//delete the temporary schema xml file
unlink('schema.xml');