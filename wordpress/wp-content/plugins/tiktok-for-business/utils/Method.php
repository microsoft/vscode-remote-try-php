<?php

/**
 * Abstract class used as enum for utility method to determine catalog product_id
 */
abstract class Method {

	const CATALOG       = 0;
	const DELETE        = 1;
	const ADDTOCART     = 2;
	const PURCHASE      = 3;
	const STARTCHECKOUT = 4;
	const VIEWCONTENT   = 5;
}
