<?php
namespace AshleyJSheridan\PHPCli\Controllers;

use AshleyJSheridan\PHPCli\Entities\HtmlBoldNode;
use AshleyJSheridan\PHPCli\Entities\HtmlNode;
use AshleyJSheridan\PHPCli\Helpers\HtmlHelper;
use AshleyJSheridan\PHPCli\Helpers\ScreenHelper;
use AshleyJSheridan\PHPCli\Renderers\ScreenRenderer;
use PHPUnit\Framework\TestCase;

class PHPCliTest extends TestCase
{
	private $screenHelper;
	private $htmlHelper;
	private $screenRenderer;
	private $controller;

	protected function setUp(): void
	{
		$this->screenHelper = $this->getMockBuilder(ScreenHelper::class)
			->setMethods(['getScreenDimensions'])
			->disableOriginalConstructor()
			->getMock();
		$this->htmlHelper = $this->getMockBuilder(HtmlHelper::class)
			->disableOriginalConstructor()
			->setMethods(['parseHtml'])
			->getMock();
		$this->screenRenderer = $this->getMockBuilder(ScreenRenderer::class)
			->disableOriginalConstructor()
			->getMock();

		$this->controller = $this->getMockBuilder(PHPCli::class)
			->setConstructorArgs([$this->screenHelper, $this->htmlHelper, $this->screenRenderer])
			->getMock();
	}

	public function tearDown(): void
	{
		unset($this->screenHelper);
		unset($this->htmlHelper);
		unset($this->screenRenderer);
		unset($this->controller);
	}

	public function testCanBeConstructed()
	{
		$this->assertTrue($this->controller instanceof PHPCli);
	}

	public function testShouldParseHtmlAndBuildAFormattedTree()
	{
		$message = 'some message';
		$parsedHtmlTree = 'some parsed tree';

		$this->htmlHelper->expects($this->once())
			->method('parseHtml')
			->with($message)
			->willReturn($parsedHtmlTree);

		$this->controller = $this->getMockBuilder(PHPCli::class)
			->setConstructorArgs([$this->screenHelper, $this->htmlHelper, $this->screenRenderer])
			->setMethods(['buildFormattedTree'])
			->getMock();
		$this->controller->expects($this->once())
			->method('buildFormattedTree')
			->with($parsedHtmlTree);

		$this->controller->message($message);
	}

	public function testShouldBuildEachBranchOfATree()
	{
		$htmlTree = [
			new HtmlNode('some content 1'),
			new HtmlNode('some content 2'),
			new HtmlNode('some content 3'),
		];

		$this->controller = $this->getMockBuilder(PHPCli::class)
			->setConstructorArgs([$this->screenHelper, $this->htmlHelper, $this->screenRenderer])
			->setMethods(['buildFormattedBranch'])
			->getMock();
		for($i = 0; $i < count($htmlTree); $i ++)
		{
			$this->controller->expects($this->at($i))
				->method('buildFormattedBranch')
				->with($htmlTree[$i]);
		}

		$this->controller->buildFormattedTree($htmlTree);
	}

	public function testShouldApplyFormattingIfNodeIsFormattingNode()
	{
		$htmlNode = $this->getMockBuilder(HtmlNode::class)
			->setMethods(['canFormat', 'canOutput', 'hasChildren'])
			->disableOriginalConstructor()
			->getMock();
		$htmlNode->expects($this->once())
			->method('canFormat')
			->willReturn(true);

		$this->screenRenderer = $this->getMockBuilder(ScreenRenderer::class)
			->disableOriginalConstructor()
			->setMethods(['setFormatting'])
			->getMock();
		$this->screenRenderer->expects($this->once())
			->method('setFormatting')
			->with($htmlNode);

		$this->controller = $this->getMockBuilder(PHPCli::class)
			->setConstructorArgs([$this->screenHelper, $this->htmlHelper, $this->screenRenderer])
			->setMethods(null)
			->getMock();

		$this->controller->buildFormattedBranch($htmlNode);
	}

	public function testShouldNotApplyFormattingIfNodeIsNotFormattingNode()
	{
		$htmlNode = $this->getMockBuilder(HtmlNode::class)
			->setMethods(['canFormat', 'canOutput', 'hasChildren'])
			->disableOriginalConstructor()
			->getMock();

		$this->screenRenderer = $this->getMockBuilder(ScreenRenderer::class)
			->disableOriginalConstructor()
			->setMethods(['setFormatting'])
			->getMock();
		$this->screenRenderer->expects($this->never())
			->method('setFormatting');

		$this->controller = $this->getMockBuilder(PHPCli::class)
			->setConstructorArgs([$this->screenHelper, $this->htmlHelper, $this->screenRenderer])
			->setMethods(null)
			->getMock();

		$this->controller->buildFormattedBranch($htmlNode);
	}

