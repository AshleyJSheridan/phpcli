<?php
class phpcli
{
	private $rows;
	private $cols;
	private $tokenisedText = [];
	private $reset = "\033[0m";
	private $zws = '';	// this has to be set in the __construct method, as mb_chr() is not allowed here
	private $formattedMessage = [];
	private $stateStack = [];	// this will hold the current formatting state of the text as a history, so when a state is closed, the previous can be reinstated

	public function __construct()
	{
		$this->zws = mb_chr(8203, 'UTF-8');
	
		$this->checkDimensions();
	}
	
	public function message($message, $echo = false, $options = [])
	{
		$this->formattedMessage = $this->tokenisedText = $this->stateStack = [];

		$message = $this->parseHtml($message);
		$message = implode('', $message);
		
		// if there are any extra customisation options for the text, run through them and do whatever needs doing
		if(count($options))
		{
			foreach($options as $opt => $value)
			{
				switch($opt)
				{
					case 'width':
						$message = $this->fixWidth($message, $value);
						break;
				}
			}
		}

		if(!$echo)
			return $message;

		echo preg_replace("/$this->zws/", '', $message);
	}
	
	private function fixWidth($message, $width)
	{
		$wordsRaw = preg_split("/[ {$this->zws}]/", $message);
		$lines = $words = [];
		
		// because of splitting the string on spaces and non-breaking spaces, there are some empty strings in the words list
		foreach($wordsRaw as $word)
		{
			if(strlen($word))
			{
				$words[] = $word;
			}
		}
		
		for($i = 0; $i < count($words); $i ++)
		{
			$escapedWord = $this->getStringWithoutEscapes($words[$i]);

			// first, check if any word is longer than the current box width and adjust it if necessary
			if(strlen($escapedWord) > $width)
			{
				$width = strlen($escapedWord);
			}
		}

		for($i = 0; $i < count($words); $i ++)
		{
			$escapedWord = $this->getStringWithoutEscapes($words[$i]);
			
			// determines if the previous word exists and was an actual word and not an escape sequence thereby requiring a space
			$space = ($i && strlen($this->getStringWithoutEscapes($words[$i-1]) ) )?' ':'';

			if(strlen($words[$i]))
			{
				if(isset($lines[count($lines)-1]) && (strlen("{$lines[count($lines)-1]} {$escapedWord}") <= $width ))
				{
					$lines[count($lines) - 1] .= "$space{$words[$i]}";
				}
				else
				{
					$lines[] = $words[$i];
				}
			}
		}

		if(count($lines))
		{
			$lines[0] = trim($lines[0]);
		}
		
		return str_replace('  ', ' ', implode("\n", $lines));
	}
	
	private function getStringWithoutEscapes($escapedString)
	{
		$cleanString = preg_replace("/\033(\[\d+m)/", '', $escapedString);
		
		return $cleanString;
	}
	
	private function parseHtml($message)
	{
		// convert the html message into a list of tokenised elements using domdocument
		// unfortunately, I have to do this because the reset codes don't all work inside of the BASH shell
		// particularly the bold one, which is likely to be one most used
		$doc = new DOMDocument();
		$doc->loadHTML($message);
		$this->getDomNode($doc);

		$reset = 0;
		
		foreach($this->tokenisedText as $element)
		{
			$currentState = (count($this->stateStack)) ? end($this->stateStack) : null;

			switch($element['type'])
			{
				case 'b':
				case 'strong':
					$this->stateStack[] = new stateStack('bold', $currentState);
					$reset = 0;
					break;
				case 'i':
				case 'em':
					$this->stateStack[] = new stateStack('italic', $currentState);
					$reset = 0;
					break;
				case 'u':
					$this->stateStack[] = new stateStack('underlined', $currentState);
					$reset = 0;
					break;
				case 'foreground':
					$this->stateStack[] = new stateStack(['foreground', $element['content']], $currentState);
					$reset = 0;
					break;
				case 'background':
					$this->stateStack[] = new stateStack(['background', $element['content']], $currentState);
					$reset = 0;
					break;
				case 'foreback':
					$this->stateStack[] = new stateStack(
						[
							['background', $element['content']['background']],
							['foreground', $element['content']['color']],
						],
						$currentState,
						$this->zws
					);
					$reset = 0;
					break;
				case '#text':
					$reset ++;

					if($reset > 1)
					{
						array_pop($this->stateStack);
						$currentState = (count($this->stateStack) ) ? end($this->stateStack) : null;
					}

					if(!$currentState)
					{
						$this->formattedMessage[] = $this->reset;
					}
					else
					{
						$this->formattedMessage[] = "{$this->zws}{$currentState->formatString}{$this->zws}";
					}
					
					$this->formattedMessage[] = $element['content'];
					
					break;
			}
		}
		$this->formattedMessage[] = $this->reset;

		return $this->formattedMessage;
	}
	
