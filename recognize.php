<?php
require_once 'studySample.php';

$filename	= 'samples/picture/2em2.png';
$imageRes	= imagecreatefrompng($filename);

require_once 'ToTwoValuer.php';
$toTwoValuer	= new ToTwoValuer();
$twoValueImageRes	= $toTwoValuer->convertImageToTwoValue($imageRes);
imagejpeg($twoValueImageRes, 'tmp/tmp.jpg');
$twoValueImageRes	= imagecreatefromjpeg('tmp/tmp.jpg');

require_once 'Printer.php';
$printer	= new Printer();
$printer->printImageToString($twoValueImageRes);

require_once 'Cutter.php';
$cutter	= new Cutter;
$imageResArr	= $cutter->cutImageToMultiJpeg($twoValueImageRes);
foreach ($imageResArr as $imageRes) {
	imagejpeg($imageRes, 'tmp/tmp.jpg');
	$imageRes	= imagecreatefromjpeg('tmp/tmp.jpg');

	$printer->printImageToString($imageRes);

	$char	= $charImgFeaStudy->recognize(calImageFeature($imageRes));
	echo $char . "\n";
}
