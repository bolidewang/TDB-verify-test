<?php
define('POSITION_LEFT', 'l');
define('POSITION_LEFT_TOP', 'lt');
define('POSITION_LEFT_BOTTOM', 'lb');
define('POSITION_RIGHT', 'r');
define('POSITION_RIGHT_TOP', 'rt');
define('POSITION_RIGHT_BOTTOM', 'rb');
define('POSITION_TOP', 't');
define('POSITION_TOP_LEFT', 'tl');
define('POSITION_TOP_RIGHT', 'tr');
define('POSITION_BOTTOM', 'b');
define('POSITION_BOTTOM_LEFT', 'bl');
define('POSITION_BOTTOM_RIGHT', 'br');

define('CUT_LENGTH', 1 / 3);
define('CUT_WIDTH', 2 / 3);


define('RIGHT_THRESHOLD', 2);
define('LEFT_THRESHOLD', 2);
define('MUTATION_THRESHOLD', 3);
define('MIN_LINE_PNUM', 3);

define('RIGHT_DS', 1);
define('MIDDLE_DS', 0);
define('LEFT_DS', -1);

define('LINE_RIGHT', 'R');
define('LINE_LEFT', 'L');
define('LINE_STRIGHT', 'S');
define('LINE_CIRCLE', 'C');
define('LINE_NONE', 'N');

require_once 'CharImageFeature.php';
require_once 'CharImageFeatureStudy.php';
$charImgFeaStudy	= new CharImageFeatureStudy();

$allCharPositionLineTypePerArr	= array();
$singleDir	= 'samples/single';
$singleDirRes	= opendir($singleDir);
while (false !== ($charDirName = readdir($singleDirRes))) {
	if ($charDirName == '.' || $charDirName == '..') {
		continue;
	}
	$char	= strlen($charDirName) == 2 ? substr($charDirName, 0, 1) : $charDirName;
	$charDir	= $singleDir . '/' . $charDirName; 

	$charDirRes	= opendir($charDir);
	$jpgFileNum	= 0;
	$posLineTypeNumArr	= array();
	while (false !== ($jpgFilename = readdir($charDirRes))) {
		if ($jpgFilename == '.' || $jpgFilename == '..') {
			continue;
		}

		$imageRes	= imagecreatefromjpeg($charDir . '/' . $jpgFilename);
		
		$jpgFileNum++;
		$charImageFeature	= calImageFeature($imageRes);

		$charImgFeaStudy->addSample($char, $charImageFeature);

		
/*
		if ($char == 's') {
			require_once 'Printer.php';
			$printer	= new Printer;
			$printer->printImageToString($imageRes);
			print_r($charImageFeature);
		}
 */
	}
	
	
//	if ($char == 'd') {
//		print_r($allCharPositionLineTypePerArr[$char]);
//		exit;
//	}

}
echo 'max width : ' . $charImgFeaStudy->getMaxWidth() . "\n";
echo 'min width : ' . $charImgFeaStudy->getMinWidth() . "\n";
echo 'max height: ' . $charImgFeaStudy->getMaxHeight() . "\n";
echo 'min height : ' . $charImgFeaStudy->getMinHeight() . "\n";
exit;

$singleDir	= '/home/chenlong/downloads/wcc/3/';'samples/single';
$singleDirRes	= opendir($singleDir);
$allCharSuccessRateArr	= array();
while (false !== ($charDirName = readdir($singleDirRes))) {
	if ($charDirName == '.' || $charDirName == '..') {
		continue;
	}
	if ($charDirName != 'q_') {
//continue;
	}
	$curChar	= strlen($charDirName) == 2 ? substr($charDirName, 0, 1) : $charDirName;
	$charDir	= $singleDir . '/' . $charDirName; 

	$charDirRes	= opendir($charDir);
	$jpgFileNum	= 0;
	$posLineTypeNumArr	= array();
	$successNum	= 0;
	while (false !== ($jpgFilename = readdir($charDirRes))) {
		if ($jpgFilename == '.' || $jpgFilename == '..') {
			continue;
		}

		$imageRes	= imagecreatefromjpeg($charDir . '/' . $jpgFilename);
		$jpgFileNum++;
		$imageFeature	= calImageFeature($imageRes);
		
		$similarChar	= $charImgFeaStudy->recognize($imageFeature);


		if (strtolower($similarChar) == strtolower($curChar)) {
			$successNum++;	
		} else {
			echo $curChar . " not a " . $similarChar . "\n";
		}
	}
	
	$allCharSuccessRateArr[$curChar]	= $successNum / $jpgFileNum;
}
print_r($allCharSuccessRateArr);

