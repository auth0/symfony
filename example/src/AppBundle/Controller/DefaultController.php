<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        return $this->render('AppBundle:Default:index.html.twig',array(
          'AUTH0_DOMAIN' => $this->container->getParameter('jwt_auth.domain'),
          'AUTH0_CLIENT_ID' => $this->container->getParameter('jwt_auth.client_id')
        ));
    }
}
