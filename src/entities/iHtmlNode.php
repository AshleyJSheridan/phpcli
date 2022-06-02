<?php
namespace AshleyJSheridan\PHPCli\Entities;

interface iHtmlNode
{
	public function hasChildren();
	public function canOutput();
	public function canFormat();
	public function applyFormatToCurrentStackItem(OutputFormatStackItem $formatItem);
}