	public function testShouldOutputCurrentFormattingStackWithContentIfContentNode()
	{
		$htmlNode = $this->getMockBuilder(HtmlNode::class)
			->setMethods(['canFormat', 'canOutput', 'hasChildren'])
			->disableOriginalConstructor()
			->getMock();
		$htmlNode->expects($this->once())
			->method('canOutput')
			->willReturn(true);
		$htmlNode->content = 'some content';

		$this->screenRenderer = $this->getMockBuilder(ScreenRenderer::class)
			->disableOriginalConstructor()
			->setMethods(['outputStack'])
			->getMock();
		$this->screenRenderer->expects($this->once())
			->method('outputStack')
			->with('some content');

		$this->controller = $this->getMockBuilder(PHPCli::class)
			->setConstructorArgs([$this->screenHelper, $this->htmlHelper, $this->screenRenderer])
			->setMethods(null)
			->getMock();

		$this->controller->buildFormattedBranch($htmlNode);
	}

	public function testShouldNotOutputContentIfNotContentNode()
	{
		$htmlNode = $this->getMockBuilder(HtmlNode::class)
			->setMethods(['canFormat', 'canOutput', 'hasChildren'])
			->disableOriginalConstructor()
			->getMock();

		$this->screenRenderer = $this->getMockBuilder(ScreenRenderer::class)
			->disableOriginalConstructor()
			->setMethods(['outputStack'])
			->getMock();
		$this->screenRenderer->expects($this->never())
			->method('outputStack');

		$this->controller = $this->getMockBuilder(PHPCli::class)
			->setConstructorArgs([$this->screenHelper, $this->htmlHelper, $this->screenRenderer])
			->setMethods(null)
			->getMock();

		$this->controller->buildFormattedBranch($htmlNode);
	}

	public function testShouldOutputChildrenIfNodeHasChildNodes()
	{
		$htmlNode = $this->getMockBuilder(HtmlNode::class)
			->setMethods(['canFormat', 'canOutput', 'hasChildren'])
			->disableOriginalConstructor()
			->getMock();
		$htmlNode->expects($this->once())
			->method('hasChildren')
			->willReturn(true);
		$htmlNode->children = ['some', 'child', 'nodes'];

		$this->screenRenderer = $this->getMockBuilder(ScreenRenderer::class)
			->disableOriginalConstructor()
			->setMethods(['outputStack'])
			->getMock();

		$this->controller = $this->getMockBuilder(PHPCli::class)
			->setConstructorArgs([$this->screenHelper, $this->htmlHelper, $this->screenRenderer])
			->setMethods(['buildFormattedTree'])
			->getMock();
		$this->controller->expects($this->once())
			->method('buildFormattedTree')
			->with(['some', 'child', 'nodes']);

		$this->controller->buildFormattedBranch($htmlNode);
	}

	public function testShouldNotOutputChildrenIfNodeHasNoChildren()
	{
		$htmlNode = $this->getMockBuilder(HtmlNode::class)
			->setMethods(['canFormat', 'canOutput', 'hasChildren'])
			->disableOriginalConstructor()
			->getMock();

		$this->screenRenderer = $this->getMockBuilder(ScreenRenderer::class)
			->disableOriginalConstructor()
			->setMethods(['outputStack'])
			->getMock();

		$this->controller = $this->getMockBuilder(PHPCli::class)
			->setConstructorArgs([$this->screenHelper, $this->htmlHelper, $this->screenRenderer])
			->setMethods(['buildFormattedTree'])
			->getMock();
		$this->controller->expects($this->never())
			->method('buildFormattedTree');

		$this->controller->buildFormattedBranch($htmlNode);
	}

	public function testShouldFinallyPopLastFormattingIfFormattingNode()
	{
		$htmlNode = $this->getMockBuilder(HtmlNode::class)
			->setMethods(['canFormat', 'canOutput', 'hasChildren'])
			->disableOriginalConstructor()
			->getMock();
		$htmlNode->expects($this->once())
			->method('canFormat')
			->willReturn(true);

		$this->screenRenderer = $this->getMockBuilder(ScreenRenderer::class)
			->disableOriginalConstructor()
			->setMethods(['popStackItem'])
			->getMock();
		$this->screenRenderer->expects($this->once())
			->method('popStackItem');

		$this->controller = $this->getMockBuilder(PHPCli::class)
			->setConstructorArgs([$this->screenHelper, $this->htmlHelper, $this->screenRenderer])
			->setMethods(null)
			->getMock();

		$this->controller->buildFormattedBranch($htmlNode);
	}

	public function testShouldNotFinallyPopLastFormattingIfNotFormattingNode()
	{
		$htmlNode = $this->getMockBuilder(HtmlNode::class)
			->setMethods(['canFormat', 'canOutput', 'hasChildren'])
			->disableOriginalConstructor()
			->getMock();

		$this->screenRenderer = $this->getMockBuilder(ScreenRenderer::class)
			->disableOriginalConstructor()
			->setMethods(['popStackItem'])
			->getMock();
		$this->screenRenderer->expects($this->never())
			->method('popStackItem');

		$this->controller = $this->getMockBuilder(PHPCli::class)
			->setConstructorArgs([$this->screenHelper, $this->htmlHelper, $this->screenRenderer])
			->setMethods(null)
			->getMock();

		$this->controller->buildFormattedBranch($htmlNode);
	}
}