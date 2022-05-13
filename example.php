#!/usr/bin/env php
<?php
namespace AshleyJSheridan\PHPCli;

use DI\ContainerBuilder;

require_once 'vendor/autoload.php';

$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions("src/config/di.php");
$container = $containerBuilder->build();

try {
	$phpcli = $container->make('PHPCli');
	$options = array(
		'width' => 20,
	);
	$phpcli->message('<font color="#f00">red <i>text</i></font> with <b>bold</b> <span>bit</span>, <font background="#080" color="#000">green background text</font> here'."\n", true, $options);
} catch (\Exception $ex) {
	var_dump($ex);
}
