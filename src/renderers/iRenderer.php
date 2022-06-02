<?php
namespace AshleyJSheridan\PHPCli\Renderers;

use AshleyJSheridan\PHPCli\Entities\iHtmlNode;
use AshleyJSheridan\PHPCli\Entities\OutputFormatStackItem;

interface iRenderer
{
	public function setFormatting(iHtmlNode $formatItem);
	public function pushStackItem(OutputFormatStackItem $stackItem);
	public function popStackItem();
	public function outputStack($text);
	public function outputContentToScreen($content);
}