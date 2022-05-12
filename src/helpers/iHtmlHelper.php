<?php
namespace AshleyJSheridan\PHPCli\Helpers;

interface iHtmlHelper
{
	public function parseHtml($message);
	public function getDomNode(\DOMNode $domNode);
}