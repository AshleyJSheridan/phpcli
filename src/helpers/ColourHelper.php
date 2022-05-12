<?php
namespace AshleyJSheridan\PHPCli\Helpers;

use \AshleyJSheridan\PHPCli\Exceptions\InvalidColourException;

class ColourHelper implements iColourHelper
{
	private $colours = [
		30 => [0, 0, 0],		// black
		31 => [128, 0, 0],		// red
		32 => [0, 128, 0],		// green
		33 => [128, 128, 0],	// yellow
		34 => [128, 0, 0],		// blue
		35 => [128, 128, 0],	// magenta
		36 => [0, 128, 128],	// cyan
		37 => [192, 192, 192],	// grey

		90 => [128, 128, 128],	// dark grey
		91 => [255, 0, 0],		// light red
		92 => [0, 255, 0],		// light green
		93 => [255, 255, 0],	// light yellow
		94 => [0, 0, 255],		// light blue
		95 => [255, 255, 0],	// light magenta
		96 => [0, 255, 255],	// light cyan
		97 => [255, 255, 255],	// white
	];

	public function getClosestColour($colour, $type = 'foreground')
	{
		if(!preg_match("/#([0-9a-f]{3}|[0-9a-f]{6})/", $colour))
			throw new InvalidColourException("Colour $colour is not recognised");

		// normalise into a single format without losing any colour information
		if(strlen($colour) == 4)
		{
			$colour = '#'
				. substr($colour, 1, 1)
				. substr($colour, 1, 1)
				. substr($colour, 2, 1)
				. substr($colour, 2, 1)
				. substr($colour, 3, 1)
				. substr($colour, 3, 1);
		}

		list($r, $g, $b) = sscanf($colour, "#%2x%2x%2x");

		$lowestDiff = 1000000;
		$colourIndex = null;

		foreach($this->colours as $id => $colour)
		{
			$difference = sqrt(
				pow($r - $colour[0],2) +
				pow($g - $colour[1],2) +
				pow($b - $colour[2],2)
			);

			if($difference < $lowestDiff)
			{
				$lowestDiff = $difference;
				$colourIndex = $id;
			}
		}

		if($type == 'foreground')
			return $colourIndex;
		else
			return $colourIndex + 10;	// background colours in the terminal are the same but with an index of 10 higher
	}
}