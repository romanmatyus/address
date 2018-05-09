<?php

namespace RM\Address\Application\Responses;

use Nette;
use Nette\Utils\Json;

/**
 * @author Roman Mátyus
 * @copyright (c) Roman Mátyus 2015
 * @license MIT
 */
class JsonResponse extends Nette\Application\Responses\JsonResponse
{
	/**
	* Sends response to output.
	* @return void
	*/
	public function send(Nette\Http\IRequest $httpRequest, Nette\Http\IResponse $httpResponse)
	{
		$httpResponse->setContentType($this->contentType . '; charset=UTF-8');
		$httpResponse->setExpiration(FALSE);
		echo Json::encode($this->payload, Json::PRETTY);
	}
}
