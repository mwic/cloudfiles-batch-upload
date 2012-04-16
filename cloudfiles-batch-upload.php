<?php
define("CBU_DIR","/home/mcrouch/workspace/cloudfiles-batch-upload/" );
//set to true if you want dirs like ".git" and ".svn" included
define("BACKUP_HIDDEN_DIRS",0) ;

$path = CBU_DIR . 'lib/';
set_include_path(get_include_path() . PATH_SEPARATOR . $path);
$path = CBU_DIR . 'lib/rs';
set_include_path(get_include_path() . PATH_SEPARATOR . $path);
/**
 * Define these variables!
 */

// The directory to walk
$baseDir = isset($argv[1])
?$argv[1]
:".";
// Your Cloud Files username
$username = '';
// Your Cloud Files API key
$apiKey = '';



// The container to store objects in
$defaultContainer = 'test';
define("DEBUG",1) ;
require('cloudfiles.php');
require_once("util.php") ;
$destination = getContainerName() ;
$defaultContainer = $destination ;
pr("sending to container : ".$destination );
/*
 * You shouldn't have to touch anything below here.
 */

if( empty($baseDir) || empty($username) || empty($apiKey) || empty($defaultContainer) )
	die("You must set the required variables.\n");
// Sanitize base directory
if( !is_dir($baseDir) )
	die("Base directory is not actually a directory.\n");
if( substr($baseDir, 0, -1) !== '/' )
	$baseDir .= '/';

// Establish a connection to Cloud Files
$cfAuth = new CF_Authentication($username, $apiKey);
try {
	$cfAuth->authenticate();
	$cfConn = new CF_Connection($cfAuth);
	$cfContainer = $cfConn->create_container($defaultContainer);
} catch( Exception $e) {
	die(sprintf("%s: %s\n", get_class($e), $e->getMessage()));
}

// Walk the directory
walkDirectory($baseDir, '/');

// Cleanup
$cfConn->close();


function walkDirectory($baseDir, $workingDir) {
// Sanitize the working directory
if( empty($workingDir) || $workingDir === '/' ) {
$workingDir = '';
} else {
// Remove leading slash
if( substr($workingDir, 0, 1) === '/' )
$workingDir = substr($workingDir, 1);
// Add trailing slash
if( substr($workingDir, -1, 1) !== '/' )
$workingDir .= '/';
}

$dirStructure = scandir($baseDir . $workingDir);
foreach( $dirStructure as &$entry ) {
// Ignore current and parent directories
if( $entry === '.' || $entry === '..' )
continue;
if( !BACKUP_HIDDEN_DIRS && substr($entry,0,1) == ".") {
pr("skip hidden: ".$entry) ;
continue;
}

if( is_dir($baseDir . $workingDir . $entry) ) {
// If the entry is a directory, walk it...
walkDirectory($baseDir, $workingDir . $entry);
} else {
// ... otherwise upload the file
global $cfContainer;
$objectName = sprintf('%s%s', $workingDir, $entry);
try {
	$cfContainer->get_object($objectName) ;
echo "$objectName already there " ;
}catch (Exception $e) {
	echo " creating $objectName ";
	$cfObject = $cfContainer->create_object($objectName);
	try {
	$cfObject->load_from_filename($baseDir . $objectName);
	// Null the object so it can be GC'd sooner
	$cfObject = null;
	} catch( Exception $e ) {
		die(sprintf("%s: %s\n", get_class($e), $e->getMessage()));
	}
}
}
}
}

//timewasted's original fxn -- does not check for existing .. could be useful for something else?
function originalWalkDirectory($baseDir, $workingDir) {
	// Sanitize the working directory
	if( empty($workingDir) || $workingDir === '/' ) {
		$workingDir = '';
	} else {
		// Remove leading slash
		if( substr($workingDir, 0, 1) === '/' )
			$workingDir = substr($workingDir, 1);
		// Add trailing slash
		if( substr($workingDir, -1, 1) !== '/' )
			$workingDir .= '/';
	}

	$dirStructure = scandir($baseDir . $workingDir);
	foreach( $dirStructure as &$entry ) {
		// Ignore current and parent directories
		if( $entry === '.' || $entry === '..' )
			continue;

		if( is_dir($baseDir . $workingDir . $entry) ) {
			// If the entry is a directory, walk it...
			walkDirectory($baseDir, $workingDir . $entry);
		} else {
			// ... otherwise upload the file
			global $cfContainer;
			$objectName = sprintf('%s%s', $workingDir, $entry);
			$cfObject = $cfContainer->create_object($objectName);
			try {
				$cfObject->load_from_filename($baseDir . $objectName);
				// Null the object so it can be GC'd sooner
				$cfObject = null;
			} catch( Exception $e ) {
				die(sprintf("%s: %s\n", get_class($e), $e->getMessage()));
			}
		}
	}
}

