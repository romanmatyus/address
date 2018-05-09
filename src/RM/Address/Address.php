<?php

namespace RM\Address;

use Nette\Object;

/**
 * @author Roman Mátyus
 * @copyright (c) Roman Mátyus 2015
 * @license MIT
 */
class Address extends Object
{
	/** @var int */
	public $limit = 5;

	/** @var [] of IProvider */
	private $providers = [];

	public function addProvider(IProvider $provider)
	{
		$this->providers[] = $provider;
	}

	public function find($attr)
	{
		$response = [];
		foreach ($this->providers as $provider)
			$response = array_merge($response, $provider->find($attr));

		usort($response, function ($a, $b) {
			return ($a->full < $b->full) ? 1 : -1;
		});

		return array_slice($response, 0, $this->limit);
	}
}
