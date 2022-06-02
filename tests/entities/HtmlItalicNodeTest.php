<?php
namespace AshleyJSheridan\PHPCli\Entities;

use PHPUnit\Framework\TestCase;

class HtmlItalicNodeTest extends TestCase
{
	private $node;

	public function setUp(): void
	{
		$this->node = new HtmlItalicNode('');
	}

	public function tearDown(): void
	{
		unset($this->node);
	}

	public function testCanBeConstructed()
	{
		$this->assertTrue($this->node instanceof HtmlItalicNode);
	}

	public function testCanFormatShouldReturnTrue()
	{
		$this->assertTrue($this->node->canFormat());
	}

	public function testShouldReturnFormatItemWithItalicSet()
	{
		$stackItem = new OutputFormatStackItem();

		$this->assertFalse($stackItem->hasItalic());

		$this->node->applyFormatToCurrentStackItem($stackItem);

		$this->assertTrue($stackItem->hasItalic());
	}
}
