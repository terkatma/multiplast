<?php
/**
 * Created by PhpStorm.
 * User: tomas
 * Date: 17.7.15
 * Time: 9:59
 */

namespace App\Components;

/**
 * Interface IListImportComponentFactory
 * @package App\Components
 */
interface IListImportComponentFactory
{

    /** @return ListImportComponent */
    function create();
}