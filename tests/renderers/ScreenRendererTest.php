<?php
namespace AshleyJSheridan\PHPCli\Renderers;

use AshleyJSheridan\PHPCli\Entities\HtmlNode;
use AshleyJSheridan\PHPCli\Entities\OutputFormatStackItem;
use AshleyJSheridan\PHPCli\Helpers\ColourHelper;
use PHPUnit\Framework\TestCase;

class ScreenRendererTest extends TestCase
{
	private $helper;
	private $renderer;
	private $zws = '';
	private $reset = "\033[0m";

	protected function setUp(): void
	{
		$this->zws = mb_chr(8203, 'UTF-8');

		$this->helper = $this->getMockBuilder(ColourHelper::class)
			->getMock();
		$this->renderer = $this->getMockBuilder(ScreenRenderer::class)
			->setMethods(null)
			->setConstructorArgs([$this->helper])
			->getMock();
	}

	protected function tearDown(): void
	{
		unset($this->renderer);
	}

	public function testCanBeConstructed()
	{
		$this->assertTrue($this->renderer instanceof ScreenRenderer);
	}

	public function testShouldSetFormattingOnANode()
	{
		$stackItem = $this->getMockBuilder(OutputFormatStackItem::class)
			->getMock();
		$expectedStack = [$stackItem];

		$node = $this->getMockBuilder(HtmlNode::class)
			->setMethods(['applyFormatToCurrentStackItem'])
			->disableOriginalConstructor()
			->getMock();
		$node->expects($this->once())
			->method('applyFormatToCurrentStackItem')
			->with($stackItem)
			->willReturn($stackItem);

		$this->renderer = $this->getMockBuilder(ScreenRenderer::class)
			->setMethods(['getLastOrNewStackItem'])
			->setConstructorArgs([$this->helper])
			->getMock();
		$this->renderer->expects($this->once())
			->method('getLastOrNewStackItem')
			->willReturn($stackItem);

		$this->renderer->setFormatting($node);
		$this->assertEquals($expectedStack, $this->renderer->getStack());
	}

	public function testPushStackItem()
	{
		$stackItem = $this->getMockBuilder(OutputFormatStackItem::class)
			->getMock();
		$expectedStack = [$stackItem];

		$this->renderer->pushStackItem($stackItem);
		$this->assertEquals($expectedStack, $this->renderer->getStack());
	}

	public function testPopStackItem()
	{
		$stackItem = $this->getMockBuilder(OutputFormatStackItem::class)
			->getMock();

		$this->renderer->pushStackItem($stackItem);
		$this->renderer->popStackItem();
		$this->assertEquals([], $this->renderer->getStack());
	}

	public function testGetLastOrNewStackItemShouldReturnCloneOfLastStackItem()
	{
		$stackItem1 = $this->getMockBuilder(OutputFormatStackItem::class)
			->getMock();
		$stackItem2 = $this->getMockBuilder(OutputFormatStackItem::class)
			->getMock();
		$stackItem2->background = 'some background';
		$stackItem2->foreground = 'some foreground';

		$this->renderer->pushStackItem($stackItem1);
		$this->renderer->pushStackItem($stackItem2);

		$stackItemResult = $this->renderer->getLastOrNewStackItem();

		$this->assertNotSame($stackItem2, $stackItemResult);
		$this->assertEquals($stackItem2, $stackItemResult);
	}

	public function testGetLastOrNewStackItemShouldReturnNewOutputFormatStackItem()
	{
		$blankStackItem = new OutputFormatStackItem();

		$stackItemResult = $this->renderer->getLastOrNewStackItem();

		$this->assertEquals($blankStackItem, $stackItemResult);
		$this->assertNotSame($blankStackItem, $stackItemResult);
	}

	public function testOutputStackShouldOutputTextWithBackgroundFormatting()
	{
		$stackItem = new OutputFormatStackItem();
		$stackItem->background = '#fff';
		$cliColourCode = 'some background colour';
		$text = 'some text';

		$colourHelper = $this->getMockBuilder(ColourHelper::class)
			->setMethods(['getClosestColour'])
			->getMock();
		$colourHelper->expects($this->once())
			->method('getClosestColour')
			->with('#fff', 'background')
			->willReturn($cliColourCode);

		$renderer = $this->getMockBuilder(ScreenRenderer::class)
			->setMethods(['outputContentToScreen'])
			->setConstructorArgs([$colourHelper])
			->getMock();
		$renderer->expects($this->at(0))
			->method('outputContentToScreen')
			->with("\033[{$cliColourCode}m{$this->zws}");
		$renderer->expects($this->at(1))
			->method('outputContentToScreen')
			->with($text);
		$renderer->expects($this->at(2))
			->method('outputContentToScreen')
			->with($this->reset);

		$renderer->pushStackItem($stackItem);
		$renderer->outputStack($text);
	}

