<?php

/*
 * @company: 	Symbiotic Infotech Pvt. Ltd.
 * @copyright: 	© Symbiotic Infotech Pvt. Ltd. 2011
 *				All rights reserved.Any redistribution or reproduction of part
 * 				or all of the contents in any form is prohibited. You may not,
 * 				except with express written permission, distribute or
 * 				commercially exploit or personally use the content.
 * 				Nor may you transmit it or store it in any other media or
 * 				other form of electronic or physical retrieval system.
 *
 * @filename:	regex.class.inc
 * @filetype:	PHP
 * @filedesc:	RegExpBuilder is a library that helps you to build simple regular expressions
 *
 */


/*Modifier constants*/
define ( "CASE_SENSITIVE", "!i" );
define ( "CASE_INSENSITIVE", "i" );
define ( "UNICODE_MODE", "u" );
define ( "MULTILINE", "m" );
/*Frequency constants*/
define ( "ONE_OR_MORE", "+" );
define ( "ZERO_OR_MORE", "*" );
define ( "ZERO_OR_ONE", "?" );
/*Frequency limit constants*/
define ( "LESS_THEN", "<" );
define ( "MORE_THEN", ">" );
define ( "LESS_THEN_OR_EQUAL_TO", "<=" );
define ( "MORE_THEN_OR_EQUAL_TO", "=>" );
/*Condition constants*/
define ( "FOLLOWED_BY", "?=" );
define ( "NOT_FOLLOWED_BY", "?!" );
define ( "PRECEEDED_BY", "?<=" );
define ( "NOT_PRECEEDED_BY", "?<!" );
/*Characters group constants*/
define ( "GENERAL_SPACE_CHAR", "ï¿½ï¿½ï¿½\s" ); //Every space, tabulation, newline ecc..
define ( "NEWLINE_CHAR", "ï¿½ï¿½ï¿½\n" ); //Newline character
define ( "TAB_CHAR", "ï¿½ï¿½ï¿½\t" ); //Tabulation character
define ( "CARRIAGE_RETURN_CHAR", "ï¿½ï¿½ï¿½\r" ); //Carriage return character
define ( "SPACE_CHAR", " " ); //Space character
define ( "NON_GENERAL_SPACE_CHAR", "ï¿½ï¿½ï¿½\S" ); //Every char except space, tabulation, newline ecc...
/*Latin characters group constants*/
define ( "LETTER_CHAR", "ï¿½ï¿½ï¿½[a-zA-Z]" ); //Letters
define ( "UPPERCASE_LETTER", "ï¿½ï¿½ï¿½[A-Z]" ); //Uppercase letter
define ( "LOWERCASE_LETTER", "ï¿½ï¿½ï¿½[a-z]" ); //Lowercase letter
define ( "DIGIT_CHAR", "ï¿½ï¿½ï¿½\d" ); //Numbers
define ( "NON_DIGIT_CHAR", "ï¿½ï¿½ï¿½\D" ); //Every char except numbers
define ( "NON_LETTER_CHAR", "ï¿½ï¿½ï¿½\W" ); //Every char except letters
/*UNICODE mode characters group constants*/
define ( "UNICODE_LETTER_CHAR", "ï¿½ï¿½ï¿½\p{L}" ); //Letters
define ( "UNICODE_UPPERCASE_LETTER", "ï¿½ï¿½ï¿½\p{Lu}" ); //Uppercase letter
define ( "UNICODE_LOWERCASE_LETTER", "ï¿½ï¿½ï¿½\p{Ll}" ); //Lowercase letter
define ( "UNICODE_DIGIT_CHAR", "ï¿½ï¿½ï¿½\p{N}" ); //Numbers
define ( "UNICODE_NON_DIGIT_CHAR", "ï¿½ï¿½ï¿½\P{N}" ); //Every char except numbers
define ( "UNICODE_NON_LETTER_CHAR", "ï¿½ï¿½ï¿½\P{L}" ); //Every char except letters

