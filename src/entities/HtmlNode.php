<?php
namespace AshleyJSheridan\PHPCli\Entities;

class HtmlNode implements iHtmlNode
{
	public $content = '';
	public $children = [];

	public function __construct($content)
	{
		$this->content = $content;
	}

	public function hasChildren()
	{
		return count($this->children) > 0;
	}

	public function canOutput()
	{
		return false;
	}

	public function canFormat()
	{
		return false;
	}

	public function applyFormatToCurrentStackItem(OutputFormatStackItem $formatItem)
	{
		return null;
	}
}