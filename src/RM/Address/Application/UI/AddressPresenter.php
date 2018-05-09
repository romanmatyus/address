<?php

namespace RM\Address\Application\UI;

use NetteModule\MicroPresenter;
use Nette\Application\UI\Presenter;
use RM\Address\Address;
use RM\Address\Application\Responses\JsonResponse;

/**
 * NettePresenter for send JSON response with address.
 *
 * @author Roman Mátyus
 * @copyright (c) Roman Mátyus 2015
 * @license MIT
 */
class AddressPresenter extends Presenter
{
	/** @var Address @inject */
	public $address;

	public function actionAddress($street = NULL, $number = NULL, $city = NULL, $zip = NULL, $country = NULL)
	{
		$this->sendResponse(new JsonResponse($this->address->find([
			'street' => $street,
			'number' => $number,
			'city' => $city,
			'zip' => $zip,
			'country' => $country,
		])));
	}
}
