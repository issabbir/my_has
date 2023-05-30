<?php
namespace App\Enums\Auth;

Abstract class UserParams
{
    final static function params()
    {
        return [
            'p_user_name' => '',
            'p_user_pass' => '',
            'p_user_ip_address' => '',
            'o_user_full_name' => sprintf('%20f', ''),
            'o_user_id' => sprintf('%20f', ''),
            'o_status_code' => sprintf('%20f', ''),
            'o_need_pass_reset' => sprintf('%20f', ''),
            'o_status_message' => sprintf('%4000s', ''),
        ];
    }

    public static function bindParams($params)
    {
        $a1 = self::params();
        $mappedParams =array_merge($a1, $params);

        if ($mappedParams['_token'])
            unset($mappedParams['_token']);

        return $mappedParams;
    }
}