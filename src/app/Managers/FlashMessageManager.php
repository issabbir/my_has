<?php
namespace App\Managers;

/**
 * Class FlashMessageManager
 * @package App\Managers
 */
class FlashMessageManager
{
    public function getMessage($params)
    {
        $flashMessage = [];
        if($params['o_status_code'] == 1) {
            $flashMessage['class'] = 'success';
        } else {
            $flashMessage['class'] = 'error';
        }

        $flashMessage['message'] = $params['o_status_message'];

        return $flashMessage;
    }
}