<?php
class CharImageFeatureStudy
{
	private $_charNumArr	= array();

	private $_charPosLineTypeNumArr	= array();

	private $_charPosLineTypePerArr	= array();

	private $_isHandlePer	= false;

	private $_maxWidth	= 0;

	private $_minWidth	= null;

	private $_maxHeight	= 0;

	private $_minHeight	= null;

	public function __construct()
	{
	}

	public function addSample($char, CharImageFeature $charImgFeature)
	{
		@$this->_charNumArr[$char]++;
		$PosLineTypeNumArr	= array();
		foreach ($charImgFeature->getPosLineTypes() as $position => $lineTypeSeq) {
			@$this->_charPosLineTypeNumArr[$char][$position][$lineTypeSeq]++;
		}

		if ($charImgFeature->getWidth() > $this->_maxWidth) {
			$this->_maxWidth	= $charImgFeature->getWidth();
		}

		if ($this->_minWidth === null || $charImgFeature->getWidth() < $this->_minWidth) {
			$this->_minWidth	= $charImgFeature->getWidth();
		}

		if ($charImgFeature->getHeight() > $this->_maxHeight) {
			$this->_maxHeight	= $charImgFeature->getHeight();
		}

		if ($this->_minHeight === null || $charImgFeature->getHeight() < $this->_minHeight) {
			$this->_minHeight	= $charImgFeature->getHeight();
		}
	}

	public function getMaxWidth()
	{
		return $this->_maxWidth;
	}

	public function getMinWidth()
	{
		return $this->_minWidth;
	}

	public function getMaxHeight()
	{
		return $this->_maxHeight;
	}

	public function getMinHeight()
	{
		return $this->_minHeight;
	}

	private function _handlePercent()
	{
		foreach ($this->_charPosLineTypeNumArr as $char => $positionLineTypeSeqNumArr) {
			foreach ($positionLineTypeSeqNumArr as $position => $lineTypeSeqNumArr) {
				foreach ($lineTypeSeqNumArr as $lineTypeSeq => $num) {
					$this->_charPosLineTypePerArr[$char][$position][$lineTypeSeq]	= $num / $this->_charNumArr[$char];
				}
			}
		}	

		$this->_isHandlePer	= true;
	}

	public function loadCache($cacheFilePath)
	{
		$cacheData	= unserialize(file_get_contents($cacheFilePath));
		$fields	= array(
			'charNumArr', 'charPosLineTypeNumArr', 'charPosLineTypePerArr', 'isHandlePer', 'maxWidth', 'minWidth', 'maxHeight', 'minHeight',	
		);

		foreach ($fields as $field) {
			$property	= "_" . $field;
			$this->{$property}	= $cacheData[$field];
		}
	}

	public function saveCache($cacheFilePath)
	{
		$fields	= array(
			'charNumArr', 'charPosLineTypeNumArr', 'charPosLineTypePerArr', 'isHandlePer', 'maxWidth', 'minWidth', 'maxHeight', 'minHeight',	
		);

		$cacheData	= array();
		foreach ($fields as $field) {
			$property	= "_" . $field;
			$cacheData[$field]	= $this->{$property};
		}
		file_put_contents($cacheFilePath, serialize($cacheData));
	}

	public function recognize(CharImageFeature $imageFeature)
	{
		if (!$this->_isHandlePer) {
			$this->_handlePercent();
		}
	//	print_r($this->_charPosLineTypePerArr);
	//	exit;

		$maxScore	= 0;
		$similarChar	= '';

		$willRecImagePosLineType	= $imageFeature->getPosLineTypes();
		print_r($willRecImagePosLineType);
		foreach ($this->_charPosLineTypePerArr as $char => $positionLineTypePerArr) {
			$score	= null;
			foreach ($positionLineTypePerArr as $position => $lineTypeSeqPerArr) {
				$willRecImageLineTypeSeq	= $willRecImagePosLineType[$position];	
				if (isset($lineTypeSeqPerArr[$willRecImageLineTypeSeq])) {
					if ($score === null) {
						$score	= $lineTypeSeqPerArr[$willRecImageLineTypeSeq];
					} else {
						$score	*= $lineTypeSeqPerArr[$willRecImageLineTypeSeq];
					}
				} else {
					$score	*= 0.05;
				}
			}

			if ($score > $maxScore) {
				$similarChar	= $char;
				$maxScore	= $score;
			}
/*
			print_r($positionLineTypePerArr);
			echo $char . ":" . $score . "\n";
 */
		}

		return $similarChar;
	}
}
