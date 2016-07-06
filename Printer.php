<?php
class Printer
{
	public function printImageToString($jpegRes)
	{
		$width	= imagesx($jpegRes);
		$height	= imagesy($jpegRes);

		for ($y = 0; $y < $height; $y++) {
			for ($x = 0; $x < $width; $x++) {
				$color	= imagecolorat($jpegRes, $x, $y);
				if ((($color >> 16) & 0xFF) < 125) {
					echo '1';
				} else {
					echo ' ';
				}
			}
			echo "\n";
		}
	}
}
