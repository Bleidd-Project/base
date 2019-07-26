<?php

namespace App\Page\Controller;

use App\Base\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/")
 */
class IndexController extends Controller
{

    /**
     * @var \Twig_Environment
     */
    private $twig;

    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @Route("/", name="page_index")
     */
    public function index()
    {
        $html = $this->twig->render('@page/index.html.twig');

        return new Response($html);
    }

}
