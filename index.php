<?php

require_once 'vk.php';
require_once 'set_functions.php';

$mysqli = new mysqli('hostname', 'username', 'password', 'database');
$vk = new VK();
$set_functions = new SetFunctions($vk);

if ($vk->data->type == 'message_new') {
    $peer_id = $vk->data->object->message->peer_id;
    $user_id = $vk->data->object->message->from_id;
    $text = mb_strtolower($vk->data->object->message->text, 'UTF-8');
    if ($peer_id > 2000000000 and $user_id > 0) {
        $date_created = file_get_contents('https://vk.com/foaf.php?id=' . $user_id);
        $date_created = explode('<ya:created dc:date="', $date_created);
        $date_created = explode('"', $date_created[1]);
        if (strtotime($date_created[0]) > date('U') - 1209600 and (strpos($text, 'http') !== false or strpos($text, 't.me') !== false or strpos($text, 'vk.me') !== false)) {
            $vk->messages_delete($vk->data->object->message->conversation_message_id, 1, $peer_id);
            $vk->messages_remove_chat_user($peer_id, $user_id);
            $vk->messages_send($peer_id, 'Пользователь, ' . $set_functions->getFullNameWithNotification($user_id) . ' исключён за рассылку');
            exit();
        }
        $mysqli->query("INSERT INTO `database`.`protection` (`ID`, `peer_id`, `user_id`, `conversation_message_id`, `time`) VALUES (NULL, '$peer_id', '$user_id', '{$vk->data->object->message->conversation_message_id}', '" . date('U') . "')");
    }
}