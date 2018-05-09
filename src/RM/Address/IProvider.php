<?php

namespace RM\Address;

/**
 * Interface for address providers.
 *
 * @author Roman Mátyus
 * @copyright (c) Roman Mátyus 2015
 * @license MIT
 */
interface IProvider
{
	/**
	 * Get address
	 * @param  array|string $attr ["street" => NULL, "number" => NULL, "city" => NULL, "zip" => NULL]
	 * @return array ["street" => "", "number" => "", "city" => "", "zip" => ""]
	 */
	public function find($attr);
}
