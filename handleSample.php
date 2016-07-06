<?php
require_once 'Cutter.php';
require_once 'ToTwoValuer.php';
$cutter	= new Cutter;
$toTwoValuer	= new ToTwoValuer;

$sampleDir	= '../tmp';
$dirRes	= opendir($sampleDir);
$ci	= 0;
$dirNum	= 0;
$totalCaptchaNum	= 4;
while (false !== ($filename = readdir($dirRes))) {
	if ($filename != '.' && $filename != '..') {
		$imageRes	= imagecreatefrompng($sampleDir . '/' . $filename);	
		$twoValueImageRes	= $toTwoValuer->convertImageToTwoValue($imageRes);
		imagejpeg($twoValueImageRes, '/tmp/test.jpg');
		$twoValueImageRes	= imagecreatefromjpeg('/tmp/test.jpg');
		$jpegResArr = $cutter->cutImageToMultiJpeg($twoValueImageRes);
		$totalSw	= 0;
		foreach ($jpegResArr as $i => $jpegRes) {
			$sw	= imagesx($jpegRes);
			$totalSw	+= $sw;
		}

		foreach ($jpegResArr as $i => $jpegRes) {
			$sw	= imagesx($jpegRes);
			$num	= round($sw * $totalCaptchaNum / $totalSw);
			if ($num == 1) {
				if ($ci % 500 == 0) {
					$dirNum++;
					mkdir('../tmp_willreg/' . $dirNum);
					mkdir('../tmp_willreg/' . $dirNum . '/samples');
					for ($i = 0; $i < 10; $i++) {
						mkdir('../tmp_willreg/' . $dirNum . '/' . $i);
					}
					for ($i = 65; $i < 65 + 26; $i++) {
						mkdir('../tmp_willreg/' . $dirNum . '/' . chr($i));
					}
					for ($i = 97; $i < 97 + 26; $i++) {
						mkdir('../tmp_willreg/' . $dirNum . '/' . chr($i));
					}
				}

				imagejpeg($jpegRes, '../tmp_willreg/' . $dirNum . '/samples/' . $ci . '.jpg');
//				imagejpeg($twoValueImageRes, '../tmp_willreg/' . $dirNum . '/samples/' . $ci . '_tv.jpg');
				copy($sampleDir . '/' . $filename, '../tmp_willreg/' . $dirNum . '/samples/' . $ci . '_ori.jpg');
				$ci++;
			}
		}
	}
}
