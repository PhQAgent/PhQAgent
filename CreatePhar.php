<?php
$DIRN = 'src';
$pharPath = "PhQAgent.phar";
$phar = new Phar($pharPath);
$phar->setStub('<?php require_once("phar://". __FILE__ ."/PhQAgent.php");  __HALT_COMPILER();');
$phar->setSignatureAlgorithm(Phar::SHA1);
$phar->startBuffering();
$filePath=__DIR__ ."/$DIRN";
foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($filePath)) as $file){
	$path = ltrim(str_replace(["\\", $filePath], ["/", ""], $file), "/");
	$filePath_r = str_replace("\\","/",$filePath)."/";
	$path = str_replace($filePath_r,"",$path);
	if($path{0} === "." or strpos($path, "/.") !== false){
		continue;
	}
	$phar->addFile($file, $path);
	echo "Processing: $path\n";
}
foreach($phar as $file => $finfo){
	if($finfo->getSize() > (1024 * 512)){
		$finfo->compress(\Phar::GZ);
	}
}
$phar->stopBuffering();
echo "Phar Created at $pharPath \n";