<?php
/**
 * Created by PhpStorm.
 * User: a
 * Date: 1/6/17
 * Time: 12:17 PM
 */

namespace App\MyHealthcare\Helpers;

use JeroenNoten\LaravelAdminLte\Menu\Builder;
use JeroenNoten\LaravelAdminLte\Menu\Filters\FilterInterface;
use Zizaco\Entrust\EntrustFacade as Entrust;

class MyMenuFilter implements FilterInterface
{
    public function transform($item, Builder $builder)
    {
        if (isset($item['role']) && !Entrust::hasRole($item['role'])) {
            return false;
        }

//        if (isset($item['permission']) && !Entrust::can($item['permission'])) {
//            return false;
//        }

        if (isset($item['header'])) {
            $item = $item['header'];
        }

        return $item;
    }
}
