<?php
namespace AshleyJSheridan\PHPCli\Entities;

class HtmlNode
{
	public $content = '';
	public $children = [];

	public function __construct($content)
	{
		$this->content = $content;
	}

	public function hasChildren()
	{
		return count($this->children) > 0;
	}
}