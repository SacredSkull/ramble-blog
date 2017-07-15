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
use Ramble\Models\QueryBuilder;
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
     * @var QueryBuilder
     */
	protected $queryBuilder;

	public function __construct(ContainerInterface $ci) {
		$this->ci = $ci;
		$this->queryBuilder = $ci->queryBuilder;
		$this->logger = $ci->logger;
		$this->router = $ci->router;
	}


}