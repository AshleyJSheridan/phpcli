<?php
namespace AshleyJSheridan\PHPCli\Entities;

class OutputFormatStackItem
{
	public $background;
	public $foreground;
	public $bold;
	public $italic;
	public $underlined;

	public function hasBackground()
	{
		return !is_null($this->background);
	}

	public function hasForeground()
	{
		return !is_null($this->foreground);
	}

	public function hasBold()
	{
		return !is_null($this->bold);
	}

	public function hasItalic()
	{
		return !is_null($this->italic);
	}

	public function hasUnderlined()
	{
		return !is_null($this->underlined);
	}
}