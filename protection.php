<?php

require_once 'vk.php';
require_once 'set_functions.php';

$mysqli = new mysqli('hostname', 'username', 'password', 'database');

$vk = new VK();
$set_functions = new SetFunctions($vk);

$database = $mysqli->query("SELECT DISTINCT `peer_id` FROM `database`.`protection`");
while ($row = mysqli_fetch_assoc($database)) {
    $messages_get_conversation_members = $vk->messages_get_conversation_members($row['peer_id'], 'deactivated');
    for ($i = 0; $i < count($messages_get_conversation_members->response->profiles); $i++) {
        if (isset($messages_get_conversation_members->response->profiles[$i]->deactivated)) {
            if ($messages_get_conversation_members->response->profiles[$i]->deactivated == "banned") {
                $protection = $mysqli->query("SELECT `conversation_message_id` FROM `database`.`protection` WHERE `peer_id` = {$row['peer_id']} AND `user_id` = {$messages_get_conversation_members->response->profiles[$i]->id}");
                $cmids = '';
                while ($conversation_message_id = mysqli_fetch_assoc($protection)) {
                    $cmids = $cmids . $conversation_message_id['conversation_message_id'] . ',';
                }

                $vk->messages_delete($cmids, 1, $row['peer_id']);
                $vk->messages_remove_chat_user($row['peer_id'], $messages_get_conversation_members->response->profiles[$i]->id);
                $vk->messages_send($row['peer_id'], 'Пользователь, ' . $set_functions->getFullNameWithNotification($messages_get_conversation_members->response->profiles[$i]->id) . ' вёл себя подозрительно.');
                $mysqli->query("DELETE FROM `database`.`protection` WHERE `peer_id` = {$row['peer_id']} AND `user_id` = {$messages_get_conversation_members->response->profiles[$i]->id}");
            }
        }
    }
}

$mysqli->query("DELETE FROM `database`.`protection` WHERE `time` < " . (date('U') - 7200));