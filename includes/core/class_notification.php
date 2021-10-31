<?php

class Notification {

    // TEST
    public static function get_list($data = []) {
        if (!$data['user_id']) {
            return response(error_response(2400, 'User not specified.'));
        }
        $query = "SELECT title, description, created, viewed FROM user_notifications WHERE user_id='".$data['user_id']."'";
        if (isset($data['viewed'])) {
            $query .= " AND viewed=1";
        }
        $query .= ";";
        $q = DB::query($query) or die (DB::error());
        if ($rows = DB::fetch_all($q)) {
            return $rows;
        }
        return response(error_response(2200, 'All notifacations are return.'));
    }

    public static function read_list($data = []) {
        if (!$data['user_id']) {
            return response(error_response(2400, 'User not specified.'));
        }
        DB::query("UPDATE user_notifications SET viewed=1 WHERE user_id='".$data['user_id']."';") or die (DB::error());
        return response(error_response(2200, 'All notifacations are read.'));
    }
    // your code here ...

}
