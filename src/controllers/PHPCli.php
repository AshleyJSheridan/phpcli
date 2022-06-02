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

	public function message($message)
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
		$popStackItemAfterOutputtingChildren = false;

		if($branch->canFormat())
		{
			$this->screenRenderer->setFormatting($branch);
			$popStackItemAfterOutputtingChildren = true;
		}

		if($branch->canOutput())
		{
			$this->screenRenderer->outputStack($branch->content);
		}

		if($branch->hasChildren())
		{
			$this->buildFormattedTree($branch->children);
		}

		if($popStackItemAfterOutputtingChildren)
		{
			$this->screenRenderer->popStackItem();
		}
	}
}