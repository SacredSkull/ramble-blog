<?php
/**
 * Created by PhpStorm.
 * User: sacredskull
 * Date: 21/08/16
 * Time: 02:55
 */

namespace Ramble\Controllers;


use Interop\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Views\Twig;

abstract class Controller {
	protected $ci;
	/**
	 * @var \Monolog\Logger
	 */
	protected $logger;
	/**
	 * @var \Slim\Router
	 */
	protected $router;
	/**
	 * @var \Slim\Flash\Messages
	 */
	protected $flash;
	/**
	 * @var Twig
	 */
	protected $view;

	public function __construct(ContainerInterface $ci) {
		$this->ci = $ci;
		$this->logger = $ci->logger;
		$this->flash = $ci->flash;
		$this->router = $ci->router;
		$this->view = $ci->view;
	}

	protected function render(ResponseInterface $res, string $template, array $args = []) : ResponseInterface {
		//ddd($this->flash->getMessages());
		return $this->view->render($res, $template, array_merge(array(
			'admin' => $this->ci['ramble']['admin'] ?? false,
			'debug' => $this->ci['ramble']['debug'] ?? false,
			'quote' => $this->ci['ramble']['quote'] ?? "",
			'flash' => $this->flash->getMessages()
		), $args));
	}
}