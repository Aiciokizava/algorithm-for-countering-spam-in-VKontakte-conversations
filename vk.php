<?php

require_once 'config.php';

class VK
{
    public $data;

    public function __construct()
    {
        $this->data = json_decode(file_get_contents('php://input'), true);
        if ($this->data['type'] == 'confirmation') exit($this->key);
        else print('ok');
    }

    // Отправка JSON
    public function call($method, $params = [])
    {
        $params['access_token'] = Config::TOKEN;
        $params['v'] = Config::VERSION;
        $url = 'https://api.vk.com/method/' . $method . '?' . http_build_query($params);
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $json = curl_exec($curl);
        curl_close($curl);
        return json_decode($json);
    }

    // Методы ВК
    public function messages_send($peer_id, $message)
    {
        return VK::call('messages.send', [
            'random_id' => rand(),
            'peer_id' => $peer_id,
            'message' => $message
        ]);
    }

    public function messages_delete($cmids, $delete_for_all, $peer_id){
        return VK::call('messages.delete', [
            'cmids' => $cmids,
            'group_id' => Config::GROUP_ID,
            'delete_for_all' => $delete_for_all,
            'peer_id' => $peer_id
        ]);
    }

    public function messages_remove_chat_user($peer_id, $user_id) {
        return VK::call('messages.removeChatUser', [
            'chat_id' => $peer_id - 2000000000,
            'user_id' => $user_id,
            'member_id' => $user_id
        ]);
    }

    public function messages_get_conversation_members($peer_id, $fields) {
        return VK::call('messages.getConversationMembers', [
            'peer_id' => $peer_id,
            'fields' => $fields
        ]);
    }

    public function users_get($user_ids, $fields) {
        return VK::call('users.get', [
            'user_ids' => $user_ids,
            'fields' => $fields
        ]);
    }
}