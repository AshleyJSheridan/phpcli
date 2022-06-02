<?php
namespace AshleyJSheridan\PHPCli\Entities;

class HtmlBoldNode extends HtmlNode
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
		$stackItem->bold = true;

		return $stackItem;
	}
}