<?php
namespace Database\DBAL;

/* ENUM */
abstract class Fetch {
	const ASSOC = 1;
	const NUM = 2;
	const FIRST = 3;
	const ALL_ASSOC = 4;
	const ALL_NUM = 5;
	const ALL_SINGLE = 6;
}