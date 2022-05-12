<?php
namespace AshleyJSheridan\PHPCli\Controllers;

use AshleyJSheridan\PHPCli\Entities\HtmlNode;
use AshleyJSheridan\PHPCli\Helpers\iHtmlHelper;
use AshleyJSheridan\PHPCli\Helpers\iScreenHelper;
use AshleyJSheridan\PHPCli\Renderers\iRenderer;

class PHPCli
{
	private $screenHelper;
	private $htmlHelper;
	private $screenRenderer;
	private $screenDimensions;

	public function __construct(iScreenHelper $screenHelper, iHtmlHelper $htmlHelper, iRenderer $screenRenderer)
	{
		$this->screenHelper = $screenHelper;
		$this->htmlHelper = $htmlHelper;
		$this->screenRenderer = $screenRenderer;

		$this->screenDimensions = $this->screenHelper->getScreenDimensions();
	}

	public function message($message, $echo = false, $options = [])
	{
		$messageTree = $this->htmlHelper->parseHtml($message);


		$this->buildFormattedTree($messageTree);
	}

	public function buildFormattedTree($branches)
	{
		foreach($branches as $branch)
		{
			$this->buildFormattedBranch($branch);
		}
	}

	public function buildFormattedBranch(HtmlNode $branch)
	{
		$baseClassName =  substr(strrchr(get_class($branch), '\\'), 1);
		$stackItemAfterChildren = false;

		switch($baseClassName)
		{
			case 'HtmlColourNode':
				$this->screenRenderer->setColour($branch);
				$stackItemAfterChildren = 'remove colour formatting';
				break;
			case 'HtmlBoldNode':
				$this->screenRenderer->setBold($branch);
				$stackItemAfterChildren = 'remove bold formatting';
				break;
			case 'HtmlItalicNode':
				$this->screenRenderer->setItalic($branch);
				$stackItemAfterChildren = 'remove italic formatting';
				break;
			case 'HtmlUnderlineNode':
				$this->screenRenderer->setUnderlined($branch);
				$stackItemAfterChildren = 'remove underline formatting';
				break;
			case 'HtmlTextNode':
				$this->screenRenderer->outputStack($branch->content);
				break;
		}

		if($branch->hasChildren())
		{
			$this->buildFormattedTree($branch->children);
		}

		if($stackItemAfterChildren)
		{
			$this->screenRenderer->popStackItem();
		}
	}
}