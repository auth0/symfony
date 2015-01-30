<?php
/**
 * Created by PhpStorm.
 * User: german
 * Date: 1/29/15
 * Time: 9:23 PM
 */

namespace AppBundle\Security;


class A0AnonymousUser extends A0User {

    public function __construct()
    {
        parent::__construct(null,array('IS_AUTHENTICATED_ANONYMOUSLY'));
    }

    public function getUsername()
    {
        return null;
    }

} 