<?php
class phpcli
{
	private $rows;
	private $cols;
	private $tokenised_text = array();
	private $reset = "\033[0m";
	private $formatted_message = array();
	private $state_stack = array();	// this will hold the current formatting state of the text as a history, so when a state is closed, the previous can be reinstated

	public function __construct()
	{
		$this->check_dimensions();
	}
	
	public function message($message, $append_newline=false)
	{
		$message = $this->parse_html($message);
		
		if($append_newline)
			$message[] = "\n";

		return implode('', $message);
	}
	
	private function parse_html($message)
	{
		// convert the html message into a list of tokenised elements using domdocument
		// unfortunately, I have to do this because the reset codes don't all work inside of the BASH shell
		// particularly the bold one, which is likely to be one most used
		$doc = new DOMDocument();
		$doc->loadHTML("$message");
		$this->get_dom_node($doc);

		$reset = 0;
		
		foreach($this->tokenised_text as $element)
		{
			$current_state = (count($this->state_stack) )?end($this->state_stack):null;

			switch($element['type'])
			{
				case 'b':
				case 'strong':
					$this->state_stack[] = new state_stack('bold', $current_state);
					$reset = 0;
					break;
				case 'i':
				case 'em':
					$this->state_stack[] = new state_stack('italic', $current_state);
					$reset = 0;
					break;
				case 'u':
					$this->state_stack[] = new state_stack('underlined', $current_state);
					$reset = 0;
					break;
				case 'foreground':
					$this->state_stack[] = new state_stack(array('foreground', $element['content']), $current_state);
					$reset = 0;
					break;
				case 'background':
					$this->state_stack[] = new state_stack(array('background', $element['content']), $current_state);
					$reset = 0;
					break;
				case 'foreback':
					$this->state_stack[] = new state_stack(
						array(
							array('background', $element['content']['background']),
							array('foreground', $element['content']['color']),
						),
						$current_state
					);
					$reset = 0;
					break;
				case '#text':
					$reset ++;

					if($reset > 1)
					{
						array_pop($this->state_stack);
						$current_state = (count($this->state_stack) )?end($this->state_stack):null;
					}

					if(!$current_state)
						$this->formatted_message[] = $this->reset;
					else
						$this->formatted_message[] = $current_state->format_string;
					
					$this->formatted_message[] = $element['content'];
					
					break;
			}
		}
		$this->formatted_message[] = $this->reset;
		
		echo "\n";
		
		return $this->formatted_message;
	}
	
	private function get_dom_node(DOMNode $dom_node)
	{
		foreach($dom_node->childNodes as $node)
		{
			if(in_array($node->nodeName, array('b', 'strong', 'u', 'i', 'em', '#text') ) )
				$this->tokenised_text[] = array('type'=>$node->nodeName, 'content'=>$node->nodeValue);
			
			if(in_array($node->nodeName, array('font') ) )
			{
				if($node->hasAttribute('color') && $node->hasAttribute('background') )
					$this->tokenised_text[] = array('type'=>'foreback', 'content'=>array('color'=>$node->getAttribute('color'), 'background'=>$node->getAttribute('background') ) );
				else
				{
					if($node->hasAttribute('color') )
						$this->tokenised_text[] = array('type'=>'foreground', 'content'=>$node->getAttribute('color') );
						
					if($node->hasAttribute('background') )
						$this->tokenised_text[] = array('type'=>'background', 'content'=>$node->getAttribute('background') );
				}
			}

			if($node->hasChildNodes() )
				$this->get_dom_node ($node);
		}
	}
	
	private function check_dimensions()
	{
		$this->cols = intval(exec("tput cols") );
		$this->rows = intval(exec("tput lines") );
	}
}

class state_stack
{
	protected $bold = false;
	protected $italic = false;
	protected $underlined = false;
	protected $foreground = null;
	protected $background = null;
	
	private $colours = array(
		30 => array(0,0,0),			// black
		31 => array(128,0,0),		// red
		32 => array(0,128,0),		// green
		33 => array(128,128,0),		// yellow
		34 => array(128,0,0),		// blue
		35 => array(128,128,0),		// magenta
		36 => array(0,128,128),		// cyan
		37 => array(192,192,192),	// grey
		
		90 => array(128,128,128),	// dark grey
		91 => array(255,0,0),		// light red
		92 => array(0,255,0),		// light green
		93 => array(255,255,0),		// light yellow
		94 => array(0,0,255),		// light blue
		95 => array(255,255,0),		// light magenta
		96 => array(0,255,255),		// light cyan
		97 => array(255,255,255),	// white
	);
	
	public $format_string = "\033[0m";

	public function __construct($new_state, $previous_state=null)
	{
		// the new state should build upon the previous
		if($previous_state)
		{
			foreach($previous_state as $state => $value)
				$this->{$state} = $value;
		}
		
		// simple state additions
		if(is_string($new_state) && in_array($new_state, array('bold', 'italic', 'underlined') ) )
			$this->{$new_state} = true;
			
		// complex state additions
		if(is_array($new_state))
		{
			if(is_string($new_state[0]) )
			{
				switch($new_state[0])
				{
					case 'foreground':
						$this->foreground = $new_state[1];
						break;
					case 'background':
						$this->background = $new_state[1];
						break;
				}
			}
			else
			{
				foreach($new_state as $state)
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
							$this->format_string .= "\033[1m";
						break;
					case 'italic':
						if($this->italic)
							$this->format_string .= "\033[3m";
						break;
					case 'underlined':
						if($this->underlined)
							$this->format_string .= "\033[4m";
						break;
					case 'foreground':
					case 'background':
						$colour = $this->get_closest_colour($this->{$prop}, $prop);

						$this->format_string .= "\033[{$colour}m";
						//$this->format_string .= $colour;
						break;
				}
			}
		}
	}
	
	private function get_closest_colour($colour, $type='foreground')
	{
		if(strlen($colour) == 4)
			$colour = '#' . substr($colour, 1, 1) . substr($colour, 1, 1) . substr($colour, 2, 1) . substr($colour, 2, 1) . substr($colour, 3, 1) . substr($colour, 3, 1);

		list($r, $g, $b) = sscanf($colour, "#%2x%2x%2x");
   
		$lowest_diff = 1000000;
		$colour_index = null;
   
		foreach($this->colours as $id => $colour)
		{
			$difference = sqrt(pow($r-$colour[0],2)+pow($g-$colour[1],2)+pow($b-$colour[2],2));

			if($difference < $lowest_diff)
			{
				$lowest_diff = $difference;
				$colour_index = $id;
			}
		}
		
		if($type == 'foreground')
			return $colour_index;
		else
			return $colour_index + 10;	// background colours in the terminal are the same but with an index of 10 higher
	}
}
