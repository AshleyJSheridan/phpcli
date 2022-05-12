<?php
namespace AshleyJSheridan\PHPCli\Entities;

class ScreenDimensions
{
	public $rows;
	public $cols;

	public function __construct($rows, $cols)
	{
		$this->rows = $rows;
		$this->cols = $cols;
	}
}