function calImageFeature($imageRes)
{
	$width	= imagesx($imageRes);
	$height	= imagesy($imageRes);

	$ylx	= ceil($width * (1 - CUT_LENGTH));
	$yrx	= ceil($width * CUT_LENGTH);
	$ty		= ceil($height * CUT_LENGTH);
	$by		= ceil($height * (1 - CUT_LENGTH));
	$xty	= ceil($height * (1 - CUT_LENGTH));
	$xby	= ceil($height * CUT_LENGTH);
	$lx		= ceil($width * CUT_LENGTH);
	$rx		= ceil($width * (1 - CUT_LENGTH));

	$lk	= array(
		POSITION_LEFT_TOP	=> array(),
		POSITION_LEFT_BOTTOM	=> array(),
		POSITION_RIGHT_TOP	=> array(),
		POSITION_RIGHT_BOTTOM	=> array(),
		POSITION_TOP_LEFT	=> array(),
		POSITION_TOP_RIGHT	=> array(),
		POSITION_BOTTOM_LEFT	=> array(),	
		POSITION_BOTTOM_RIGHT	=> array(),	
		/*
		POSITION_LEFT	=> array(),
		POSITION_RIGHT	=> array(),
		POSITION_TOP	=> array(),
		POSITION_BOTTOM	=> array(),
		 */
	);
	for ($y = 0; $y < $height; $y++) {
		$minX = $maxX = null;
		for ($x = 0; $x < $width; $x++) {
			if (isImageXYHavePixel($imageRes, $x, $y)) {
				if ($minX === null) {
					$minX	= $x;
				}
				
				if ($maxX === null || $maxX < $x) {
					$maxX	= $x;
				}	
				 
				$ytPosArr = $ybPosArr = array();
				if ($x < $ylx) {
					$ytPosArr[]	= POSITION_TOP_LEFT;
					$ybPosArr[]	= POSITION_BOTTOM_LEFT;
				} 

				if ($x >= $yrx) {
					$ytPosArr[]	= POSITION_TOP_RIGHT;
					$ybPosArr[]	= POSITION_BOTTOM_RIGHT;
				}

				foreach ($ytPosArr as $ytPos) {
					if (!isset($lk[$ytPos][$x]) || $lk[$ytPos][$x] > $y) {
						if ($y < $ty) {
							$lk[$ytPos][$x]	= $y;
						} else {
							$lk[$ytPos][$x]	= null;
						}
					}
				}

				if (!isset($lk[POSITION_TOP][$x]) || $lk[POSITION_TOP][$x] > $y) {
					if ($y < $ty) {
						$lk[POSITION_TOP][$x]	= $y;
					} else {
						$lk[POSITION_TOP][$x]	= null;
					}
				}

				foreach ($ybPosArr as $ybPos) {
					if (!isset($lk[$ybPos][$x]) || $lk[$ybPos][$x] < $y) {
						if ($y >= $by) {
							$lk[$ybPos][$x]	= $y; 
						} else {
							$lk[$ybPos][$x]	= null;
						}
					}
				}

				if (!isset($lk[POSITION_BOTTOM][$x]) || $lk[POSITION_BOTTOM][$x] > $y) {
					if ($y >= $by) {
						$lk[POSITION_BOTTOM][$x]	= $y;
					} else {
						$lk[POSITION_BOTTOM][$x]	= null;
					}
				}
			}
		}

		$xlPosArr = $xrPosArr = array();
		if ($y < $xty) {
			$xlPosArr[]	= POSITION_LEFT_TOP;
			$xrPosArr[]	= POSITION_RIGHT_TOP;
		}

		if ($y >= $xby) {
			$xlPosArr[]	= POSITION_LEFT_BOTTOM;
			$xrPosArr[]	= POSITION_RIGHT_BOTTOM;
		}

		foreach ($xlPosArr as $xlPos) {
			if ($minX < $lx) {
				$lk[$xlPos][$y]	= $minX;
			} else {
				$lk[$xlPos][$y]	= null; 
			}
		}


		if ($minX < $lx) {
			$lk[POSITION_LEFT][$y]	= $minX;
		} else {
			$lk[POSITION_LEFT][$y]	= null;
		}
	

		foreach ($xrPosArr as $xrPos) {
			if ($maxX >= $rx) {
				$lk[$xrPos][$y]	= $maxX;
			} else {
				$lk[$xrPos][$y]	= null; 
			}
		}

		
		if ($maxX >= $rx) {
			$lk[POSITION_RIGHT][$y]	= $maxX;
		} else {
			$lk[POSITION_RIGHT][$y]	= $maxX;
		}
		
	}

	$imageFeature	= new CharImageFeature;
	$imageFeature->setPosLineTypes(calPosLineTypes($lk, $width, $height));
	$imageFeature->setWidth($width);
	$imageFeature->setHeight($height);
	return $imageFeature;
}

