<?php

namespace App\User\Controller;

use App\User\Service\Test;
use App\User\Service\UserService;
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

    /** @var UserService */
    private $service;

    /**
     * IndexController constructor.
     *
     * @param \Twig_Environment $twig
     * @param Test              $test
     * @param UserService       $service
     */
    public function __construct(\Twig_Environment $twig, UserService $service)
    {
        $this->twig = $twig;
        $this->service = $service;
    }

    /**
     * @Route("/", name="admin_user_index")
     */
    public function index()
    {
        $html = $this->twig->render('@user/index.html.twig', [
            'text' => $this->service->test(),
        ]);

        return new Response($html);
    }

}
