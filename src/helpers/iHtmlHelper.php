<?php
namespace AshleyJSheridan\PHPCli\Helpers;

interface iHtmlHelper
{
	public function parseHtml($message);
	public function getDomNodes(\DOMNode $domNode);
}