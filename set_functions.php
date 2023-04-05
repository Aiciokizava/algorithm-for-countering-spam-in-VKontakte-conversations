<?php

class SetFunctions
{
	public $vk;

	public function __construct($vk)
	{
		$this->vk = $vk;
	}

    public function getFullNameWithNotification($user_id): string
    {
        $users_get = $this->vk->users_get($user_id, '');
        return '[id' . $user_id . '|' . $users_get->response[0]->first_name . ' ' . $users_get->response[0]->last_name . ']';
    }
}