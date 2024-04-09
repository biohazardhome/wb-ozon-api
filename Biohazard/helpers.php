<?php

function array_split(array $array, int $parts) {
	return array_chunk($array, ceil(count($array) / $parts));
}