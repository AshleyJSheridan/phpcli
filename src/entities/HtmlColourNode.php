<?php
namespace AshleyJSheridan\PHPCli\Entities;

class HtmlColourNode extends HtmlNode
{
	public $foreground;
	public $background;

	public function __construct()
	{}

	public function canFormat()
	{
		return true;
	}

	public function applyFormatToCurrentStackItem(OutputFormatStackItem $stackItem)
	{
		if(!is_null($this->foreground))
		{
			$stackItem->foreground = $this->foreground;
		}
		if(!is_null($this->background))
		{
			$stackItem->background = $this->background;
		}

		return $stackItem;
	}
}