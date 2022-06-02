<?php
namespace AshleyJSheridan\PHPCli\Entities;

class HtmlItalicNode extends HtmlNode
{
	public function __construct($content)
	{
		parent::__construct($content);
	}

	public function canFormat()
	{
		return true;
	}

	public function applyFormatToCurrentStackItem(OutputFormatStackItem $stackItem)
	{
		$stackItem->italic = true;

		return $stackItem;
	}
}