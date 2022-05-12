<?php
namespace AshleyJSheridan\PHPCli\Renderers;

use AshleyJSheridan\PHPCli\Entities\HtmlBoldNode;
use AshleyJSheridan\PHPCli\Entities\HtmlColourNode;
use AshleyJSheridan\PHPCli\Entities\HtmlItalicNode;
use AshleyJSheridan\PHPCli\Entities\HtmlUnderlineNode;

interface iRenderer
{
	public function setColour(HtmlColourNode $formatItem);
	public function setBold(HtmlBoldNode $formatItem);
	public function setItalic(HtmlItalicNode $formatItem);
	public function setUnderlined(HtmlUnderlineNode $formatItem);
	public function popStackItem();
	public function outputStack($text);
}