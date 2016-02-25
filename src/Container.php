<?php

namespace Smack\Di;

use \Smack\Di\Exception\ContainerException;
use \Smack\Di\Exception\NotFoundException;

class Container
{
	protected $parameters;
	protected $services;
	protected $storage;

	public function __construct(array $parameters = [], array $services = [])
	{
		$this->parameters = $parameters;
		$this->services = $services;
	}

	public function get(string $id)
	{
		return $this->storage[$id] ?? $this->resolveService($id);
	}

	public function has(string $id)
	{
		return isset($this->services[$id]);
	}

	public function resolveService(string $id)
	{
		if (!$this->has($id)) {
			throw new NotFoundException('invalid identifier');
		}

		$config = $this->services[$id];

		if (!isset($config['class'])) {
			throw new ContainerException('invalid service config');
		}

		$service = (new \ReflectionClass($config['class']));

		if (isset($config['args'])) {
			$service = $service->newInstanceArgs($this->resolveArgs($config['args']));
		} else {
			$service = $service->newInstance();
		}

		$this->storage[$id] = isset($config['calls']) ? $this->initService($service, $config['calls']) : $service;
		return $this->storage[$id];
	}

	public function resolveArgs($args)
	{
		$arguments = [];
		foreach ($args as $arg) {
			if ($arg instanceOf Reference) {
				if ($arg->isType('service')) {
					$arguments[] = $this->get($arg->getName());
				} else {
					$arguments[] = $this->getParam($arg->getName());
				}
			} else {
				$arguments[] = $arg;
			}
		}

		return $arguments;
	}

	public function initService($service, array $calls)
	{
		foreach ($calls as $call) {
			call_user_func_array([$service, $call['method']], $this->resolveArgs($call['params']));
		}

		return $service;
	}

	public function getParam($key)
	{
 		$current = $this->parameters;
  		$p = strtok($key, '.');

		while ($p !== false) {
		    $current = $current[$p] ?? null;
		    $p = strtok('.');
		}

  		return $current;
	}

	public function getServiceConfig()
	{
		return $this->services;
	}
}