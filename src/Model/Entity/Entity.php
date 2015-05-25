<?php

namespace AppCore\Model\Entity;

use Cake\ORM\Entity as CakeEntity;
use Cake\Utility\Hash;

class Entity extends CakeEntity {

	/**
	 * 
	 */
	public function hasErrors()
	{
		if(count($this->errors()) > 0)
			return true;
		return false;
	}

	/**
	 * 
	 */
	public function getErrorMessages()
	{
		if(!$this->hasErrors())
			return false;
		$messages = Hash::extract($this->errors(), '{s}.{s}');
		return $messages;
	}
}