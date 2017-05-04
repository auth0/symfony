<?php
/**
 * Created by PhpStorm.
 * User: german
 * Date: 1/20/15
 * Time: 11:12 PM
 */

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class SecuredController extends Controller
{
    /**
     * @Route("/api/ping", name="pingpage")
     */
    public function indexAction()
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $username = $user->getUsername();
        if ($username === null) {
            $username = 'ANONYMOUS';
        }

        return new JsonResponse(array('status' => "Pong! {$username}"));
    }
    /**
     * @Route("/api/secure/ping", name="unsecurepingpage")
     */
    public function unsecureIndexAction()
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $username = $user->getUsername();

        return new JsonResponse(array('status' => "Pong! {$username}"));
    }

}