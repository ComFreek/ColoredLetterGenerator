<?php

// Colored Letter Generator
namespace CLG;

require_once('calculateTextBox.php');

/**
 * Returns the dimension a specific text needs
 * Note that this function does not take letter-specific whitespace on the left
 * and right sides into account!
 * We therefore don't use this function for centering the letters
 * @see calculateTextBox()
 * 
 * @param integer $size Font size
 * @param float $angle Angle
 * @param string $text Text
 * @return The dimension saved as ['width' => width, 'height' => height]
 */
function getDimensionOfText($size, $angle, $font, $text) {
	$bbox = imagettfbbox($size, $angle, $font, $text);
	$width = max($bbox[2], $bbox[4]) - max($bbox[0], $bbox[6]);
	$height = max($bbox[1], $bbox[3]) - max($bbox[5], $bbox[7]);
	
	return compact('width', 'height');
}

class Generator {
	private $letterSize;
	private $font;
	
	/**
	 * The range of font sizes in which the maximum fitting font size
	 * Defaults to [1, 500]
	 */
	private $fontSizeRange = [1, 500];
	
	/**
	 * @param array $letterSize Size of the graphics: ['width' => ..., 'height' => ...]
	 * @param string $font Path to the font file (may be relative or absolute)
	 */
	public function __construct(Array $letterSize, $font) {
		$this->letterSize = $letterSize;
		$this->font = $font;
	}
	
	/**
	 * Sets the range of font sizes in which the maximum fitting font size
	 * will be searched.
	 * @param integer $start
	 * @param integer $end
	 * @see Generator::$fontSizeRange
	 */
	public function setFontSizeRange($start, $end) {
		$this->fontSizeRange = [$start, $end];
	}

	/**
	 * Calculates the maximum font size for $letter which would still
	 * fit into the bounding box (=$letterSize values)
	 * @param string $letter
	 * @return integer Font size
	 * 
	 * @see Generator::$fontSizeRange
	 * @see Generator::setFontSizeRange()
	 */
	private function calcMaxSize($letter) {
		$lastGoodSize = null;
		for ($size=$this->fontSizeRange[0]; $size<$this->fontSizeRange[1]; $size++) {
			$sizeData = imagettfbbox($size, 0, $this->font, $letter);
			$dimension = getDimensionOfText($size, 0, $this->font, $letter);

			if ($dimension['width'] > $this->letterSize['width'] ||
			    $dimension['height'] > $this->letterSize['height']) {
				return $lastGoodSize;
			}
			$lastGoodSize = $size;
		}
		return $lastGoodSize;
	}
	
	/**
	 * Calculates the centered coordinates for use with imagettfbox()
	 * @param string $letter
	 * @param integer $size The letter's size
	 * @return array ['x' => centeredXCoordinate, 'y' => centeredYCoordinate]
	 */
	private function getCenteredCoords($letter, $size) {
		$bbox = calculateTextBox($size, 0, $this->font, $letter);
		$centeredX = (($this->letterSize['width'] - $bbox['width']) / 2);
		$centeredY = (($this->letterSize['height'] - $bbox['height']) / 2);
		
		return [
			'x' => $centeredX + $bbox['left'],
			'y' => $centeredY + $bbox['top']
		];
	}
	
	/*
	 * @param string $letter Letter
	 * @param Array $color Color, format: ['r' => 123, 'g' => 456, 'b' => 789]
	 * @param Array $bgColor Background color, same format as $color
	 * @return GD2 image resource (note: it's up to you to imagedestroy() it!)
	 */
	public function &makeLetter($letter, Array $color, $bgColor = ['r' => 255, 'g' => 255, 'b' => 255]) {
		$img = imagecreatetruecolor($this->letterSize['width'], $this->letterSize['height']);
		
		$colorResource = imagecolorallocate($img, $color['r'], $color['g'], $color['b']);
		$bgColorResource = imagecolorallocate($img, $bgColor['r'], $bgColor['g'], $bgColor['b']);
		
		$maxSize = $this->calcMaxSize($letter);
		
		$coords = $this->getCenteredCoords($letter, $maxSize);
		
		imagefill($img, 0, 0, $bgColorResource);
		imagettftext($img, $maxSize, 0, $coords['x'], $coords['y'], $colorResource, $this->font, $letter);
		
		return $img;
	}
}
