<?php

function getContainerName() {
	exec("hostname",$hostOut) ;
	$cName = $hostOut[0]."_";	
	exec("pwd",$pwdOut);
//	pr($pwdOut);
	if(sizeof($pwdOut) != 1) {
		pr("problem with pwd: ");
		pr($pwdOut);
	}
	$wdPieces = explode("/",$pwdOut[0]) ;
//starts with a slash so ignore firs
array_shift($wdPieces);
$cName .= array_shift($wdPieces);
foreach($wdPieces as $dirName) {
	$dirName = strtolower($dirName) ;
	$cName .= ucfirst($dirName);
}
return $cName ;
	
}

function pr($input) {
	if(DEBUG) {
		if(php_sapi_name() != "cli") {
			echo "<pre>";
		}
		print_r($input);

		if(php_sapi_name() != "cli") {
                        echo "</pre>";
                }  else {
		echo "\n" ;
		}
	}
}
?>
