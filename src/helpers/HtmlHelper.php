<?php
namespace AshleyJSheridan\PHPCli\Helpers;

use AshleyJSheridan\PHPCli\Entities\HtmlColourNode;
use AshleyJSheridan\PHPCli\Entities\HtmlItalicNode;
use AshleyJSheridan\PHPCli\Entities\HtmlNode;
use AshleyJSheridan\PHPCli\Entities\HtmlTextNode;
use AshleyJSheridan\PHPCli\Entities\HtmlBoldNode;
use AshleyJSheridan\PHPCli\Entities\HtmlUnderlineNode;

class HtmlHelper implements iHtmlHelper
{
	//private $tokenisedText = [];
	//private $stateStack = [];

	public function parseHtml($message)
	{
		// convert the html message into a list of tokenised elements using domdocument
		// unfortunately, I have to do this because the reset codes don't all work inside of the BASH shell
		// particularly the bold one, which is likely to be one most used
		$doc = new \DOMDocument();
		$doc->loadHTML($message);

		return $this->getDomNode($doc);
	}

	public function getDomNode(\DOMNode $domNode)
	{
		$tokenisedText = [];

		foreach($domNode->childNodes as $childNode)
		{
			$node = null;

			switch($childNode->nodeName)
			{
				case '#text':
					$node = new HtmlTextNode($childNode->nodeValue);
					break;
				case 'b':
				case 'strong':
					$node = new HtmlBoldNode($childNode->nodeValue);
					break;
				case 'u':
					$node = new HtmlUnderlineNode($childNode->nodeValue);
					break;
				case 'i':
				case 'em':
					$node = new HtmlItalicNode($childNode->nodeValue);
					break;
				case 'font':
					$node = new HtmlColourNode();
					if($childNode->hasAttribute('color'))
					{
						$node->foreground = $childNode->getAttribute('color');
					}
					if($childNode->hasAttribute('background'))
					{
						$node->background = $childNode->getAttribute('background');
					}
					break;
				default:
					$node = new HtmlNode($childNode->nodeValue);
			}

			if($childNode->hasChildNodes())
			{
				$node->children = $this->getDomNode($childNode);
			}

			$tokenisedText[] = $node;
		}

		return $tokenisedText;
	}
}