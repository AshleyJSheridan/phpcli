<?php

namespace AshleyJSheridan\PHPCli\Entities;

use PHPUnit\Framework\TestCase;

class OutputFormatStackItemTest extends TestCase
{
	private $item;

	public function setUp(): void
	{
		$this->item = new OutputFormatStackItem();
	}

	public function tearDown(): void
	{
		unset($this->item);
	}

	public function testCanBeConstructed()
	{
		$this->assertTrue($this->item instanceof OutputFormatStackItem);
	}

	public function testHasItalicReturnsTrue()
	{
		$this->item->italic = true;

		$this->assertTrue($this->item->hasItalic());
	}

	public function testHasItalicReturnsFalse()
	{
		$this->item->italic = null;

		$this->assertFalse($this->item->hasItalic());
	}

	public function testHasBackgroundReturnsTrue()
	{
		$this->item->background = '#fff';

		$this->assertTrue($this->item->hasBackground());
	}

	public function testHasBackgroundReturnsFalse()
	{
		$this->item->background = null;

		$this->assertFalse($this->item->hasBackground());
	}

	public function testHasBoldReturnsTrue()
	{
		$this->item->bold = true;

		$this->assertTrue($this->item->hasBold());
	}

	public function testHasBoldReturnsFalse()
	{
		$this->item->bold = null;

		$this->assertFalse($this->item->hasBold());
	}

	public function testHasForegroundReturnsTrue()
	{
		$this->item->foreground = '#000';

		$this->assertTrue($this->item->hasForeground());
	}

	public function testHasForegroundReturnsFalse()
	{
		$this->item->foreground = null;

		$this->assertFalse($this->item->hasForeground());
	}

	public function testHasUnderlinedReturnsTrue()
	{
		$this->item->underlined = true;

		$this->assertTrue($this->item->hasUnderlined());
	}

	public function testHasUnderlinedReturnsFalse()
	{
		$this->item->underlined = null;

		$this->assertFalse($this->item->hasUnderlined());
	}
}
