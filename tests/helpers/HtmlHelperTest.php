<?php
namespace AshleyJSheridan\PHPCli\Helpers;

use PHPUnit\Framework\TestCase;

class HtmlHelperTest extends TestCase
{
	private $helper;

	protected function setUp(): void
	{
		$this->helper = $this->getMockBuilder(HtmlHelper::class)
			->setMethods(null)
			->getMock();
	}

	protected function tearDown(): void
	{
		unset($this->helper);
	}

	public function testCanBeConstructed()
	{
		$this->assertTrue($this->helper instanceof HtmlHelper);
	}

	public function testShouldParseAnHtmlString()
	{
		$htmlString = "<p>some content</p>";
		$domDoc = new \DOMDocument($htmlString);

		$this->helper = $this->getMockBuilder(HtmlHelper::class)
			->setMethods(['getDocument', 'getDomNodes'])
			->getMock();
		$this->helper->expects($this->once())
			->method('getDocument')
			->with($htmlString)
			->willReturn($domDoc);
		$this->helper->expects($this->once())
			->method('getDomNodes')
			->with($domDoc);

		$this->helper->parseHtml($htmlString);
	}

	public function testShouldReturnEmptyArrayIfDomNodeHasNoChildren()
	{
		$dom = new \DOMDocument();
		$node = $dom->createTextNode('some text');

		$result = $this->helper->getDomNodes($node);
		$this->assertEquals([], $result);
	}

	public function testShouldReturnANestedArrayOfNodesForADocument()
	{
		$dom = new \DOMDocument();
		$root = $dom->createElement('root');
		$child = $dom->createElement('child');
		$root->appendChild($child);
		$dom->appendChild($root);

		$result = $this->helper->getDomNodes($dom);
		$this->assertCount(1, $result);
		$this->assertCount(1, $result[0]->children);
	}

	public function testShouldLoadHtmlSnippetAsDocument()
	{
		$htmlString = "<p>some content</p>";

		$result = $this->helper->getDocument($htmlString);

		$this->assertTrue($result instanceof \DOMDocument);
	}
}