<?php
namespace AshleyJSheridan\PHPCli\Helpers;

use PHPUnit\Framework\TestCase;
use \AshleyJSheridan\PHPCli\Exceptions\InvalidColourException;

class ColourHelperTest extends TestCase
{
	private $helper;

	protected function setUp(): void
	{
		$this->helper = $this->getMockBuilder(ColourHelper::class)
			->setMethods(null)
			->getMock();
	}

	protected function tearDown(): void
	{
		unset($this->helper);
	}

	public function testCanBeConstructed()
	{
		$this->assertTrue($this->helper instanceof ColourHelper);
	}

	public function testReturnsAccurateForegroundColourMatches()
	{
		// hex colour string, bash escape code
		$colourMap = [
			['#000', 30],
			['#f00', 91],
			['#008800', 32],
			['#0070b0', 36],
			['#00ffff', 96],
			['#888', 90],
			['#888888', 90],
			['#00b070', 36],
		];

		foreach ($colourMap as $colour)
		{
			$this->assertEquals($this->helper->getClosestColour($colour[0]), $colour[1]);
		}
	}

	public function testReturnsAccurateBackgroundColourMatches()
	{
		// hex colour string, bash escape code
		$colourMap = [
			['#000', 40],
			['#f00', 101],
			['#008800', 42],
			['#0070b0', 46],
			['#00ffff', 106],
			['#888', 100],
			['#888888', 100],
			['#00b070', 46],
		];

		foreach ($colourMap as $colour)
		{
			$this->assertEquals($this->helper->getClosestColour($colour[0], 'background'), $colour[1]);
		}
	}

	public function testShouldThrowIfColourIsInvalidFormat()
	{
		$this->expectException(InvalidColourException::class);

		$this->helper->getClosestColour('not a colour');
		$this->helper->getClosestColour('not a colour', 'background');
	}
}