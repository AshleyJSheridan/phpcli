<?php
namespace AshleyJSheridan\PHPCli\Helpers;

interface iColourHelper
{
	public function getClosestColour($colour, $type = 'foreground');
}