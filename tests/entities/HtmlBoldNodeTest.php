<?php

namespace AshleyJSheridan\PHPCli\Entities;

use PHPUnit\Framework\TestCase;

class HtmlBoldNodeTest extends TestCase
{
	private $node;

	public function setUp(): void
	{
		$this->node = new HtmlBoldNode('');
	}

	public function tearDown(): void
	{
		unset($this->node);
	}

	public function testCanBeConstructed()
	{
		$this->assertTrue($this->node instanceof HtmlBoldNode);
	}

	public function testCanFormatShouldReturnTrue()
	{
		$this->assertTrue($this->node->canFormat());
	}

	public function testShouldReturnFormatItemWithBoldSet()
	{
		$stackItem = new OutputFormatStackItem();

		$this->assertFalse($stackItem->hasBold());

		$this->node->applyFormatToCurrentStackItem($stackItem);

		$this->assertTrue($stackItem->hasBold());
	}
}
