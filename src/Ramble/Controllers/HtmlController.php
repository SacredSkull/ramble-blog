<?php
/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 05/07/2017
 * Time: 00:27
 */

namespace Ramble\Controllers;


use Interop\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Views\Twig;

abstract class HtmlController extends Controller {
    /**
     * @var \Slim\Flash\Messages
     */
    protected $flash;
    /**
     * @var Twig
     */
    protected $view;

    public function __construct(ContainerInterface $ci) {
        parent::__construct($ci);
        $this->flash = $ci->flash;
        $this->view = $ci->view;
    }

    protected function render(ResponseInterface $res, string $template, array $args = []) : ResponseInterface {
        //ddd($this->flash->getMessages());
        return $this->view->render($res, $template, array_merge(array(
            'admin' => $this->ci['ramble']['admin'] ?? false,
            'debug' => $this->ci['ramble']['debug'] ?? false,
            'quote' => $this->ci['ramble']['quote'] ?? "",
            'flash' => $this->flash->getMessages(),

        ), $args));
    }
}