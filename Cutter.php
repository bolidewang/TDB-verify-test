<?php
class Cutter
{
	public function cutImageToMultiJpeg($jpegRes)
	{
		return $this->_cutImgToMultiJpeg($jpegRes);
	}

	private function _cutImgToMultiJpeg($imgRes)
	{
		$width	= imagesx($imgRes);
		$height	= imagesy($imgRes);

		$lastColsKey	= array(0, -1, 1);
		$qiepianArr	= array();
		$curBiaoq	= 0;
		$biaoqGroupArr	= array();
		$groupBiaoqArr	= array();
		$curGroupNum	= 0;
		// 切片分组打标签
		$isBlank	= true;
		for ($x = 0; $x < $width; $x++) {
			$cols	= array();
			if ($isBlank) {
				$lastQiepianCols	= array();
			} else {
				$isBlank	= true;
				$lastQiepianCols	= end($qiepianArr);
			}

			for ($y = 0; $y < $height; $y++) {
				$color	= imagecolorat($imgRes, $x, $y);
				if ($this->_checkPixelIsAValidColor($color)) {
					if (empty($cols[$y - 1])) {
						$curBiaoq++;
						$biaoqGroupArr[$curBiaoq]	= 0;
					}
					$cols[$y]	= $curBiaoq;

					foreach ($lastColsKey as $lastColKey) {
						$yKey	= $lastColKey + $y;
						if (!empty($lastQiepianCols[$yKey])) {
							$lastQiepianColBiaoq	= $lastQiepianCols[$yKey];
							$lastQiepianColGroup	= $biaoqGroupArr[$lastQiepianColBiaoq];

							$curGroup	= $biaoqGroupArr[$curBiaoq];
							if (!$curGroup && !$lastQiepianColGroup) {
								$curGroupNum++;
								$groupBiaoqArr[$curGroupNum]	= array($curBiaoq, $lastQiepianColBiaoq);		
								$biaoqGroupArr[$curBiaoq]	= $curGroupNum;
								$biaoqGroupArr[$lastQiepianColBiaoq]	= $curGroupNum;
							} else if ($curGroup && !$lastQiepianColGroup) {
								$groupBiaoqArr[$curGroup][]	= $lastQiepianColBiaoq;
								$biaoqGroupArr[$lastQiepianColBiaoq]	= $curGroup;
							} else if (!$curGroup && $lastQiepianColGroup) {
								$groupBiaoqArr[$lastQiepianColGroup][]	= $curBiaoq;
								$biaoqGroupArr[$curBiaoq]	= $lastQiepianColGroup;
							} else {
								if ($curGroup == $lastQiepianColGroup) {
									continue;
								}
								foreach ($groupBiaoqArr[$curGroup] as $biaoq) {
									$biaoqGroupArr[$biaoq]	= $lastQiepianColGroup;
									$groupBiaoqArr[$lastQiepianColGroup][]	= $biaoq;
								}
								unset($groupBiaoqArr[$curGroup]);
							}

							if ($yKey == 0) {
								break;
							}
						}
					}
					$isBlank	= false;
				} else {
					$cols[$y]	= 0;
				}
			}
			
			if (!$isBlank) {
				$qiepianArr[]	= $cols;
			}
		}	

		$noBlankWidth	= count($qiepianArr);
		$noBlankHeight	= count(current($qiepianArr));
		$groupQiePianArr	= array();
		$groupMaxAndMin		= array();
		// 切片分组合并
		for ($y = 0; $y < $noBlankHeight; $y++) {
			for ($x = 0; $x < $noBlankWidth; $x++) {
				$biaoq	= $qiepianArr[$x][$y];
				if ($biaoq) {
					$curGroup	= $biaoqGroupArr[$biaoq];
					foreach ($groupBiaoqArr as $group => $biaoqArr) {
						if ($curGroup == $group) {
							$groupQiePianArr[$group][$y][$x]	= 1;
							
							// 计算最大最小边界
							if (!isset($groupMaxAndMin[$group])) {
								$groupMaxAndMin[$group]	= array(
									'minx'	=> $x,
									'maxx'	=> $x,	
									'miny'	=> $y,
									'maxy'	=> $y,	
								);
								continue;
							}
							if (!isset($groupMaxAndMin[$group]['minx']) || $x < $groupMaxAndMin[$group]['minx']) {
								$groupMaxAndMin[$group]['minx']		= $x;
							} 

							if (!isset($groupMaxAndMin[$group]['miny']) || $y < $groupMaxAndMin[$group]['miny']) {
								$groupMaxAndMin[$group]['miny']		= $y;
							} 

							if (!isset($groupMaxAndMin[$group]['maxx']) || $x > $groupMaxAndMin[$group]['maxx']) {
								$groupMaxAndMin[$group]['maxx']		= $x;
							} 

							if (!isset($groupMaxAndMin[$group]['maxy']) || $y > $groupMaxAndMin[$group]['maxy']) {
								$groupMaxAndMin[$group]['maxy']		= $y;
							} 
						} else {
							$groupQiePianArr[$group][$y][$x]	= 0;
						}
					}
				} else {
					foreach ($groupBiaoqArr as $group => $biaoqArr) {
						$groupQiePianArr[$group][$y][$x]	= 0;
					}
				}	
			}
		}

		$imgResArr	= array();
		foreach ($groupQiePianArr as $group => $maxtrix) {
			if (!isset($groupMaxAndMin[$group])) {
				continue;
			}

			$minx	= $groupMaxAndMin[$group]['minx'];
			$maxx	= $groupMaxAndMin[$group]['maxx'];
			$miny	= $groupMaxAndMin[$group]['miny'];
			$maxy	= $groupMaxAndMin[$group]['maxy'];
	
			$imgRes	= imagecreate($maxx - $minx + 1, $maxy - $miny + 1);
			imagecolorallocate($imgRes, 255, 255, 255);
			$blackColor	= imagecolorallocate($imgRes, 0, 0, 0);
			for ($y = $miny; $y <= $maxy; $y++) {
				for ($x = $minx; $x <= $maxx; $x++) {
					if ($maxtrix[$y][$x]) {
						imagesetpixel($imgRes, $x - $minx, $y - $miny, $blackColor);	
					}
				}
			}
			$imgResArr[]	= $imgRes;
		}

		return $imgResArr;
	}

