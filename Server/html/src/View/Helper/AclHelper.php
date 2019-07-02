<?php
/**
 * Created by PhpStorm.
 * User: mcollins
 * Date: 1/2/18
 * Time: 4:02 PM
 */

namespace App\View\Helper;

use Cake\View\Helper;


class AclHelper extends Helper
{

    public function hasAccess($acls, $controller, $action) {
        foreach ($acls as $acl) {
            if ($acl->controller == $controller &&
                (
                    $acl->action == $action
                    ||
                    $acl->action == '*'
                )
            ) {
                return true;
            }
        }

        return false;
    }
}