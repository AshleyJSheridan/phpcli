<?php
namespace AshleyJSheridan\PHPCli\Helpers;

use AshleyJSheridan\PHPCli\Entities\ScreenDimensions;

class ScreenHelper implements iScreenHelper
{
	public function getScreenDimensions(): ScreenDimensions
	{
		$rows = intval(exec("tput lines") );
		$cols = intval(exec("tput cols") );

		return new ScreenDimensions($rows, $cols);
	}
}