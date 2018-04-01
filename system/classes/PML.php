<?php defined('SYSPATH') or die('No direct script access');
/**
 * PML - Helper class for all miscellaneous functions
 *
 */
class PML
{
	/**
	 *
	 * Return the first instance of a key from an array using recursive search
	 * @param  string $needle array key to retrieve
	 * @param  array  $array  array to search
	 * @return mixed         value associated to the provided key
	 */
	public static function recursiveFind($needle, array $array)
	{
    	$iterator  = new RecursiveArrayIterator($array);

    	$recursive = new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::SELF_FIRST);

    	foreach ($recursive as $key => $value)
		{
        	if ($key === $needle)
			{
            	return $value;
        	}
    	}

		throw new Exception('Key: ' . $needle . ' doesn\'t exist anywhere in array.');
	}

	/**
	 * Compare two hashes in a time-invariant manner.
	 * Prevents cryptographic side-channel attacks (timing attacks, specifically)
	 *
	 * @param string $a cryptographic hash
	 * @param string $b cryptographic hash
	 * @return boolean
	 */
	public static function slow_equals($a, $b)
	{
		$diff = strlen($a) ^ strlen($b);
		for($i = 0; $i < strlen($a) AND $i < strlen($b); $i++)
		{
			$diff |= ord($a[$i]) ^ ord($b[$i]);
		}
		return $diff === 0;
	}
}
