<?php

class User {

    // GENERAL

    public static function user_info($data) {
        // vars
        $user_id = isset($data['user_id']) && is_numeric($data['user_id']) ? $data['user_id'] : 0;
        $phone = isset($data['phone']) ? preg_replace('~[^\d]+~', '', $data['phone']) : 0;
        // where
        if ($user_id) $where = "user_id='".$user_id."'";
        else if ($phone) $where = "phone='".$phone."'";
        else return [];
        // info
        $q = DB::query("SELECT user_id, first_name, last_name, middle_name, email, gender_id, count_notifications FROM users WHERE ".$where." LIMIT 1;") or die (DB::error());
        if ($row = DB::fetch_row($q)) {
            return [
                'id' => (int) $row['user_id'],
                'first_name' => $row['first_name'],
                'last_name' => $row['last_name'],
                'middle_name' => $row['middle_name'],
                'gender_id' => (int) $row['gender_id'],
                'email' => $row['email'],
                'phone' => (int) $row['phone'],
                'phone_str' => phone_formatting($row['phone']),
                'count_notifications' => (int) $row['count_notifications']
            ];
        } else {
            return [
                'id' => 0,
                'first_name' => '',
                'last_name' => '',
                'middle_name' => '',
                'gender_id' => 0,
                'email' => '',
                'phone' => '',
                'phone_str' => '',
                'count_notifications' => 0
            ];
        }
    }

    public static function user_get_or_create($phone) {
        // validate
        $user = User::user_info(['phone' => $phone]);
        $user_id = $user['id'];
        // create
        if (!$user_id) {
            DB::query("INSERT INTO users (status_access, phone, created) VALUES ('3', '".$phone."', '".Session::$ts."');") or die (DB::error());
            $user_id = DB::insert_id();
        }
        // output
        return $user_id;
    }

    // TEST

    public static function owner_info($data = []) {
        if (!$data) {
            return response(error_response(2400, 'Data is empty.'));
        }
        return self::user_info(['user_id'=>$data['user_id']]);
    }

    public static function owner_update($data = []) {
        if (!$data) {
            return response(error_response(2400, 'Data is empty.'));
        }
        if (!$data['first_name'] || !$data['last_name'] || !$data['phone']) {
            return response(error_response(2400, 'All fields are required (first_name, last_name, phone).'));
        }
        $first_name = $data['first_name'];
        $last_name = $data['last_name'];
        $middle_name = $data['middle_name'] ?? '';
        $email = strtolower($data['email']) ?? '';
        $phone = preg_replace('~[^\d]+~', '', $data['phone']);
        if ($phone[0] != '7') {
            return response(error_response(2400, 'Phone must start with 7.'));
        }
        if (strlen($phone) != 11) {
            return response(error_response(2400, 'Phone must be 11 digits long.'));
        }
        $update_text = "first_name='".$first_name."', ";
        $update_text .= "last_name='".$last_name."', ";
        $update_text .= "middle_name='".$middle_name."', ";
        $update_text .= "email='".$email."', ";
        $update_text .= "phone='".$phone."'";
        DB::query("UPDATE users SET ".$update_text." WHERE user_id=1 LIMIT 1;") or die (DB::error());
        return response(error_response(2200, 'Update user is success. Congratulation.'));
    }

}
