<?php

class MyCript
{
	private $iv =  '1A2B3C4D5E6F7E47';
    private $key = 'A1B2C3D4E5F6E7AB';

    function __construct()
    {
			//
    }
    /**
     * @param string $str
     * @param bool $isBinary whether to encrypt as binary or not. Default is: false
     * @return string Encrypted data
     */
    function encrypt($str, $isBinary = false)
    {
			$iv = $this->iv;
			$str = $isBinary ? $str : utf8_decode($str);
			$td = mcrypt_module_open('rijndael-128', ' ', 'cbc', $iv);
			mcrypt_generic_init($td, $this->key, $iv);
			$encrypted = mcrypt_generic($td, $str);
			mcrypt_generic_deinit($td);
			mcrypt_module_close($td);
			return $isBinary ? $encrypted : bin2hex($encrypted);
    }

    function vcgEncrypt($str, $key_one, $key_two, $isBinary = false)
    {
        $iv = $key_one;
        $key = $key_two;
        $str = $isBinary ? $str : utf8_decode($str);
        $td = mcrypt_module_open('rijndael-128', ' ', 'cbc', $iv);
        mcrypt_generic_init($td, $key, $iv);
        $encrypted = mcrypt_generic($td, $str);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        return $isBinary ? $encrypted : bin2hex($encrypted);
    }

    /**
     * @param string $code
     * @param bool $isBinary whether to decrypt as binary or not. Default is: false
     * @return string Decrypted data
     */
    function decrypt($code, $isBinary = false)
    {
			$code = $isBinary ? $code : $this->hex2bin($code);
			$iv = $this->iv;
			$td = mcrypt_module_open('rijndael-128', ' ', 'cbc', $iv);
			mcrypt_generic_init($td, $this->key, $iv);
			$decrypted = mdecrypt_generic($td, $code);
			mcrypt_generic_deinit($td);
			mcrypt_module_close($td);
			return $isBinary ? trim($decrypted) : utf8_encode(trim($decrypted));
    }

    function vcgDecrypt($code, $key_one, $key_two, $isBinary = false)
    {
        $iv = $key_one;
        $key = $key_two;
        $code = $isBinary ? $code : $this->hex2bin($code);
        $td = mcrypt_module_open('rijndael-128', ' ', 'cbc', $iv);
        mcrypt_generic_init($td, $key, $iv);
        $decrypted = mdecrypt_generic($td, $code);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        return $isBinary ? trim($decrypted) : utf8_encode(trim($decrypted));
    }
	
	function pbkdf2($p, $s, $c, $dk_len, $algo = 'sha1') {

	  // experimentally determine h_len for the algorithm in question

	  static $lengths;
	  if (!isset($lengths[$algo])) { $lengths[$algo] = strlen(hash($algo, null, true)); }
	  $h_len = $lengths[$algo];

	  if ($dk_len > (pow(2, 32) - 1) * $h_len) {
		return false; // derived key is too long
	  } else {
		$l = ceil($dk_len / $h_len); // number of derived key blocks to compute
		$t = null;
		for ($i = 1; $i <= $l; $i++) {
		  $f = $u = hash_hmac($algo, $s . pack('N', $i), $p, true); // first iterate
		  for ($j = 1; $j < $c; $j++) {
			$f ^= ($u = hash_hmac($algo, $u, $p, true)); // xor each iterate
		  }
		  $t .= $f; // concatenate blocks of the derived key
		}
		return substr($t, 0, $dk_len); // return the derived key of correct length
	  }
	}
	
	function addpadding($string)
	{
	  $blocksize = 16;
	  $len = strlen($string);
	  $pad = $blocksize - ($len % $blocksize);
	  $string .= str_repeat(chr($pad), $pad);

	  return $string;
	}
	
    protected function hex2bin($hexdata)
    {
        $bindata = '';
        for ($i = 0; $i < strlen($hexdata); $i += 2) {
            $bindata .= chr(hexdec(substr($hexdata, $i, 2)));
        }
        return $bindata;
    }

    function addPadding2($value){
	$pad = 16 - (strlen($value) % 16);
	    	return $value.str_repeat(chr($pad), $pad);
	}
	 
	function stripPadding($value){
	  	$pad = ord($value[($len = strlen($value)) - 1]);
	  	return $this->paddingIsValid($pad, $value) ? substr($value, 0, $len - $pad) : $value;
	}
	 
	function paddingIsValid($pad, $value){
	    	$beforePad = strlen($value) - $pad;
	    	return substr($value, $beforePad) == str_repeat(substr($value, -1), $pad);
	}
}
?>
