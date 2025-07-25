<?php

class Auth
{
    public static function check($roles)
    {
        $user = Session::get('user');
        if (!$user || !in_array($user['role'], $roles)) {
            header('Location: /login');
            exit;
        }
    }
}
