<?php


namespace App\Controller;


use Symfony\Component\Routing\Annotation\Route;

class TestController extends BaseController
{
    /**
     * @Route("/", name="homepage")
     */
    public function startPage() {
        return $this->render('base.html.twig');
    }
}