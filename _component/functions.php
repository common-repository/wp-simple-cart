<?php
/**
 * デバッグ用関数
 *
 */
if (!function_exists('a')) {
    function a() {
        if (!class_exists('dBug', false)) {
            require_once(WP_PLUGIN_DIR . '/wp-simple-cart/_component/debug/dBug.php');
        }
        foreach (func_get_args() as $v) new dBug($v);
    }
}

/**
 * デバッグ用関数
 *
 */
if (!function_exists('d')) {
    function d() {
        echo '<pre style="background:#fff;color:#333;border:1px solid #ccc;margin:2px;padding:4px;font-family:monospace;font-size:12px">';
        foreach (func_get_args() as $v) var_dump($v);
        echo '</pre>';
    }
}

/**
 * メール送信用関数
 *
 */
if (!function_exists('sc_mail')) {
    function sc_mail($params) {
        global $sc_mode;
        if (!isset($params['to']) || $params['to']=='') {
            return false;
        }

        if ($sc_mode==SC_MODE_BP) {
            $pmessage = new BP_Messages_Message;
            $pmessage->sender_id = $params['from'];
            $pmessage->subject = $params['subject'];
            $pmessage->message = $params['body'];
            $pmessage->thread_id = 0;
            $pmessage->date_sent = time();
            $pmessage->message_order = 0;
            $pmessage->sender_is_group = 0;
            $pmessage->recipients = array($params['to']);
            $pmessage->send();
            return $pmessage;
        }
        else {
            $from = SC_ADMIN_FROM;
            $mail_headers = "MIME-Version: 1.0\n" . "From: \"{$from}\" <{$from}>\n";
            @wp_mail($params['to'], $params['subject'], $params['body'], $mail_headers);
            return true;
        }
    }
}

/**
 * メッセージ情報取得用関数
 *
 */
if (!function_exists('sc_get_message_thread')) {
    function sc_get_message_thread($params) {
        global $wpdb, $bp, $sc_mode;
        if (!isset($params['thread_id']) || $params['thread_id']=='') {
            return false;
        }

        if (!isset($sc_mode)) {
            $sc_options = SimpleCartModel::getOptions();
            $sc_mode = $sc_options['sc_buddypress'];
        }

        $thread_info = array();
        if ($sc_mode==SC_MODE_BP) {
            //new BP_Messages_Thread($params['thread_id'], true) はログインしたユーザーに関連するメッセージのみの取得
            $thread = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$bp->messages->table_name_threads} WHERE id = %d", $params['thread_id'] ) );

            if ($thread) {
                $message_ids = maybe_unserialize($thread->message_ids);
                $thread_info['message_list_count']  = count($message_ids);
                sort($message_ids);
                $thread_info['message_ids']  = $message_ids;
                $first_message_id = $message_ids[0];
                $thread_info['first_message_id']    = $first_message_id;

                $first_message_info = $wpdb->get_results( "SELECT * FROM {$bp->messages->table_name_messages} WHERE id ={$first_message_id}");
                $first_message_info = $first_message_info[0];
                $thread_info['thread_subject']    = $first_message_info->subject;
                $sender_id = $first_message_info->sender_id;
                $thread_info['thread_sender_id']  = $sender_id;
                $thread_info['thread_post_date']    = $first_message_info->date_sent;
                $thread_info['thread_last_post_date']    = $thread->last_post_date;

                //送信・受信者取得　２人のやり取りを想定　（３人以上は想定しません）
                $recipients = $wpdb->get_col( $wpdb->prepare( "SELECT user_id FROM {$bp->messages->table_name_recipients} WHERE thread_id = %d and user_id <> %d", $params['thread_id'], $sender_id ) );
                $receiver_id = $recipients[0];
                if(!isset($receiver_id)) {
                    //送信者＝受信者
                    $receiver_id = $sender_id;
                }
                $thread_info['thread_receiver_id']  = $receiver_id;

                if (!empty($message_ids)) {
                    $message_ids = implode( ',', $message_ids );
                    //メッセージ一覧取得
                    $tmp_message_list = $wpdb->get_results( "SELECT * FROM {$bp->messages->table_name_messages} WHERE id IN (" . $wpdb->escape($message_ids) . ") " . $where_keys . " ORDER BY id" );

                    $thread_info['thread_sender_id']  = $sender_id;
                    foreach ($tmp_message_list as $tmp_message_info) {
                        $message_info = array();
                        $message_info['id'] = $tmp_message_info->id;
                        $message_info['sender_id']   = $tmp_message_info->sender_id;
                        if ($tmp_message_info->sender_id != $sender_id) {
                            $message_info['receiver_id'] = $sender_id;
                        }
                        else {
                            $message_info['receiver_id'] = $receiver_id;
                        }
                        $message_info['sender_nm']      = bp_core_get_username($message_info['sender_id']);
                        $message_info['receiver_nm']    = bp_core_get_username($message_info['receiver_id']);

                        //受信者の指定がある場合
                        if (count($receiver_id_list) > 0) {
                            if (!in_array($message_info['receiver_id'], $receiver_id_list)) {
                                continue;
                            }
                        }
                        $message_info['subject']    = $tmp_message_info->subject;
                        $message_info['message']    = $tmp_message_info->message;
                        $message_info['date_sent']  = $tmp_message_info->date_sent;
                        $message_list[] = $message_info;
                    }

                    $thread_info['message_list']  = $message_list;
                }
            }
            return $thread_info;
        }
    }
}
