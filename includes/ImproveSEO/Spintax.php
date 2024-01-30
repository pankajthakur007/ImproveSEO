<?php

namespace ImproveSEO;

class Spintax
{

		
	/**
	 * Creates spintax array from text.
	 *
	 * @param  mixed $content
	 * @param  mixed $parent_key
	 * @return void
	 */
	public static function parse($content, $parent_key = null) 
	{
		preg_match_all("/\{(((?>[^\{\}]+)|(?R))*)\}/x", $content, $spintaxes);
		
		foreach ($spintaxes[0] as $key => $value) {
			//$value = "/". preg_quote($value, '/') ."/";
			//$content = preg_replace($value, '@@'. $key, $content, 1);
			$content = str_replace($value, '@@'. $key, $content);
		}
		
		$template = $content;
		$vars = array();

		// 
		if (strstr($content, '|')) {
			$strokes = explode('|', $content);
			$values = array();
			
			foreach ($strokes as $value) {
				if (strstr($value, '@@')) {
					preg_match_all("/\@\@(\d+)/i", $value, $varKeys);
					$string = $value;

					// Unique keys
					//$spintaxes[0] = array_unique($spintaxes[0]);
					//var_dump($spintaxes[0]);
					
					foreach ($varKeys[1] as $vark) {
						$string = str_replace("@@". $vark, $spintaxes[0][$vark], $string);
					}
					
					$values[] = self::parse($string);
				} else {
					$values[] = $value;
				}
			}

			return $values;
		} else {
			foreach ($spintaxes[1] as $key => $value) {
				if (strstr($value, '{')) {
					$parsed = self::parse($value, $key);
					$vars[$key] = $parsed;
				} else {
					$vars[$key] = explode('|', $value);
				}
			}

			return array(
				'template' => $template,
				'vars' => $vars
			);
		}
	}

	
	/**
	 * Counts available unique texts from spintax.
	 *
	 * @param  mixed $spintax
	 * @param  mixed $level
	 * @return void
	 */
	public static function count($spintax, $level = 0) 
	{
		$vars = 0;
		$values = 0;

		$variables = isset($spintax['vars']) ? $spintax['vars'] : $spintax;
		foreach ($variables as $key => $var) {
			if (is_array($var)) {
				if ($level > 0) {
					$vars += self::count($var, $level + 1);
				} else {
					$vars = ($vars == 0 ? 1 : $vars) * self::count($var, $level + 1);
				}
			} else {
				$vars++;
			}
		}

		return $level == 0 && $vars == 0 ? 1 : $vars;
	}


	/**
	 * Build spintax variables table.
	 *
	 * @param  mixed $spintax
	 * @return void
	 */
	public static function build($spintax) 
	{
		$table = array();

		$variables = isset($spintax['vars']) ? $spintax['vars'] : $spintax;
		foreach ($variables as $key => $var) {
			$subitems = array();
			if(is_array($var)){
				foreach ($var as $key_vr => $vr) {
					if (isset($vr['template'])) {
						$subitems[$key_vr] = self::build($vr);
					}
				}
			}

			$table[] = array(
				'item' => 1,
				'max' => (is_array($var)) ? count($var) : 1,
				'subitems' => $subitems
			);
		}

		return $table;
	}
	