	private function getDomNode(DOMNode $domNode)
	{
		foreach($domNode->childNodes as $node)
		{
			if(in_array($node->nodeName, ['b', 'strong', 'u', 'i', 'em', '#text']))
			{
				$this->tokenisedText[] = ['type' => $node->nodeName, 'content' => $node->nodeValue];
			}
			
			if(in_array($node->nodeName, ['font']))
			{
				if($node->hasAttribute('color') && $node->hasAttribute('background') )
				{
					$this->tokenisedText[] = [
						'type' => 'foreback',
						'content' => [
							'color' => $node->getAttribute('color'),
							'background' => $node->getAttribute('background')
						]
					];
				}
				else
				{
					if($node->hasAttribute('color'))
					{
						$this->tokenisedText[] = ['type' => 'foreground', 'content' => $node->getAttribute('color')];
					}
						
					if($node->hasAttribute('background'))
					{
						$this->tokenisedText[] = ['type' => 'background', 'content' => $node->getAttribute('background')];
					}
				}
			}

			if($node->hasChildNodes() )
			{
				$this->getDomNode($node);
			}
		}
	}
	
	private function checkDimensions()
	{
		$this->cols = intval(exec("tput cols") );
		$this->rows = intval(exec("tput lines") );
	}
}

class stateStack
{
	protected $bold = false;
	protected $italic = false;
	protected $underlined = false;
	protected $foreground = null;
	protected $background = null;
	
	private $colours = [
		30 => [0, 0, 0],		// black
		31 => [128, 0, 0],		// red
		32 => [0, 128, 0],		// green
		33 => [128, 128, 0],	// yellow
		34 => [128, 0, 0],		// blue
		35 => [128, 128, 0],	// magenta
		36 => [0, 128, 128],	// cyan
		37 => [192, 192, 192],	// grey
		
		90 => [128, 128, 128],	// dark grey
		91 => [255, 0, 0],		// light red
		92 => [0, 255, 0],		// light green
		93 => [255, 255, 0],	// light yellow
		94 => [0, 0, 255],		// light blue
		95 => [255, 255, 0],	// light magenta
		96 => [0, 255, 255],	// light cyan
		97 => [255, 255, 255],	// white
	];
	
	public $formatString = "\033[0m";

	public function __construct($newState, $previousState = null, $zws = '')
	{
		// the new state should build upon the previous
		if($previousState)
		{
			foreach($previousState as $state => $value)
			{
				$this->{$state} = $value;
			}
		}
		
		// simple state additions
		if(is_string($newState) && in_array($newState, ['bold', 'italic', 'underlined']))
		{
			$this->{$newState} = true;
		}
			
		// complex state additions
		if(is_array($newState))
		{
			if(is_string($newState[0]))
			{
				switch($newState[0])
				{
					case 'foreground':
						$this->foreground = $newState[1];
						break;
					case 'background':
						$this->background = $newState[1];
						break;
				}
			}
			else
			{
				foreach($newState as $state)
				{
					switch($state[0])
					{
						case 'foreground':
							$this->foreground = $state[1];
							break;
						case 'background':
							$this->background = $state[1];
							break;
					}
				}
			}
		}
		
		foreach($this as $prop => $value)
		{
			if($value)
			{
				switch($prop)
				{
					case 'bold':
						if($this->bold)
							$this->formatString .= "\033[1m{$zws}";
						break;
					case 'italic':
						if($this->italic)
							$this->formatString .= "\033[3m{$zws}";
						break;
					case 'underlined':
						if($this->underlined)
							$this->formatString .= "\033[4m{$zws}";
						break;
					case 'foreground':
					case 'background':
						$colour = $this->getClosestColour($this->{$prop}, $prop);

						$this->formatString .= "\033[{$colour}m{$zws}";
						break;
				}
			}
		}
	}
	
	private function getClosestColour($colour, $type = 'foreground')
	{
		if(strlen($colour) == 4)
		{
			$colour = '#'
				. substr($colour, 1, 1)
				. substr($colour, 1, 1)
				. substr($colour, 2, 1)
				. substr($colour, 2, 1)
				. substr($colour, 3, 1)
				. substr($colour, 3, 1);
		}

		list($r, $g, $b) = sscanf($colour, "#%2x%2x%2x");
   
		$lowestDiff = 1000000;
		$colourIndex = null;
   
		foreach($this->colours as $id => $colour)
		{
			$difference = sqrt(pow($r - $colour[0],2) + pow($g - $colour[1],2) + pow($b - $colour[2],2));

			if($difference < $lowestDiff)
			{
				$lowestDiff = $difference;
				$colourIndex = $id;
			}
		}
		
		if($type == 'foreground')
			return $colourIndex;
		else
			return $colourIndex + 10;	// background colours in the terminal are the same but with an index of 10 higher
	}
}
