<?php

namespace App\Admin\Controller;

use App\Base\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
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
     * @Route("/", name="admin_index")
     */
    public function index()
    {
        $html = $this->twig->render('@admin/index.html.twig');

        return new Response($html);
    }

}
