<?php

namespace Smack\Di;

class Reference
{
	protected $type;
	protected $name;

	public function __construct(string $type, string $name)
	{
		 if(!in_array($type, ['parameter', 'service'])) {
		 	throw new Exception('invalid reference type.');
		 }

		 $this->type = $type;
		 $this->name = $name;
	}

	public function getType():string
	{
		return $this->type;
	}

	public function isType(string $type):bool
	{
		if ($type === $this->type) {
			return true;
		} else {
			return false;
		}
	}

	public function getName():string
	{
		return $this->name;
	}
}