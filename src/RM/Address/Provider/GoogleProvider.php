<?php

namespace RM\Address\Provider;

use Nette;
use Nette\Object;
use Nette\Utils\Json;
use Nette\Utils\Strings;
use RM\Address\IProvider;

/**
 * Google Maps provider of address.
 *
 * @author Roman Mátyus
 * @copyright (c) Roman Mátyus 2015
 * @license MIT
 */
class GoogleProvider extends Object implements IProvider
{
	/**
	 * Language code of response.
	 * List of possible values: https://developers.google.com/maps/faq#languagesupport
	 * @var string
	 */
	public $language = 'en';

	/**
	 * Define country where be addresses searched.
	 * List of possible values: https://developers.google.com/maps/faq#languagesupport
	 * @var string
	 */
	public $country;

	/**
	 * Google API key
	 * @var string
	 */
	public $key;

	/**
	 * Maximal count of addresses.
	 * @var integer
	 */
	public $limit = 5;

	/**
	 * Get address
	 * @param  array $attr ['street' => NULL, 'number' => NULL, 'city' => NULL, 'zip' => NULL, 'country' => NULL]
	 * @return [] of \StdClass
	 */
	public function find($attr)
	{
		$suggest = [];
		try {
			$data = Json::decode($this->getFromServer($attr));
		} catch (Nette\Utils\JsonException $e) {
			return [];
		}
		if (!isset($data->results))
			return [];
		foreach ($data->results as $result) {

			$address = [
				'address' => NULL,
				'label' => NULL,
				'street' => NULL,
				'number' => NULL,
				'city' => NULL,
				'zip' => NULL,
				'country' => NULL,
				'full' => FALSE,

				'street_number' => NULL,
				'premise' => NULL,
				'sublocality' => NULL,
			];

			foreach ($result->address_components as $component) {
				if (in_array('street_number', $component->types)) {
					$address['street_number'] = $component->long_name;
				} elseif (in_array('premise', $component->types)) {
					$address['premise'] = $component->long_name;
				} elseif (in_array('route', $component->types)) {
					$address['street'] = $component->long_name;
				} elseif (in_array('sublocality', $component->types)) {
					$address['sublocality'] = $component->long_name;
				} elseif (in_array('locality', $component->types)) {
					$address['city'] = $component->long_name;
				} elseif (in_array('postal_code', $component->types)) {
					$address['zip'] = Strings::replace($component->long_name, "~\s+~i", "");
				} elseif (in_array('country', $component->types)) {
					$address['country'] = strtolower($component->short_name);
				}
			}
			if ($address['city'] === NULL) {
				$address['city'] = $address['sublocality'];
			}

			$address['number'] = implode('/', array_filter([
				$address['premise'],
				$address['street_number'],
			]));
			unset($address['street_number']);
			unset($address['premise']);

			$address['street'] = array_merge(array_filter([
				$address['street'],
				$address['sublocality'],
				$address['city'],
			]), [''])[0];
			unset($address['sublocality']);

			$address['address'] = implode(' ', array_filter([
				$address['street'],
				$address['number']
			]));

			if (
				$address['street'] &&
				$address['number'] &&
				$address['city'] &&
				$address['zip'] &&
				$address['country']
			) {
				$address['full'] = TRUE;
			}

			$address['label'] = $result->formatted_address;
			$address['geometry'] = (object) [
				'location' => $result->geometry->location,
			];

			$suggest[] = (object) $address;

			if (count($suggest) >= $this->limit)
				break;
		}

		return $suggest;
	}

	public function getFromServer($attr)
	{
		return @file_get_contents($this->generateUrl($attr));
	}

	public function generateUrl($attr)
	{
		$attr = array_replace([
			'street' => NULL,
			'number' => NULL,
			'city' => NULL,
			'zip' => NULL,
			'country' => NULL,
		], $attr);

		// [[<street> ]<number>, ]<city>
		$term = implode(', ', array_filter([
			implode(' ', array_filter([
				$attr['street'],
				$attr['number'],
			])),
			$attr['city'],
		]));

		$data = [
			'address' => $term,
			'key' => $this->key,
		];

		if ($this->language)
			$data['language'] = $this->language;

		$components = [];
		//if ($this->country || $attr['country'])
		//	$components[] = 'country:' . (($attr['country']) ? $attr['country'] : $this->country);
		if ($attr['zip'])
			$components[] = 'postal_code:' . preg_replace('/[^0-9]/s', '', $attr['zip']);

		if ($components)
			$data['components'] = implode('|', $components);

		return 'https://maps.googleapis.com/maps/api/geocode/json?' . http_build_query($data);
	}
}
