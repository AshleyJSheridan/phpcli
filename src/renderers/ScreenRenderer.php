<?php
namespace AshleyJSheridan\PHPCli\Renderers;

use AshleyJSheridan\PHPCli\Entities\HtmlBoldNode;
use AshleyJSheridan\PHPCli\Entities\HtmlColourNode;
use AshleyJSheridan\PHPCli\Entities\HtmlItalicNode;
use AshleyJSheridan\PHPCli\Entities\HtmlUnderlineNode;
use AshleyJSheridan\PHPCli\Entities\OutputFormatStackItem;
use AshleyJSheridan\PHPCli\Helpers\iColourHelper;

class ScreenRenderer implements iRenderer
{
	private $colourHelper;
	private $stack = [];
	private $zws = ''; // this has to be set in the __construct method, as mb_chr() is not allowed here
	private $reset = "\033[0m";

	public function __construct(iColourHelper $colourHelper)
	{
		$this->colourHelper = $colourHelper;

		$this->zws = mb_chr(8203, 'UTF-8');
	}

	public function setColour(HtmlColourNode $formatItem)
	{
		$stackItem = $this->getLastOrNewStackItem();

		if(!is_null($formatItem->foreground))
		{
			$stackItem->foreground = $formatItem->foreground;
		}
		if(!is_null($formatItem->background))
		{
			$stackItem->background = $formatItem->background;
		}

		$this->stack[] = $stackItem;
	}

	public function setBold(HtmlBoldNode $formatItem)
	{
		$stackItem = $this->getLastOrNewStackItem();
		$stackItem->bold = true;
		$this->stack[] = $stackItem;
	}

	public function setItalic(HtmlItalicNode $formatItem)
	{
		$stackItem = $this->getLastOrNewStackItem();
		$stackItem->italic = true;
		$this->stack[] = $stackItem;
	}

	public function setUnderlined(HtmlUnderlineNode $formatItem)
	{
		$stackItem = $this->getLastOrNewStackItem();
		$stackItem->underlined = true;
		$this->stack[] = $stackItem;
	}

	public function popStackItem()
	{
		array_pop($this->stack);
	}

	public function outputStack($text)
	{
		$lastStackItem = $this->stack[count($this->stack) - 1];

		if($lastStackItem->hasBackground())
		{
			$colour = $this->colourHelper->getClosestColour($lastStackItem->background, 'background');
			$this->outputContentToScreen("\033[{$colour}m{$this->zws}");
		}
		if($lastStackItem->hasForeground())
		{
			$colour = $this->colourHelper->getClosestColour($lastStackItem->foreground);
			$this->outputContentToScreen("\033[{$colour}m{$this->zws}");
		}
		if($lastStackItem->hasBold())
		{
			$this->outputContentToScreen("\033[1m{$this->zws}");
		}
		if($lastStackItem->hasItalic())
		{
			$this->outputContentToScreen("\033[3m{$this->zws}");
		}
		if($lastStackItem->hasUnderlined())
		{
			$this->outputContentToScreen("\033[4m{$this->zws}");
		}

		$this->outputContentToScreen($text);
		$this->outputContentToScreen($this->reset);
	}

	public function outputContentToScreen($content)
	{
		echo $content;
	}

	private function getLastOrNewStackItem()
	{
		if(!isset($this->stack[count($this->stack) - 1]))
		{
			$this->stack[] = new OutputFormatStackItem();
		}

		return clone $this->stack[count($this->stack) - 1];
	}
}