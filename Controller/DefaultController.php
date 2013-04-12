<?php

namespace Btanase\MultiSelectAutocompleteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {

        return $this->render('BtanaseMultiSelectAutocompleteBundle:Default:index.html.twig', array('name' => $name));
    }
}
