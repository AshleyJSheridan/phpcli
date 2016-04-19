#!/bin/php
<?php
require_once("phpcli.php");

$cli = new phpcli();
$options = array(
	'width' => 20,
);
$cli->message('<font color="#f00">red text</font> with <b>bold</b> bit, <font background="#080" color="#000">green background text</font> here'."\n", true, $options);