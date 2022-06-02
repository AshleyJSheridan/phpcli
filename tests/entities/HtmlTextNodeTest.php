<?php
namespace AshleyJSheridan\PHPCli\Entities;

use PHPUnit\Framework\TestCase;

class HtmlTextNodeTest extends TestCase
{
	private $node;

	public function setUp(): void
	{
		$this->node = new HtmlTextNode('');
	}

	public function tearDown(): void
	{
		unset($this->node);
	}

	public function testCanBeConstructed()
	{
		$this->assertTrue($this->node instanceof HtmlTextNode);
	}

	public function testCanOutputShouldReturnTrue()
	{
		$this->assertTrue($this->node->canOutput());
	}
}
