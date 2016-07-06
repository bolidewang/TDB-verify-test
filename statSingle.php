<?php
$singleDir	= 'samples/single';
$dirRes		= opendir($singleDir);
$areaFoundPer	= array();
$areaNum	= 4;
$filenum	= 0;

$everyCharToHeartDisPer	= array();
while (false !== ($filename = readdir($dirRes))) {
	if ($filename != '.' && $filename != '..') {
		$char	= strlen($filename) == 2 ? substr($filename, 0, 1) : $filename;

		$charDir	= $singleDir . '/' . $filename;
		$charDirRes	= opendir($charDir);

		$toHeartDisArr	= array();
		$jpgFileNum	= 0;
		while (false !== ($jpgFilename = readdir($charDirRes))) {
			if ($jpgFilename == '.' || $jpgFilename == '..') {
				continue;
			}

			$imageRes	= imagecreatefromjpeg($charDir . '/' . $jpgFilename);

			$width	= imagesx($imageRes);
			$height	= imagesy($imageRes);

			$heartPointX	= intval($width / 2) - 1;
			$heartPointY	= intval($height/ 2) - 1;

			$jpgFileNum++;
			for ($y = 0; $y < $height; $y++) {
				for ($x = 0; $x < $width; $x++) {
					if (isImageXYHavePixel($imageRes, $x, $y)) {
						$toHeartDis		= abs($x - $heartPointX) + abs($y - $heartPointY);
						if (($y - $heartPointY) == 0) {
							$angle	= 0;
						} else {
							$angle	= ($x - $heartPointX) / ($y - $heartPointY);
						}

						if (isset($toHeartDisArr[$toHeartDis])) {
							$toHeartDisArr[$toHeartDis]['num']++;
							$toHeartDisArr[$toHeartDis]['angle']	+= $angle;
						} else {
							$toHeartDisArr[$toHeartDis]	= array(
								'num'	=> 1,
								'angle'	=> $angle,			
							);
						}
					}
				}
			}
		}

		foreach ($toHeartDisArr as $distance => $data) {
			$toHeartDisArr[$distance]['num']	= $data['num'] / $jpgFileNum;
			$toHeartDisArr[$distance]['angle']	= $data['angle'] / $jpgFileNum;
		}

		$everyCharToHeartDisPer[$char]	= $toHeartDisArr;
	}
}

$dirRes	= opendir('samples/single');
while (false !== ($filename = readdir($dirRes))) {
	if ($filename != '.' && $filename != '..') {
		$char	= strlen($filename) == 2 ? substr($filename, 0, 1) : $filename;

		$charDir	= $singleDir . '/' . $filename;
		$charDirRes	= opendir($charDir);

		$filenum	= 0;
		$rightCheckNum	= 0;
		while (false !== ($jpgFilename = readdir($charDirRes))) {
			if ($jpgFilename == '.' || $jpgFilename == '..') {
				continue;
			}
			
			$filenum++;
			$checkChar	= checkChar($charDir . '/' . $jpgFilename);
			if ($char == $checkChar) {
				$rightCheckNum++;
			}
		}
	}
	echo "$char : $rightCheckNum / $filenum = " . ($rightCheckNum / $filenum). "\n";
}

function checkChar($checkFilePath)
{
	global $everyCharToHeartDisPer;

	$imageRes	= imagecreatefromjpeg($checkFilePath);

	$width	= imagesx($imageRes);
	$height	= imagesy($imageRes);

	$heartPointX	= intval($width / 2) - 1;
	$heartPointY	= intval($height/ 2) - 1;

	$checkToHeartDisArr	= array();
	for ($y = 0; $y < $height; $y++) {
		for ($x = 0; $x < $width; $x++) {
			if (isImageXYHavePixel($imageRes, $x, $y)) {
				$toHeartDis		= abs($x - $heartPointX) + abs($y - $heartPointY);
				if (($y - $heartPointY) == 0) {
					$angle	= 0;
				} else {
					$angle	= ($x - $heartPointX) / ($y - $heartPointY);
				}

				if (isset($checkToHeartDisArr[$toHeartDis])) {
					$checkToHeartDisArr[$toHeartDis]['num']++;
					$checkToHeartDisArr[$toHeartDis]['angle']	+= $angle;
				} else {
					$checkToHeartDisArr[$toHeartDis]	= array(
						'num'	=> 1,
						'angle'	=> $angle,
					);
				}
			}
		}
	}

	$minFc	= -1;
	$minChar	= '';
	foreach ($everyCharToHeartDisPer as $char => $toHeartDisArr) {
		$fc		= 0;
		foreach ($checkToHeartDisArr as $distance => $data) {
			if (isset($toHeartDisArr[$distance])) {
				$fc	+= pow($toHeartDisArr[$distance]['num'] - $data['num'], 2);
				$fc	+= pow($toHeartDisArr[$distance]['angle'] - $data['angle'], 2);
			}
		}
		
		if ($minFc < 0 || $minFc > $fc) {
			$minFc	= $fc;
			$minChar	= $char;
		}	
	}

	return $minChar;
}

function isImageXYHavePixel($imageRes, $x, $y)
{
	$colorIndex	= imagecolorat($imageRes, $x, $y);
	return (($colorIndex >> 16) & 0xFF) < 125;
}
