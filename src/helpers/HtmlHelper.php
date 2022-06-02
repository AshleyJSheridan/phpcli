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
	public function parseHtml($message)
	{
		$doc = $this->getDocument($message);

		return $this->getDomNodes($doc);
	}

	public function getDomNodes(\DOMNode $domNode)
	{
		$tokenisedText = [];

		if($domNode->hasChildNodes())
		{
			foreach ($domNode->childNodes as $childNode)
			{
				$node = $this->getNodeType($childNode);

				if ($childNode->hasChildNodes())
				{
					$node->children = $this->getDomNodes($childNode);
				}

				$tokenisedText[] = $node;
			}
		}

		return $tokenisedText;
	}

	public function getDocument($html)
	{
		$doc = new \DOMDocument();
		$doc->loadHTML($html);

		return $doc;
	}

	public function getNodeType($childNode)
	{
		switch ($childNode->nodeName)
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
				if ($childNode->hasAttribute('color'))
				{
					$node->foreground = $childNode->getAttribute('color');
				}
				if ($childNode->hasAttribute('background'))
				{
					$node->background = $childNode->getAttribute('background');
				}
				break;
			default:
				$node = new HtmlNode($childNode->nodeValue);
		}

		return $node;
	}
}