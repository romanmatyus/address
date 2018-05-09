<?php

namespace RM\Address\DI;

use Nette\Object;
use Nette\DI\CompilerExtension;
use RM\Address\IMode;
use RM\Address\IRouter;
use RM\Address\InvalidArgumentException;
use RM\Address\Modes\BaseMode;

/**
 * Extension for registration Address to Nette application.
 *
 * @author Roman Mátyus
 * @copyright (c) Roman Mátyus 2015
 * @license MIT
 */
class AddressExtension extends CompilerExtension
{
	/** @var [] */
	private $defaults = [
		'google' => [
			'language' => 'en',
			'country' => NULL,
			'key' => NULL,
			'limit' => 5,
		],
		'apiUrl' => 'api/address',
		'limit' => 5,
		'providersOrder' => [
			'google' => 'RM\Address\Provider\GoogleProvider',
		]
	];


	public function loadConfiguration()
	{
		$this->validateConfig($this->defaults);
		$builder = $this->getContainerBuilder();

		$addressDef = $builder->addDefinition($this->prefix('address'))
			->setFactory('RM\Address\Address')
			->addSetup('$service->limit = ?', [$this->config['limit']]);

		foreach ($this->config['providersOrder'] as $prefix => $factory) {
			$def = $builder->addDefinition($this->prefix($prefix))
				->setFactory($factory);

			if ($this->config[$prefix]) {
				foreach ($this->config[$prefix] as $key => $value) {
					$def->addSetup('$service->' . $key . ' = ?', [$value]);
				}
			}

			$addressDef->addSetup('addProvider', ['@' . $this->prefix($prefix)]);
		}

		if ($this->config['apiUrl'])
			$builder->addDefinition($this->prefix('presenter'))
				->setFactory('RM\Address\Application\UI\AddressPresenter');

	}

	function beforeCompile()
	{
		$builder = $this->getContainerBuilder();

		if ($this->config['apiUrl']) {
			$netteRouter = $builder->getDefinition('routing.router');
			$netteRouter->addSetup('$service[] = new Nette\Application\Routers\Route(\'' . $this->config['apiUrl'] . '\', ?);', ['Address:Address:address']);

			if ($builder->hasDefinition('nette.presenterFactory')) {
				$builder->getDefinition('nette.presenterFactory')
					->addSetup('setMapping', [
						['Address' => 'RM\Address\Application\UI\*Presenter'],
					]);
			}
		}
	}

}
