<?php
class Recognition 
{
	private $_sampleImageResArr	= array();

	public function __construct(array $sampleImageResArr)
	{
		$this->_sampleImageResArr	= $sampleImageResArr;
	}

	public function recognize($imageRes)
	{
		$imWidth	= imagesx($imageRes);
		$imHeight	= imagesy($imageRes);

		$similarScoreArr	= array();
		$maxSimilarScore= 0; 
		$maxSimilarChar	= '';
		foreach ($this->_sampleImageResArr as $char => $sampleImgRes) {
			$sampleWidth	= imagesx($sampleImgRes);
			$sampleHeight	= imagesy($sampleImgRes);
	
			if ($imWidth != $sampleWidth || $imHeight != $sampleHeight) {
				$tmpImgRes	= imagecreate($imWidth, $imHeight);
				imagecopyresized($tmpImgRes, $sampleImgRes, 0, 0, 0, 0, $imWidth, $imHeight, $sampleWidth, $sampleHeight);
				imagejpeg($tmpImgRes, 'tmp/tmp.jpg');
				$sampleImgRes	= imagecreatefromjpeg('tmp/tmp.jpg');
			}	
			
			
			require_once 'Printer.php';
			$printer	= new Printer();
			$printer->printImageToString($imageRes);
			echo "\n";
			$printer->printImageToString($sampleImgRes);
			echo "\n";

			$score	= 0;
			for ($y = 0; $y < $imHeight; $y++) {
				for ($x = 0; $x < $imWidth; $x++) {
					$cmpHavePixel	= $this->_checkIsHavePixel($imageRes, $x, $y);
					$sampleHavePixel	= $this->_checkIsHavePixel($sampleImgRes, $x, $y);
					if ($cmpHavePixel && $sampleHavePixel) {
						$score++;
						echo '1';
					} else if (!$cmpHavePixel && $sampleHavePixel) {
						$score--;
						echo '-';
					} else {
						echo ' ';
					}
				}
				echo "\n";
			}
			echo "\n\n";	

			$similarScoreArr[$char]	= $score;
			if ($similarScoreArr[$char] > $maxSimilarScore) {
				$maxSimilarScore	= $similarScoreArr[$char];
				$maxSimilarChar	= (string) $char;
			}
		}
		print_r($similarScoreArr);
		
		return $maxSimilarChar;
	}	

	private function _checkIsHavePixel($imageRes, $x, $y)
	{
		$colorIndex	= imagecolorat($imageRes, $x, $y);
		return (($colorIndex >> 16) & 0xFF) < 125;
	}
}

$sampleImageResArr	= array();
$sampleDir	= 'samples/tb_samples';
$dirRes		= opendir($sampleDir);
while (false !== ($filename = readdir($dirRes))) {
	if ($filename != '.' && $filename != '..') {
		$imageRes	= imagecreatefromjpeg($sampleDir . '/' . $filename);
		$char	= substr($filename, 0, 1);
		$sampleImgRes[$char]	= $imageRes;
	}
}

$recognition	= new Recognition($sampleImgRes);
$imageRes	= imagecreatefromjpeg('tmp/2em2.jpg/0-1.jpg');
$whiteColor	= imagecolorallocate($imageRes, 255, 255, 255);
$imageRes	= imagerotate($imageRes, 30, $whiteColor);
require_once 'BlankCleaner.php';
$blankCleaner	= new BlankCleaner();
$imageRes	= $blankCleaner->cleanImageBlank($imageRes);
imagejpeg($imageRes, 'tmp/tmp.jpg');
$imageRes	= imagecreatefromjpeg('tmp/tmp.jpg');
$char	= $recognition->recognize($imageRes);
var_dump($char);
