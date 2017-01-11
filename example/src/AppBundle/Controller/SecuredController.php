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
     * @Route("/api/public", name="pingpage")
     */
    public function indexAction()
    {
        return new JsonResponse(array(
          'message' => "Hello from a public endpoint! You don't need to be authenticated to see this."
        ));
    }
    /**
     * @Route("/api/private", name="unsecurepingpage")
     */
    public function unsecureIndexAction()
    {
        return new JsonResponse(array(
          'message' => "Hello from a private endpoint! You need to be authenticated and have a scope of read:messages to see this."
        ));
    }

}