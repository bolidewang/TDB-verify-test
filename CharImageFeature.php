<?php
class CharImageFeature
{
	private $_posLineTypes	= array();

	private $_width	= 0;

	private $_height	= 0;

	public function __construct()
	{
	}

	public function setPosLineTypes($posLineTypes)
	{
		$this->_posLineTypes	= $posLineTypes;
		return $this;
	}

	public function getPosLineTypes()
	{
		return $this->_posLineTypes;
	}

	public function setWidth($width)
	{
		$this->_width	= $width;
		return $this;
	}

	public function getWidth()
	{
		return $this->_width;
	}

	public function setHeight($height)
	{
		$this->_height	= $height;
	}

	public function getHeight()
	{
		return $this->_height;
	}
}
