<?php
namespace AshleyJSheridan\PHPCli\Entities;

use PHPUnit\Framework\TestCase;

class HtmlNodeTest extends TestCase
{
	private $node;

	public function setUp(): void
	{
		$this->node = new HtmlNode('');
	}

	public function tearDown(): void
	{
		unset($this->node);
	}

	public function testCanBeConstructed()
	{
		$this->assertTrue($this->node instanceof HtmlNode);
	}

	public function testReturnsTrueIfNodeHasChildren()
	{
		$this->node->children = [1, 2, 3];

		$this->assertTrue($this->node->hasChildren());
	}

	public function testCanOutputWillReturnFalse()
	{
		$this->assertFalse($this->node->canOutput());
	}

	public function testCanFormatWillReturnFalse()
	{
		$this->assertFalse($this->node->canFormat());
	}

	public function testApplyFormatToCurrentStackItemWillReturnNull()
	{
		$stackItem = new OutputFormatStackItem();

		$this->assertNull($this->node->applyFormatToCurrentStackItem($stackItem));
	}
}
