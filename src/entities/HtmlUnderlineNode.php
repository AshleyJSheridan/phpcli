<?php
namespace AshleyJSheridan\PHPCli\Entities;

class HtmlUnderlineNode extends HtmlNode
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
		$stackItem->underlined = true;

		return $stackItem;
	}
}