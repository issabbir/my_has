<?php
namespace App\Contracts\Authorization;

use Illuminate\Http\Request;

interface AuthContact
{

    /**
     * Authorization Login process
     *
     * @param $params
     * @return mixed
     */
    public function login($params);

    /**Authorization Login process
     *
     * @return mixed
     */
    public function logout();

    /**
     * Recovering password
     *
     * @return mixed
     */
    public function recoverPassword();

    /**
     * Make active an user
     *
     * @param $userId
     * @return mixed
     */
    public function makeActive($userId);

    /**
     * Make deactivate an user
     *
     * @param $uerId
     * @return mixed
     */
    public function makeDeactivate($uerId);
}