	private function _checkPixelIsAValidColor($color)
	{
		$r	= ($color >> 16) & 0xFF;
		return $r < 125;
	}
}

$cutter	= new Cutter();

/*
$filePath	= 'samples/afterTwoValue/7gvq-2.jpg';
$jpegRes	= imagecreatefromjpeg($filePath);
require_once 'Printer.php';
$printer	= new Printer();
$printer->printJpegToString($jpegRes);
$jpegResArr	= $cutter->cutImageToMultiJpeg($jpegRes);
$file	= '7gvq-2.jpg';
foreach ($jpegResArr as $i => $jpegRes) {
	imagejpeg($jpegRes, 'tmp/' . $file . '/' . $i . '.jpg');
	$jpegRes	= imagecreatefromjpeg('tmp/' . $file . '/' . $i . '.jpg');
	$printer->printJpegToString($jpegRes);
}
exit;
require_once 'BlankCleaner.php';
$blankCleaner	= new BlankCleaner();

$twoValueDir	= 'tmp/afterTwoValue';
$dirRes	= opendir($twoValueDir);
$totalCaptchaNum	= 4;
while (false !== ($file = readdir($dirRes))) {
	if ($file != '.' && $file != '..') {
		$filePath	= $twoValueDir . '/' . $file;	
		$imageRes	= imagecreatefromjpeg($filePath);
		$jpegResArr	= $cutter->cutImageToMultiJpeg($imageRes);

		$tmpDir	= 'tmp/' . $file;
		if (!is_dir($tmpDir)) {
			mkdir($tmpDir);	
			$noBlankImageRes	= $blankCleaner->cleanImageBlank($imageRes);
			imagejpeg($noBlankImageRes, $tmpDir . '/_' . $file);
		}

		$totalSw	= 0;
		foreach ($jpegResArr as $i => $jpegRes) {
			$sw	= imagesx($jpegRes);
			$totalSw	+= $sw;
		}

		foreach ($jpegResArr as $i => $jpegRes) {
			$sw	= imagesx($jpegRes);
			$num	= round($sw * $totalCaptchaNum / $totalSw);
			imagejpeg($jpegRes, 'tmp/' . $file . '/' . $i . '-' . $num . '.jpg');
		}
	}
}
 */
