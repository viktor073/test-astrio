<?php

$arrayTags = ["<a>", "<b>", "</a>", "</b>", "<div>", "</div>",  "<span>", "<span>", "</span>", "<span>", "</span>", "</span>"];


function test(array $arrayTags): bool
{
	$arrayLowercaseTags = ['<a>', '<b>', '<big>', '<br>', '<em>',
						'<i>', '<img>', '<small>', '<span>',
						'<strong>', '<sub>', '<sup>'];

	/**
	 * [checking array for emptiness]
	 */
	if (empty($arrayTags)) {
		//echo "checking array for emptiness";
	 	return false;
	}

	foreach ($arrayTags as $arrayTag => &$value) {
		/**
		 * [checking tag for closing tag]
		 */
		if ($value[1] == '/') {
			//echo "[not open tag for closing tag]";
			return false;
		}

		/**
		 * [search closing tag]
		 */
		$closeTag = '</' . substr($value, 1);
		$keyСloseTag = array_search($closeTag, $arrayTags);

		if ($keyСloseTag === false) {
			//echo "[not closing tag]";
		 	return false;
		}

		/**
		 * [checking nested tags]
		 */
		$arrayChildTags = array_slice($arrayTags, $arrayTag + 1, $keyСloseTag - $arrayTag - 1);
		if (!empty($arrayChildTags)) {

			/**
			 * [ checking lowercase tag]
			 */
			$lowercaseTag = array_search($value, $arrayLowercaseTags);
			if ($lowercaseTag !== false) {
				foreach ($arrayChildTags as $arrayChildTag => $value) {
					/**
					 * [checking nested tag for lowercase tag]
					 */
					$childLowercaseTag = array_search($value, $arrayLowercaseTags);
					if ($childLowercaseTag == false and $value[1] != '/') {
						//echo "[nested tag not lowercase tag]";
						return false;
					}
				}
			}

			/**
			 * [checking the structure of nested tags]
			 */
			if (!test($arrayChildTags)) {
				//echo "[error the structure of nested tags]";
				return false;
			}
		}

		unset($arrayTags[$arrayTag], $arrayTags[$keyСloseTag]);
	}

	return true;
};

var_dump(test($arrayTags));
