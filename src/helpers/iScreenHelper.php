<?php
namespace AshleyJSheridan\PHPCli\Helpers;

use AshleyJSheridan\PHPCli\Entities\ScreenDimensions;

interface iScreenHelper
{
	public function getScreenDimensions(): ScreenDimensions;
}