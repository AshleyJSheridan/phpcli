<?php
namespace AshleyJSheridan\PHPCli\Entities;

use PHPUnit\Framework\TestCase;

class HtmlUnderlineNodeTest extends TestCase
{
	private $node;

	public function setUp(): void
	{
		$this->node = new HtmlUnderlineNode('');
	}

	public function tearDown(): void
	{
		unset($this->node);
	}

	public function testCanBeConstructed()
	{
		$this->assertTrue($this->node instanceof HtmlUnderlineNode);
	}

	public function testCanFormatShouldReturnTrue()
	{
		$this->assertTrue($this->node->canFormat());
	}

	public function testShouldReturnFormatItemWithUnderlinedSet()
	{
		$stackItem = new OutputFormatStackItem();

		$this->assertFalse($stackItem->hasUnderlined());

		$this->node->applyFormatToCurrentStackItem($stackItem);

		$this->assertTrue($stackItem->hasUnderlined());
	}
}
