<?php

namespace Utility\Payment\External\GoogleCheckout;

class Config {
	/**
	 * This value is used to instruct Google to initiate charges to the
	 * purchaser's
	 * credit card.
	 */
	const CHARGE = 2;
	/**
	 * This value is used to instruct Google not to charge the purchaser's
	 * credit card.
	 */
	const DONTCHARGE = 1;
	/**
	 * This value is used to instruct Google not to charge the purchaser's
	 * credit card
	 * YET.
	 */
	const WAIT = 0;
}