<?php
use function DI\create;
use function DI\get;

return [
	'iScreenHelper' => create(\AshleyJSheridan\PHPCli\Helpers\ScreenHelper::class),
	'iHtmlHelper' => create(\AshleyJSheridan\PHPCli\Helpers\HtmlHelper::class),
	'iColourHelper' => create(\AshleyJSheridan\PHPCli\Helpers\ColourHelper::class),
	'iScreenRenderer' => create(\AshleyJSheridan\PHPCli\Renderers\ScreenRenderer::class)
		->constructor(get('iColourHelper')),
	'PHPCli' => create(\AshleyJSheridan\PHPCli\Controllers\PHPCli::class)
		->constructor(get('iScreenHelper'), get('iHtmlHelper'), get('iScreenRenderer'), get('iColourHelper'))
];
