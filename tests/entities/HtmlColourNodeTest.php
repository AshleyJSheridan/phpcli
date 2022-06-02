<?php
namespace AshleyJSheridan\PHPCli\Entities;

use PHPUnit\Framework\TestCase;

class HtmlColourNodeTest extends TestCase
{
	private $node;

	public function setUp(): void
	{
		$this->node = new HtmlColourNode('');
	}

	public function tearDown(): void
	{
		unset($this->node);
	}

	public function testCanBeConstructed()
	{
		$this->assertTrue($this->node instanceof HtmlColourNode);
	}

	public function testCanFormatShouldReturnTrue()
	{
		$this->assertTrue($this->node->canFormat());
	}

	public function testShouldReturnFormatItemWithForegroundSet()
	{
		$stackItem = new OutputFormatStackItem();

		$this->assertFalse($stackItem->hasForeground());
		$this->assertFalse($stackItem->hasBackground());

		$this->node->foreground = '#000';
		$this->node->applyFormatToCurrentStackItem($stackItem);

		$this->assertTrue($stackItem->hasForeground());
		$this->assertFalse($stackItem->hasBackground());
	}

	public function testShouldReturnFormatItemWithBackgroundSet()
	{
		$stackItem = new OutputFormatStackItem();

		$this->assertFalse($stackItem->hasForeground());
		$this->assertFalse($stackItem->hasBackground());

		$this->node->background = '#fff';
		$this->node->applyFormatToCurrentStackItem($stackItem);

		$this->assertFalse($stackItem->hasForeground());
		$this->assertTrue($stackItem->hasBackground());
	}

	public function testShouldReturnFormatItemWithBackgroundAndForegroundSet()
	{
		$stackItem = new OutputFormatStackItem();

		$this->assertFalse($stackItem->hasForeground());
		$this->assertFalse($stackItem->hasBackground());

		$this->node->background = '#fff';
		$this->node->foreground = '#000';
		$this->node->applyFormatToCurrentStackItem($stackItem);

		$this->assertTrue($stackItem->hasForeground());
		$this->assertTrue($stackItem->hasBackground());
	}
}