	public function testOutputStackShouldOutputTextWithForegroundFormatting()
	{
		$stackItem = new OutputFormatStackItem();
		$stackItem->foreground = '#f00';
		$cliColourCode = 'some foreground colour';
		$text = 'some text';

		$colourHelper = $this->getMockBuilder(ColourHelper::class)
			->setMethods(['getClosestColour'])
			->getMock();
		$colourHelper->expects($this->once())
			->method('getClosestColour')
			->with('#f00')
			->willReturn($cliColourCode);

		$renderer = $this->getMockBuilder(ScreenRenderer::class)
			->setMethods(['outputContentToScreen'])
			->setConstructorArgs([$colourHelper])
			->getMock();
		$renderer->expects($this->at(0))
			->method('outputContentToScreen')
			->with("\033[{$cliColourCode}m{$this->zws}");
		$renderer->expects($this->at(1))
			->method('outputContentToScreen')
			->with($text);
		$renderer->expects($this->at(2))
			->method('outputContentToScreen')
			->with($this->reset);

		$renderer->pushStackItem($stackItem);
		$renderer->outputStack($text);
	}

	public function testOutputStackShouldOutputTextWithBoldFormatting()
	{
		$stackItem = new OutputFormatStackItem();
		$stackItem->bold = true;
		$text = 'some text';

		$renderer = $this->getMockBuilder(ScreenRenderer::class)
			->setMethods(['outputContentToScreen'])
			->setConstructorArgs([$this->helper])
			->getMock();
		$renderer->expects($this->at(0))
			->method('outputContentToScreen')
			->with("\033[1m{$this->zws}");
		$renderer->expects($this->at(1))
			->method('outputContentToScreen')
			->with($text);
		$renderer->expects($this->at(2))
			->method('outputContentToScreen')
			->with($this->reset);

		$renderer->pushStackItem($stackItem);
		$renderer->outputStack($text);
	}

	public function testOutputStackShouldOutputTextWithItalicFormatting()
	{
		$stackItem = new OutputFormatStackItem();
		$stackItem->italic = true;
		$text = 'some text';

		$renderer = $this->getMockBuilder(ScreenRenderer::class)
			->setMethods(['outputContentToScreen'])
			->setConstructorArgs([$this->helper])
			->getMock();
		$renderer->expects($this->at(0))
			->method('outputContentToScreen')
			->with("\033[3m{$this->zws}");
		$renderer->expects($this->at(1))
			->method('outputContentToScreen')
			->with($text);
		$renderer->expects($this->at(2))
			->method('outputContentToScreen')
			->with($this->reset);

		$renderer->pushStackItem($stackItem);
		$renderer->outputStack($text);
	}

	public function testOutputStackShouldOutputTextWithUnderlinedFormatting()
	{
		$stackItem = new OutputFormatStackItem();
		$stackItem->underlined = true;
		$text = 'some text';

		$renderer = $this->getMockBuilder(ScreenRenderer::class)
			->setMethods(['outputContentToScreen'])
			->setConstructorArgs([$this->helper])
			->getMock();
		$renderer->expects($this->at(0))
			->method('outputContentToScreen')
			->with("\033[4m{$this->zws}");
		$renderer->expects($this->at(1))
			->method('outputContentToScreen')
			->with($text);
		$renderer->expects($this->at(2))
			->method('outputContentToScreen')
			->with($this->reset);

		$renderer->pushStackItem($stackItem);
		$renderer->outputStack($text);
	}

	public function testOutputStackShouldOutputTextWithNoFormatting()
	{
		$stackItem = new OutputFormatStackItem();
		$text = 'some text';

		$renderer = $this->getMockBuilder(ScreenRenderer::class)
			->setMethods(['outputContentToScreen'])
			->setConstructorArgs([$this->helper])
			->getMock();
		$renderer->expects($this->at(0))
			->method('outputContentToScreen')
			->with($text);
		$renderer->expects($this->at(1))
			->method('outputContentToScreen')
			->with($this->reset);

		$renderer->pushStackItem($stackItem);
		$renderer->outputStack($text);
	}

	public function testOutputContentToScreen()
	{
		$content = 'some content';

		$this->expectOutputString($content);

		$this->renderer->outputContentToScreen($content);
	}
}
