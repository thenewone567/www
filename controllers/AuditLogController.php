<?php

class AuditLogController
{
    public function index()
    {
        require_once ROOT_PATH . 'helpers/Auth.php';
        Auth::check(['Admin']);
        require_once ROOT_PATH . 'views/audit-log.php';
    }
}
