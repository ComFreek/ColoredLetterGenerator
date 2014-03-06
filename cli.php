<?php

// Colored Letter Generator
namespace CLG;

require_once('generator.php');

/*
 * Format of config file:
 * JSON
 * {
 * 		"width": 100,
 *  	"height": 100,
 *  	"font": "xyz.ttf",
 *  	"fontSizeRange": {
 *  		"start": 100,
 *  		"end": 500
 *  	},
 *  	"letters": [
 *  		"a","b","c",...
 *  	],
 *  	"colors": [
 *  		[255,0,0],
 *   		[0,255,0],
 *  		...
 *   	]
 * }
 * NOTE: this function also normalized the font path
 */
function readConfigFile($file) {
	$contents = @file_get_contents($file);
	if ($contents === false) {
		return false;
	}
	$json = json_decode($contents, true);
	if ($json === null) {
		return false;
	}
	
	// normalize font path
	// if the font path is NOT absolute (i.e. relative)
	if (!preg_match('/^(?:\/|\\\\|\w:\\\\|\w:\/).*$/', $json['font'])) {
		$json['font'] = realpath(dirname(realpath($file)) . DIRECTORY_SEPARATOR . $json['font']);
	}
	
	return $json;
}

function genImages($options, $saveCallback) {
	$gen = new Generator(['width' => $options['width'], 'height' => $options['height']], $options['font']);
	$letters = $options['letters'];
	
	if (isset($options['fontSizeRange'])) {
		$gen->setFontSizeRange($options['fontSizeRange']['start'], $options['fontSizeRange']['end']);
	}
	
	$nrOfLetters = count($options['letters']);
	$nrOfColors = count($options['colors']);
	for ($letterIdx = 0; $letterIdx < $nrOfLetters; $letterIdx++) {
		$letter = $options['letters'][$letterIdx];
	
		for ($colorIdx = 0; $colorIdx < $nrOfColors; $colorIdx++) {
			$color = $options['colors'][$colorIdx];
		
			$letterImg = $gen->makeLetter(
				$letter,
				['r' => $color[0], 'g' => $color[1], 'b' => $color[2]]
			);
			
			$saveCallback($letterImg, $letter, $color, ['r' => 255, 'g' => 255, 'b' => 255]);
			imagedestroy($letterImg);
		}
	}
}

function saveInFolder($path) {
	return function($img, $letter, $color, $bgColor) use ($path) {
		$bgPath = $path . DIRECTORY_SEPARATOR . implode('-', $bgColor);
		if (!file_exists($bgPath)) {
			mkdir($bgPath);
		}
		$filename = $letter . '_' . implode('-', $color) . '.png';
		imagepng($img, $bgPath . DIRECTORY_SEPARATOR . $filename);
	};
}

echo 'Path to your configuration file: ';
$configFile = trim(fgets(STDIN));

$options = readConfigFile($configFile);
if ($options === false) {
	exit('Could not read configuration file. Check file path and/or JSON syntax.');
}

echo 'Path to destination folder: ';
$destFolder = trim(fgets(STDIN));

if (!file_exists($destFolder)) {
	mkdir($destFolder);
}
genImages($options, saveInFolder($destFolder));

