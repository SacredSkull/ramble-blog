<?php
/**
 * Created by PhpStorm.
 * User: sacredskull
 * Date: 25/08/16
 * Time: 02:48
 */

namespace Ramble\Controllers;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Ramble\Models\ArticleQuery;
use Ramble\Models\CategoryQuery;

class Home extends Controller {
	public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args) {
		$page = $args['page'] ?? 1;
		$maxPerPage = 10;

		// Paginate() is currently not compatible with setQueryKey, and only caches the first
		// count query, which is useless because then it causes Twig to throw an exception
		// because propel threw an exception. It was horrible to diagnose and you'd better take
		// your own word for it!
		//
		// TL;DR - paginate() & setQueryKey() do not play well together currently!
		$posts = ArticleQuery::create()
//			->setQueryKey('homepage')
			->useCategoryQuery()
			->filterByName('Fake', \Propel\Runtime\ActiveQuery\Criteria::NOT_EQUAL)
			->endUse()
			->orderById('DESC')
			->filterByDraft(false)
			->paginate($page, $maxPerPage);

		$maxPages = ceil($posts->getNbResults() / $maxPerPage);
		$pagelist = $posts->getLinks(5);

		if ($page > $maxPages && $maxPages != 0) {
			$this->flash->addMessage('denied', "Fresh out of pages!");
			return $response->withStatus(302)->withHeader('Location', $this->router->pathFor("GET_HOME"));
		}

		return $this->paginatedRender($response, 'home.html.twig', $posts, $page, $pagelist, $maxPages);
	}

	protected function homepageRender(ResponseInterface $res, string $template, array $args = []) : ResponseInterface {
		$categories = CategoryQuery::create()
//			->setQueryKey('get_all_categories')
			->find();

		return $this->render($res, $template, array_merge($args, [
			'categories' => $categories,
		]));
	}

	protected function paginatedRender(ResponseInterface $res, string $template, $posts, int $current_page, $page_links, int $max_pages, array $args = []) : ResponseInterface {
		return $this->homepageRender($res, $template, array_merge($args, [
			'posts' => $posts,
			'current_page' => $current_page,
			'page_list' => $page_links,
			'max_pages' => $max_pages,
		]));
	}
}