class regExpBuilder {
	/**
	 * Regexp
	 * @var		string
	 * @access	private
	 */
	private $_regexp = "";
	/**
	 * Open groups counter
	 * @var		int
	 * @access	private
	 */
	private $_openGroups = 0;
	/**
	 * Modifiers array
	 * @var		array
	 * @access	private
	 */
	private $_modifiers = array ("i" => false, "m" => false, "s" => true, "u" => false );
	/**
	 * Internal log
	 * @var		array
	 * @access	private
	 */
	private $_log = array ();
	/**
	 * An array that associates the position of the capture and the capture name
	 * @var		array
	 * @access	private
	 */
	private $_capturingGroupsNames = array ();
	/**
	 * Error level flag
	 * @var		int
	 * @access	private
	 */
	private $_errorLevel = 1;
	/**
	 * Errors array
	 * @var		array
	 * @access	private
	 */
	private $_errors = array ();
	/**
	 * Condition stack array
	 * @var		array
	 * @access	private
	 */
	private $_conditionStack = array ();
	/**
	 * Number of capturing groups inside the regexp
	 * @var		int
	 * @access	public
	 */
	public $capturingGroups = 0;
	/**
	 * Class costructor. Set regexp modifiers
	 * @param 	string		$modifiers	Concatenation of modifier constants
	 * @access	public
	 */
	function __construct($modifiers = "") {
		$this->_logAndFix ( strtolower ( __FUNCTION__ ) );
		$this->_setModifiers ( $modifiers );
	}
	/**
	 * Set the error level. 1=errors are shown and stored in the internal array,0=errors are stored in the internal array but they are not shown
	 * @param 	string		$level	Error level
	 * @return	object		Class instance
	 * @access	public
	 */
	function setErrorLevel($level = 1) {
		$this->_errorLevel = $level;
		return $this;
	}
	/**
	 * Return the internal errors array
	 * @return	object		Class instance
	 * @access	public
	 */
	function getErrors($level = 1) {
		return $this->_errors;
	}
	/**
	 * Match the given string
	 * @param 	string		$text	Text to match
	 * @return	object		Class instance
	 * @access	public
	 */
	function match($text) {
		$this->openGroup ();
		$this->_regexp .= $this->_escape ( $text );
		return $this->closeGroup ();
	}
	/**
	 * Match one of the given characters
	 * @param 	string|array	$chars		Characters to match
	 * @return	object			Class instance
	 * @access	public
	 * @example	- Giving a single string:	matchOneOfTheseChars("abc")
				- Giving a multiple strings:matchOneOfTheseChars("a","b","c")
				- Giving an array:			matchOneOfTheseChars(array("a","b","c"))
	 */
	function matchOneOfTheseChars($chars) {
		$this->_logAndFix ( strtolower ( __FUNCTION__ ), "subject" );
		$args = func_get_args ();
		$chars = is_array ( $chars ) ? implode ( "", $chars ) : (count ( $args ) > 1 ? implode ( "", $args ) : $chars);
		if (strlen ( $chars ))
			$this->_regexp .= "[" . $this->_escape ( $chars, false ) . "]";
		return $this;
	}
	/**
	 * Match every character
	 * @return	object		Class instance
	 * @access	public
	 */
	function matchEverything() {
		$this->_logAndFix ( strtolower ( __FUNCTION__ ), "subject" );
		$this->_regexp .= ".";
		return $this;
	}
	/**
	 * Match every character except the given
	 * @param 	string|array	$chars		Characters to match
	 * @return	object		Class instance
	 * @access	public
	 * @example	- Giving a single string:	matchEveryCharExcept("abc")
				- Giving a multiple strings:matchEveryCharExcept("a","b","c")
				- Giving an array:			matchEveryCharExcept(array("a","b","c"))
	 */
	function matchEveryCharExcept($chars) {
		$this->_logAndFix ( strtolower ( __FUNCTION__ ), "subject" );
		$args = func_get_args ();
		$chars = is_array ( $chars ) ? implode ( "", $chars ) : (count ( $args ) > 1 ? implode ( "", $args ) : $chars);
		if (strlen ( $chars ))
			$this->_regexp .= "[^" . $this->_escape ( $chars, false ) . "]";
		return $this;
	}
	/**
	 * Match one of the given words
	 * @param 	string|array	$words		Words to match
	 * @return	object			Class instance
	 * @access	public
	 * @example	- Giving a single string:	matchOneOfTheseWords("test")
				- Giving a multiple strings:matchOneOfTheseWords("test","example")
				- Giving an array:			matchOneOfTheseWords(array("test","example"))
	 */
	function matchOneOfTheseWords($words) {
		$this->_logAndFix ( strtolower ( __FUNCTION__ ), "subject" );
		$args = func_get_args ();
		$words = is_array ( $words ) && count ( $words ) ? $words : $args;
		if (count ( $words )) {
			$apply = array ();
			foreach ( $words as $word )
				$apply [] = $this->_escape ( $word );
			$this->openGroup ();
			$this->_regexp .= implode ( "|", $apply );
			$this->closeGroup ();
		}
		return $this;
	}
	/**
	 * Match the line start
	 * @return	object			Class instance
	 * @access	public
	 */
	function matchLineStart() {
		$this->_logAndFix ( strtolower ( __FUNCTION__ ) );
		$this->_regexp .= "^";
		return $this;
	}
	/**
	 * Match the line end
	 * @return	object			Class instance
	 * @access	public
	 */
	function matchLineEnd() {
		$this->_logAndFix ( strtolower ( __FUNCTION__ ) );
		$this->_regexp .= "$";
		return $this;
	}
	/**
	 * Back reference. Match the result of a previous capturing group by name or position. The position count starts from 1
	 * @param 	int|string	$name		Name or position of the capturing group result to match
	 * @return	object		Class instance
	 * @access	public
	 * @example	capture("test")->match("abc")->closeCapture()->matchCapture("test"); Matches "abc" which is the result of the "test" capturing group
	 */
	function matchCapture($name) {
		$pos = false;
		//Check if the given name or position is associated to a capturing group
		if (is_int ( $name ) && isset ( $this->_capturingGroupsNames [$name] ))
			$pos = $name;
		else
			$pos = array_search ( $name, $this->_capturingGroupsNames );
		if ($pos !== false) {
			$this->openGroup ();
			$this->_regexp .= "\\" . $pos;
			$this->closeGroup ();
		} else
			$this->setError ( "Unknown capture name <b>$name</b>", __FUNCTION__ );
		return $this;
	}
	/**
	 * Set the frequency of the previous subject
	 * @param 	string|int	$min		The smaller limit of frequency, the exact frequency or one of the frequency or frequency limit constants
	 * @param 	int			$max		The higher limit of frequency
	 * @return	object			Class instance
	 * @access	public
	 * @example	- Exact frequency:			frequency(5) = match the subject 5 times
				- Limited frequency:		frequency(2,5) = match the subject between 2 and 5 times
				- frequency constants:		frequency(ONE_OR_MORE)	= match the subject if it's present one or more times
				- frequency limit constants:frequency(MORE_THEN,5) = match the subject if it's present more then 5 times
	 */
	function frequency($min = 1, $max = null) {
		if (! $this->_logAndFix ( strtolower ( __FUNCTION__ ), "frequency" ))
			return false;
		if (is_string ( $min ) && ! in_array ( $min, array (LESS_THEN, MORE_THEN, LESS_THEN_OR_EQUAL_TO, MORE_THEN_OR_EQUAL_TO ) ))
			$this->_regexp .= $min;
		elseif ($min === null && $max === null)
			$this->setError ( "You cannot set both limit to null", __FUNCTION__ );
		else {
			if ($min === LESS_THEN)
				$max = (( int ) $max) - 1;
			elseif ($min === MORE_THEN)
				$max = (( int ) $max) + 1;
			elseif ($min === LESS_THEN_OR_EQUAL_TO)
				$min = LESS_THEN;
			elseif ($min === MORE_THEN_OR_EQUAL_TO)
				$min = MORE_THEN;
			if ($min === null || $min === LESS_THEN)
				$condition = "," . (( int ) $max);
			elseif ($min === MORE_THEN)
				$condition = (( int ) $max) . ",";
			else
				$condition = (( int ) $min) . ($max !== null ? "," . (( int ) $max) : "");
			$this->_regexp .= "{" . $condition . "}";
		}
		return $this;
	}
	/**
	 * Set the frequency of the previous subject to one or more times. Same as frequency(ONE_OR_MORE)
	 * @return	object			Class instance
	 * @access	public
	 */
	function oneOrMoreTimes() {
		return $this->frequency ( ONE_OR_MORE );
	}
	/**
	 * Set the frequency of the previous subject to zero or more times. Same as frequency(ZERO_OR_MORE)
	 * @return	object			Class instance
	 * @access	public
	 */
	function zeroOrMoreTimes() {
		return $this->frequency ( ZERO_OR_MORE );
	}
	/**
	 * Set the frequency of the previous subject to zero or one time. Same as frequency(ZERO_OR_ONE)
	 * @return	object			Class instance
	 * @access	public
	 */
	function zeroOrOneTime() {
		return $this->frequency ( ZERO_OR_ONE );
	}
	/**
	 * Start a capturing group
	 * @param 	string		$name		An optional string to assign a name the capture group
	 * @return	object		Class instance
	 * @access	public
	 */
	function capture($name = null) {
		$this->_logAndFix ( strtolower ( __FUNCTION__ ) );
		$this->_openGroups ++;
		$this->_regexp .= "(";
		$this->capturingGroups ++;
		$this->_capturingGroupsNames [count ( $this->capturingGroups )] = $name;
		return $this;
	}
	/**
	 * End a capturing group
	 * @return	object		Class instance
	 * @access	public
	 */
	function closeCapture() {
		$this->_logAndFix ( strtolower ( __FUNCTION__ ), "subject" );
		if ($this->_openGroups > 0) {
			$this->_regexp .= ")";
			$this->_openGroups --;
		} else
			$this->_setError ( "There aren't other open groups", __FUNCTION__ );
		return $this;
	}
	/**
	 * Start a non-capturing group
	 * @return	object		Class instance
	 * @access	public
	 */
	function openGroup() {
		$this->_logAndFix ( strtolower ( __FUNCTION__ ) );
		$this->_openGroups ++;
		$this->_regexp .= "(?:";
		return $this;
	}
	/**
	 * End a non-capturing group
	 * @return	object		Class instance
	 * @access	public
	 */
	function closeGroup() {
		return $this->closeCapture ();
	}
	/**
	 * Start a condition for the previous subject or start a conditional pattern
	 * @param 	string		$condition		One of the condition constants
	 * @return	object		Class instance
	 * @access	public
	 */
	function ifItIs($condition) {
		if (in_array ( $condition, array (FOLLOWED_BY, NOT_FOLLOWED_BY, PRECEEDED_BY, NOT_PRECEEDED_BY ) )) {
			$this->_logAndFix ( strtolower ( __FUNCTION__ ), "startcondition::" . $condition );
			//Open two groups to easy implement conditional patterns
			$this->openGroup ();
			$this->openGroup ();
			$this->_regexp = substr ( $this->_regexp, 0, - 2 ) . $condition;
		} else
			$this->_setError ( "Uknown condition \"$condition\"", __FUNCTION__ );
		return $this;
	}
	/**
	 * Execute every function after this only if the prevoius condition is true
	 * @return	object		Class instance
	 * @access	public
	 */
	function then() {
		$this->closeGroup ();
		unset ( $this->_log [count ( $this->_log ) - 1] );
		$this->_logAndFix ( strtolower ( __FUNCTION__ ), "then" );
		$this->openGroup ();
		return $this;
	}
	/**
	 * Execute every function after this only if the prevoius condition is false
	 * @return	object		Class instance
	 * @access	public
	 */
	function otherwise() {
		$this->closeGroup ();
		unset ( $this->_log [count ( $this->_log ) - 1] );
		$this->_regexp .= "|";
		$this->_logAndFix ( strtolower ( __FUNCTION__ ), "otherwise" );
		$this->openGroup ();
		return $this;
	}
	/**
	 * End a condition or a conditional pattern
	 * @return	object		Class instance
	 * @access	public
	 */
	function closeIf() {
		$this->closeGroup ();
		$this->closeGroup ();
		$this->_logAndFix ( strtolower ( __FUNCTION__ ), "condition" );
		return $this;
	}
	/**
	 * Add the given string to the regexp without escape it. This can be useful for add pieces of code directly at the end of the regexp, 
	 * but it can break internal class controls and generate errors.
	 * @param	string		$regexp	Piece of code to add at the end of the regexp
	 * @return	object		Class instance
	 * @access	public
	 */
	function addCode($regexp) {
		$this->_regexp .= $regexp;
		//Try to understand what type of operation is the last one by checking the last two characters
		$end = strlen ( $regexp ) > 1 ? substr ( $regexp, - 2 ) : "-" . $regexp [0];
		$type = "subject";
		if ($end [0] != "\\") {
			switch ($end [1]) {
				case "*" :
				case "+" :
				case "?" :
				case "{" :
					$type = "frequency";
					break;
				case "^" :
				case "$" :
				case "(" :
				case "[" :
					$type = "sintax";
					break;
				case ":" :
					if ($end [0] == "?" && substr ( $regexp, - 3, 1 ) == "(")
						$type = "sintax";
					break;
			}
		}
		$this->_logAndFix ( strtolower ( __FUNCTION__ ), $type );
		return $this;
	}
	/**
	 * Returns the regexp constructed
	 * @return	string		Regexp
	 * @access	public
	 */
	function render() {
		$regexp = $this->_regexp;
		//Close every group that is still open
		if ($this->_openGroups > 0) {
			$this->_setError ( "There are " . $this->_openGroups . " groups still open", __FUNCTION__ );
			for($i = 0; $i < $this->_openGroups; $i ++)
				$this->closeGroup ();
		}
		//Add delimiters
		$regexp = "#" . $regexp . "#";
		//Apply modifiers
		if (count ( $this->_modifiers ))
			foreach ( $this->_modifiers as $sign => $mod )
				if ($mod)
					$regexp .= $sign;
		return $regexp;
	}
	/**
	 * Return and improves the regexp constructed. WARNING: This function is experimental and it might not work in some cases expecially for
	 * complicated regexp, use render() instead.
	 * @return	string		Regexp
	 * @access	public
	 */
	function renderAdvanced() {
		$regexp = $this->render ();
		//Replace ((?:abc)) with (abc)
		$regexp = preg_replace ( "#\(\(\?\:(.*?)\)\)#s", "(\\1)", $regexp );
		//Replace (?:(abc)) with (abc)
		$regexp = preg_replace ( "#\(\?\:\((?!\?)(.*?)\)\)#s", "(\\1)", $regexp );
		//Replace (?:abc) not followed by frequency sign with abc
		$regexp = preg_replace ( "#\(\?\:([^\(]*?)\)(?![\*\?\+\{])#s", "\\1", $regexp );
		return $regexp;
	}
	/**
	 * Test the regexp on the given strings
	 * @param 	string		...		Strings for the test
	 * @return	bool		Result of the test.
	 * @access	public
	 * @example	testOn("a","ab","abc") test the string on "a","ab" and "abc" string and return the result
	 */
	function testOn() {
		$args = func_get_args ();
		for($i = 0; $i < count ( $args ); $i ++)
			if (! preg_match ( $this->render (), ($this->_modifiers ["u"] ? utf8_encode ( $args [$i] ) : $args [$i]) . "" ))
				return false;
		return true;
	}
	/**
	 * Execute the regexp on the given string and then it returns the matches array
	 * @param 	string		$string		String for the execution
	 * @param 	mixed		$flags		Optional flags (http://www.php.net/manual/en/function.preg-match-all.php)
	 * @return	array		matches
	 * @access	public
	 */
	function execOn($string = "", $flags = PREG_PATTERN_ORDER) {
		preg_match_all ( $this->render (), ($this->_modifiers ["u"] ? utf8_encode ( $string ) : $string), $matches, $flags );
		//If UNICODE mode is enabled revert the utf8 encoding
		if ($this->_modifiers ["u"] && count ( $matches ))
			foreach ( $matches as $k => $val )
				if (! is_array ( $val ))
					$matches [$k] = utf8_decode ( $val );
				else
					foreach ( $val as $kk => $v )
						$matches [$k] [$kk] = utf8_decode ( $v );
		return $matches;
	}
	/**
	 * Replace every match in the given string with the given replacement
	 * @param 	string		$replacement	Replacement string
	 * @param 	string		$string			String for the replacement
	 * @param 	int			$limit			Maximum number of replacements. By default there's no limit.
	 * @return	string		the result of the replacement
	 * @access	public
	 */
	function replaceWith($replacement, $string, $limit = -1) {
		//If UNICODE mode is enabled convert the string and the replacement in UTF8
		if ($this->_modifiers ["u"]) {
			$string = utf8_encode ( $string );
			$replacement = utf8_encode ( $replacement );
		}
		$ret = preg_replace ( $this->render (), $replacement, $string, $limit );
		return $this->_modifiers ["u"] ? utf8_decode ( $ret ) : $ret;
	}
	/**
	 * Replace every match in the given string using the given callback
	 * @param 	string|fn	$callback	The name of the function or the result of create_function()
	 * @param 	string		$string		String for the replacement
	 * @param 	int			$limit		Maximum number of replacements. By default there's no limit.
	 * @return	string		the result of the replacement
	 * @access	public
	 */
	function replaceWithCallback($callback, $string, $limit = -1) {
		//If UNICODE mode is enabled wrap the callback in the utf8_encode function,
		//in this way the result will be in utf8 and then decoded in the previous charset
		if ($this->_modifiers ["u"]) {
			$string = utf8_encode ( $string );
			$callback = create_function ( '', 'return utf8_encode(call_user_func_array("' . $callback . '",func_get_args()));' );
		}
		$ret = preg_replace_callback ( $this->render (), $callback, $string, $limit );
		return $this->_modifiers ["u"] ? utf8_decode ( $ret ) : $ret;
	}
	/**
	 * Split string by the regexp
	 * @param 	string		$string		Subject
	 * @param 	int			$limit		Maximum number of substrings. By default there's no limit.
	 * @param 	int			$flags		Optional flags (http://www.php.net/manual/en/function.preg-split.php)
	 * @return	array		splitted string result
	 * @access	public
	 */
	function split($string, $limit = -1, $flags = 0) {
		$ret = preg_split ( $this->render (), ($this->_modifiers ["u"] ? utf8_encode ( $string ) : $string), $limit, $flags );
		if ($this->_modifiers ["u"] && count ( $ret ))
			foreach ( $ret as $k => $val )
				if (! is_array ( $val ))
					$ret [$k] = utf8_decode ( $val );
				else
					foreach ( $val as $kk => $v )
						$ret [$k] [$kk] = utf8_decode ( $v );
		return $ret;
	}
	/**
	 * Returns the entries of the given array that match the regexp
	 * @param 	array		$input		Subject array
	 * @param 	int			$flags		Optional flags (http://www.php.net/manual/en/function.preg-grep.php)
	 * @return	array		filtered array
	 * @access	public
	 */
	function grep($input, $flags = 0) {
		if ($this->_modifiers ["u"] && count ( $input ))
			foreach ( $input as $k => $v )
				$input [$k] = utf8_encode ( $v );
		$ret = preg_grep ( $this->render (), $input, $flags );
		if ($this->_modifiers ["u"] && count ( $ret ))
			foreach ( $ret as $k => $v )
				$ret [$k] = utf8_decode ( $v );
		return $ret;
	}
	/**
	 * Escape the given text
	 * @param 	string		$text	Text to escape
	 * @return	object		Escaped text
	 * @access	private
	 */
	function _escape($text = "", $allowClasses = true) {
		$text = preg_quote ( $text . "" );
		$text = str_replace ( "#", "\\#", $text );
		$text = preg_replace ( "#(?:ï¿½){3}\\\\(\\\\\w)(?:\\\\(\{\w{1,2})\\\\(\}))?#", "\\1\\2\\3", $text );
		if (! $allowClasses)
			$text = preg_replace_callback ( "#(?:ï¿½){3}\\\\\\[(.*?)\\\\\\]#", create_function ( '$match', 'return str_replace("\\-","-",$match[1]);' ), $text );
		else
			$text = preg_replace ( "#(?:ï¿½){3}\\\\\\[(.*?)\\\\\\]#", "[\\1]", $text );
		return $text;
	}
	/**
	 * Set regexp modifiers
	 * @param 	string		$modifiers	Concatenation of modifier constants
	 * @return	object		Class instance
	 * @access	private
	 */
	function _setModifiers($modifiers = "") {
		for($i = 0; $i < strlen ( $modifiers ); $i ++) {
			if (! preg_match ( "#\w#", $modifiers [$i] ))
				continue;
			$apply = true;
			if ($i != 0 && $modifiers [$i - 1] == "!")
				$apply = false;
			$this->_modifiers [$modifiers [$i]] = $apply;
		}
		return $this;
	}
	/**
	 * Add item to the internal log and fix some situations
	 * @param 	string		$function	function name
	 * @param	string		$type		function type
	 * @access	private
	 */
	function _logAndFix($function, $type = "sintax") {
		$last = end ( $this->_log );
		$last_key = count ( $this->_log ) - 1;
		switch ($type) {
			case "frequency" :
				//Frequency can be added only after a subject
				if (! count ( $this->_log ) || $last->type != "subject") {
					$this->_setError ( "The frequency can be set only after a subject, you are trying to set it after a \"" . $last->type . "\" method", $function );
					return false;
				}
				break;
			case "subject" :
				//If the last operation was matchEverything then the match must become "lazy"
				if ($last->function == "matcheverything")
					$this->_regexp .= "?";
				break;
			case "then" :
			case "otherwise" :
				//Fix lookbehind subpattern and wrong use of "then" and "otherwise" methods
				$lastcond = end ( $this->_conditionStack );
				if ($lastcond === false)
					return $this->_setError ( "\"$type\" method can be used only when there are open conditions", $function );
				if (isset ( $lastcond->lastSubject )) {
					$this->_regexp .= $lastcond->lastSubject;
					$this->_conditionStack [count ( $this->_conditionStack ) - 1]->used = true;
				}
				break;
			case "condition" :
				//Fix lookbehind assertion and wrong use of "closeIf"
				$lastcond = end ( $this->_conditionStack );
				if ($lastcond === false)
					return $this->_setError ( "\"$function\" method can be used only when there are open conditions", $function );
				if (isset ( $lastcond->used ) && ! $lastcond->used)
					$this->_regexp .= $lastcond->lastSubject;
				unset ( $this->_conditionStack [count ( $this->_conditionStack ) - 1] );
				break;
			default :
				//Put the condition in the stack
				if (strpos ( $type, "startcondition" ) !== false) {
					$parti = explode ( "::", $type );
					$cond = new stdClass ();
					$cond->type = $parti [1];
					if (in_array ( $parti [1], array (PRECEEDED_BY, NOT_PRECEEDED_BY ) )) {
						//Find last subject
						$open = 0;
						$snd = true;
						for($index = strlen ( $this->_regexp ) - 1; $index >= 0; $index --) {
							if (($this->_regexp [$index] == "(" || $this->_regexp [$index] == ")") && ($index == 0 || $this->_regexp [$index - 1] != "\\")) {
								$snd = false;
								if ($this->_regexp [$index] == "(")
									$open --;
								else
									$open ++;
							}
							if (! $open && ! $snd) {
								if (preg_match ( "#^\(\?:\(\?(?:=|!)#", substr ( $this->_regexp, $index ) ))
									$snd = true;
								else
									break;
							}
						}
						$cond->lastSubject = substr ( $this->_regexp, $index );
						$cond->used = false;
						$this->_regexp = substr ( $this->_regexp, 0, $index );
					}
					$this->_conditionStack [] = $cond;
				}
				break;
		}
		$log = new stdClass ();
		$log->function = $function;
		$log->type = $type;
		$log->current = $this->_regexp;
		$this->_log [] = $log;
		return true;
	}
	/**
	 * Set a new error
	 * @param 	string		$text	error text
	 * @param 	string		$func	function name
	 * @access	private
	 */
	function _setError($text, $func) {
		$error = "<b>Function $func</b>: $text";
		$this->_errors [] = $error;
		if ($this->_errorLevel)
			trigger_error ( $error, E_USER_WARNING );
	}
}
?>