function calPosLineTypes($lk, $width, $height)
{
	$positionMaps	= array(
		POSITION_LEFT_TOP	=> array('y', 'p'),
		POSITION_LEFT_BOTTOM	=> array('y', 'n'),
		POSITION_RIGHT_TOP	=> array('y', 'p'),
		POSITION_RIGHT_BOTTOM	=> array('y', 'n'),
		POSITION_TOP_LEFT	=> array('x', 'p'),
		POSITION_TOP_RIGHT	=> array('x', 'n'),
		POSITION_BOTTOM_LEFT	=> array('x', 'p'),
		POSITION_BOTTOM_RIGHT	=> array('x', 'n'),
		POSITION_LEFT	=> array('y', 'n'),
		POSITION_RIGHT	=> array('y', 'n'),
		POSITION_TOP	=> array('x', 'n'),
		POSITION_BOTTOM	=> array('x', 'n'),
	);

	$posLineTypes	= array();
	foreach ($positionMaps as $position => $coordinates) {
		$lines		= array();
		$lineNum	= 0;
		$lineDSArr	= array();
		$lastCoorVal	= null;

		$allNum		= $coordinates[0] == 'y' ? $height : $width;
		$start		= 0;$coordinates[1] == 'p' ? 0 : ceil($allNum * CUT_LENGTH);
		$end		= $allNum;$coordinates[1] == 'p' ? ceil($allNum * (1 - CUT_LENGTH)) : $allNum;
		for ($coor = $start; $coor < $end; $coor++) {
			if (!isset($lk[$position][$coor])) {
				continue;
			}

			if ($lastCoorVal === null) {
				$lastCoorVal	= $lk[$position][$coor];
				continue;
			}

			if ($lk[$position][$coor] !== null) {
				$DS	= $lk[$position][$coor] - $lastCoorVal;
			} else {
				$DS	= MUTATION_THRESHOLD;
			}
			$lastCoorVal	= $lk[$position][$coor];
			if (abs($DS) >= MUTATION_THRESHOLD) {
				$lineType	= calLineType($lineDSArr);
				if ($lineType) {
					$lines[$lineNum]	= $lineType;
					$lineNum++;
				}
				$lineDSArr	= array();
				continue;
			}

			if ($DS > 0) {
				@$lineDSArr[RIGHT_DS]++;
			} else if ($DS < 0) {
				@$lineDSArr[LEFT_DS]++;
			} else {
				@$lineDSArr[MIDDLE_DS]++;
			}
		}

		if (!isset($lines[$lineNum])) {
			$lineType	= calLineType($lineDSArr);
			if ($lineType) {
				$lines[$lineNum]	= $lineType;
			}
		}

		if ($lines) {
			$lineTypeSeq	= implode('', $lines);
			$posLineTypes[$position]	= $lineTypeSeq;
		} else {
			$posLineTypes[$position]	= LINE_NONE;
		}
	}	

	return $posLineTypes;
}

function calLineType($lineDSArr)
{
	$rightDSNum	= @$lineDSArr[RIGHT_DS] ? $lineDSArr[RIGHT_DS] : 0;
	$leftDSNum	= @$lineDSArr[LEFT_DS] ? $lineDSArr[LEFT_DS] : 0;
	$middleDSNum	= @$lineDSArr[MIDDLE_DS] ? $lineDSArr[MIDDLE_DS] : 0;
	if ($rightDSNum + $leftDSNum + $middleDSNum < MIN_LINE_PNUM) {
		return null;
	} else {
		if ($rightDSNum >= RIGHT_THRESHOLD && $leftDSNum >= LEFT_THRESHOLD) {
			return LINE_CIRCLE;
		} else if ($rightDSNum >= RIGHT_THRESHOLD) {
			return LINE_RIGHT;
		} else if ($leftDSNum >= LEFT_THRESHOLD) {
			return LINE_LEFT;
		} else {
			return LINE_STRIGHT;
		}	
	}
}

function isImageXYHavePixel($imageRes, $x, $y)
{
	$colorIndex	= imagecolorat($imageRes, $x, $y);
	return (($colorIndex >> 16) & 0xFF) < 125;
}
