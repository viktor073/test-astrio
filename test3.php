<?php

function test($array_tags)
{
	foreach ($array_tags as $array_tag => &$value) {
		if (isCloseTag($value)) {
			return false;
		}

		$searchValue = '</' . substr($value, 1);
		$key = array_search($searchValue, $array_tags);

		if ($key === false) {
		 	return false;
		}

		unset($array_tags[$array_tag], $array_tags[$key]);
	}

	return true;
};

function isCloseTag(string $tag)
{
	return $tag[1] == '/';
}