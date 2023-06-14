<?php

namespace Funcoes\Layout;

class Toasts
{
    public static function json()
    {
        global $session;
        $toasts = [];
        $flashMessages = $session->get('flash', []);
        foreach ($flashMessages as $type => $msgs) {
            if ($type == 'previous') {
                continue;
            }
            foreach ($msgs as $msg) {
                $toasts[] = [
                    'icon' => $type,
                    'title' => $msg,
                ];
            }
        }
        return json_encode($toasts);
    }
}
