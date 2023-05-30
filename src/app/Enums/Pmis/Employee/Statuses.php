<?php
/**
 * Created by PhpStorm.
 * User: ashraf
 * Date: 1/2/20
 * Time: 4:58 PM
 */

namespace App\Enums\Pmis\Employee;

/**
 * Employee statuses. We should use it in doing query. IE. ON ROLL query, we should use 'ON_ROLL' enum.
 * USAGE: \App\Enums\Pmis\Employee\Statuses::ON_ROLL.
 *
 * Class Statuses
 * @package App\Enums\Pmis\Employee
 */
abstract class Statuses
{
    public const ON_ROLE = 1;
    public const SUSPENDED = 2;
    public const TERMINATED = 3;
    public const PRL = 4;
    public const RETIRED = 5;
    public const HOLD = 6;
    public const DEAD = 7;
    public const NONE = 8;
    public const RESIGNED = 9;
    public const ABSENT = 10;
    public const LEAVE = 11;
    public const VOLUNTEER_RETIREMENT = 12;
}