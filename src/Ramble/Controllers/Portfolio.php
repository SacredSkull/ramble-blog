<?php
/**
 * Created by PhpStorm.
 * User: sacredskull
 * Date: 26/08/16
 * Time: 21:08
 */

namespace Ramble\Controllers;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Portfolio extends HtmlController {
	public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args) {
		return $this->view->render($response, 'portfolio.html.twig', [

		]);
	}
}