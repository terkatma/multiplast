<?php
/**
 * Created by PhpStorm.
 * User: tomas
 * Date: 17.7.15
 * Time: 9:59
 */

namespace App\Components;

/**
 * Interface ISignInComponentFactory
 * @package App\Components
 */
interface ISignInComponentFactory
{

    /** @return SignInComponent */
    function create();
}