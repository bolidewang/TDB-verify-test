<?php
class ToTwoValuer
{
	public function convertImageToTwoValue($imageRes)
	{
		$width	= imagesx($imageRes);
		$height	= imagesy($imageRes);

		$twoValueImageRes	= imagecreate($width, $height);
		imagecolorallocate($twoValueImageRes, 255, 255, 255);
		$blackColor	= imagecolorallocate($twoValueImageRes, 0, 0, 0);
		for ($y = 0; $y < $height; $y++) {
			for ($x = 0; $x < $width; $x++) {
				$index	= imagecolorat($imageRes, $x, $y);
				$color	= imagecolorsforindex($imageRes, $index);
				$gray	= ($color['red'] * 0.228 + $color['green'] * 0.587 + $color['blue'] * 0.114);
				if ($gray <= 135) {
					imagesetpixel($twoValueImageRes, $x, $y, $blackColor);	
				}
			}
		}

		return $twoValueImageRes;
	}
}
