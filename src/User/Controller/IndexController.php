<?php

namespace App\User\Controller;

use App\User\Service\Test;
use App\Base\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/user")
 */
class IndexController extends Controller
{

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /** @var Test */
    private $test;

    /**
     * IndexController constructor.
     *
     * @param \Twig_Environment $twig
     * @param Test              $test
     */
    public function __construct(\Twig_Environment $twig, Test $test)
    {
        $this->twig = $twig;
        $this->test = $test;
    }

    /**
     * @Route("/", name="admin_user_index")
     */
    public function index()
    {
        $html = $this->twig->render('@user/index.html.twig', [
            'text' => $this->test->test(),
        ]);

        return new Response($html);
    }

}
