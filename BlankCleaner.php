<?php
class BlankCleaner
{
	public function cleanImageBlank($imageRes)
	{
		$width	= imagesx($imageRes);
		$height	= imagesy($imageRes);

		$minx = $maxx = $miny = $maxy = null;
		for ($y = 0; $y < $height; $y++) {
			for ($x = 0; $x < $width; $x++) {
				$colorIndex	= imagecolorat($imageRes, $x, $y);
				$color	= imagecolorsforindex($imageRes, $colorIndex);
				if ($color['red'] < 125) {
					if ($minx === null) {
						$minx = $maxx = $x;
						$miny = $maxy = $y; 
					}
					if ($x < $minx) {
						$minx	= $x;
					}
					if ($x > $maxx) {
						$maxx	= $x;
					}
					if ($y < $miny) {
						$miny	= $y;
					}
					if ($y > $maxy) {
						$maxy	= $y;
					}
				}
			}
		}

		if ($minx === null) {
			return imagecreate(0, 0);
		} else {
			$noBlankImageRes	= imagecreate($maxx - $minx + 1, $maxy - $miny + 1);
			imagecopy($noBlankImageRes, $imageRes, 0, 0, $minx, $miny, $maxx - $minx + 1, $maxy - $miny + 1);
			return $noBlankImageRes;
		}
	}
}