	/**
	 * __drop
	 *
	 * @param  mixed $table
	 * @return void
	 */
	public static function __drop($table) 
	{
		if (!sizeof($table)) return array();

		foreach ($table as &$item) {
			foreach ($item as &$itm) {
				$itm['item'] = 1;
				$itm['subitems'] = self::__drop($itm['subitems']);
			}
		}

		return $table;
	}

		
	/**
	 * __inc
	 *
	 * @param  mixed $table
	 * @param  mixed $next
	 * @return void
	 */
	public static function __inc($table, $next) 
	{
		for ($i = $next; $i <= sizeof($table); $i++) {
			if (isset($table[$i]['subitems'][$table[$i]['item'] - 1])) {
				$result = self::__subinc($table[$i]['subitems'][$table[$i]['item'] - 1]);
				if ($result !== false) {
					$table[$i]['subitems'][$table[$i]['item'] - 1] = $result;

					return $table;
				}
			}

			$table[$i]['item']++;

			if ($table[$i]['item'] <= $table[$i]['max']) return $table;
			else {
				$table[$i]['item'] = 1;
				$table[$i]['subitems'] = self::__drop($table[$i]['subitems']);
			}
		}

		return $table;
	}


		
	/**
	 * __subinc
	 *
	 * @param  mixed $table
	 * @return void
	 */
	public static function __subinc($table) 
	{
		$result = false;

		foreach ($table as $key => &$item) {
			// Digging through subitems
			if (isset($item['subitems'][$item['item'] - 1])) {
				$result = self::__subinc($item['subitems'][$item['item'] - 1]);
				if ($result !== false) {
					$item['subitems'][$item['item'] - 1] = $result;
					return $table;
				}
			}

			if ($item['item'] < $item['max']) {
				$item['item']++;

				return $table;
			}
			else {
				continue;
			}
		}

		return $result;
	}

	 	
	/**
	 *  Math spintax variables index on table
	 *
	 * @param  mixed $spintax
	 * @param  mixed $table
	 * @param  mixed $iteration
	 * @param  mixed $maxlength
	 * @return void
	 */
	public static function math($spintax, $table, $iteration = 1, $maxlength = null) 
	{
		if ($maxlength && $iteration > $maxlength) {
			throw new \Exception('Maximum number of unique text reached.');
		}

		for ($i = 1; $i < $iteration; $i++) {
			$iterate = true;

			if (isset($table[0]['subitems'][$table[0]['item'] - 1])) {
				$result = self::__subinc($table[0]['subitems'][$table[0]['item'] - 1]);
				if ($result !== false) {
					$iterate = false;
					$table[0]['subitems'][$table[0]['item'] - 1] = $result;
				}
			}

			if ($iterate) {
				$table[0]['item']++;

				if ($table[0]['item'] > $table[0]['max'] && isset($table[1])) {
					$table[0]['item'] = 1;
					$table[0]['subitems'] = self::__drop($table[0]['subitems']);

					$table = self::__inc($table, 1);
				}
			}
		}

		return $table;
	}

		
	/**
	 * make
	 *
	 * @param  mixed $content
	 * @param  mixed $iteration
	 * @param  mixed $spintax
	 * @param  mixed $rand
	 * @return void
	 */
	public static function make($content, $iteration, $spintax = null, $rand = true) 
	{
		$spintax = $spintax ? $spintax : self::parse($content);
		$table = self::build($spintax);
		$max = self::count($spintax);
		if (!$rand) $math = self::math($spintax, $table, $iteration, $max);

		return self::renderTemplate($spintax['template'], $spintax['vars'], !$rand ? $math : null);
	}

	
	/**
	 * renderTemplate
	 *
	 * @param  mixed $template
	 * @param  mixed $vars
	 * @param  mixed $math
	 * @return void
	 */
	public static function renderTemplate($template, $vars, $math = null) 
	{
		preg_match_all("/\\@\\@(\d+)/i", $template, $keys);

		if (sizeof($keys[1]) > 0) {
			foreach ($keys[1] as $key) {
				$index = $math ? $math[$key]['item'] - 1 : rand(0, sizeof($vars[$key]) - 1);
				$renderValue = $vars[$key][$index];

				if (is_array($renderValue) && isset($renderValue['template'])) {
					$renderValue = self::renderTemplate($renderValue['template'], $renderValue['vars']);
				}
				
				$template = str_replace("@@$key", $renderValue, $template);
			}
		}
		
		return $template;
	}

		
	/**
	 * geo
	 *
	 * @param  mixed $text
	 * @param  mixed $geo
	 * @return void
	 */
	public static function geo($text, $geo) 
	{
		if (is_array($geo) && sizeof($geo)) {
			foreach ($geo as $tag => $value) {
				$text = preg_replace("/@$tag(?!short)/", $value, $text);
			}
		}

		return $text;
	}
}