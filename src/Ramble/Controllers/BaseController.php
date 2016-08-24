<?php
/**
 * Created by PhpStorm.
 * User: sacredskull
 * Date: 21/08/16
 * Time: 02:46
 */

namespace Ramble\Controllers;


use DateTime;
use Ramble\Models\Article;
use Ramble\Models\ArticleQuery;
use Ramble\Models\CategoryQuery;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class BaseController extends Controller {
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
		if ($page == 1 && $request->getUri()->getPath() != "/") {
			return $response->withStatus(302)->withHeader('Location', '/');
		}

		if ($page > $maxPages && $maxPages != 0) {
			sd([$page, $maxPages]);
			$this->flash->addMessage('denied', "I've failed you senpai.. I haven't got that many post pages!");
			return $response->withStatus(302)->withHeader('Location', '/');
		}

		return $this->paginatedRender($response, 'home.html.twig', $posts, $page, $pagelist, $maxPages);
	}

	public function filterByCategory(ServerRequestInterface $request, ResponseInterface $response, array $args){
		if($args['category'] == null || strlen($args['category']) < 1)
			$categorySlug = false;
		else
			$categorySlug = $args['category'];
		$page = $args['page'] ?? 1;

		if($categorySlug) {
			$specificCategory = CategoryQuery::create()
				->findOneBySlug($categorySlug);

			$maxPerPage = 10;

			if ($specificCategory == null) {
				$this->flash->addMessage('denied', "$categorySlug hasn't been created yet");
				return $response->withStatus(302)->withHeader("Location", $this->router->pathFor("GET_HOME"));
			}

			$posts = ArticleQuery::create()
				//->setQueryKey('homepage')
				->orderById('DESC')
				->filterByCategory($specificCategory)
				->filterByDraft(false)
				->paginate($page, $maxPerPage);

			$maxPages = ceil($posts->getNbResults() / $maxPerPage);
			if (!$maxPages > 0) {
				$this->flash->addMessage('denied', "I haven't posted anything in $categorySlug");
				return $response->withStatus(302)->withHeader("Location", $this->router->pathFor("GET_HOME"));
			}

			$pagelist = $posts->getLinks(5);

			$this->paginatedRender($response, 'home.html.twig', $posts, $page, $pagelist, $maxPages, array(
				'pagination_url' => $this->router->pathFor('GET_HOME_FILTER_CATEGORY', [
					'category' => ''
				]),
			));
		}

		return $response->withStatus(302)->withHeader("Location", $this->router->pathFor("GET_HOME"));
	}

	public function filterByPost(ServerRequestInterface $request, ResponseInterface $response, array $args){
		$slug = $args['year']."-".$args['month']."-".$args['day']."_".$args['slugArticle'];
		$post = ArticleQuery::create()->findOneBySlug($slug);

		if ($post == null) {
			// 404 page...
			$this->flash->addMessage('error', 'No post found with that information, sorry!');
			return $response->withStatus(302)->withHeader('Location', $this->router->pathFor('GET_HOME'));
		}

		return $this->homepageRender($response, 'post.html.twig', [
			'post' => $post
		]);
	}

	public function filterByDate(ServerRequestInterface $request, ResponseInterface $response, array $args){
		$day = $args['day'] ?? -1;
		$month = $args['month'] ?? -1;
		$year = $args['year'] ?? -1;
		$page = $args['page'] ?? 1;

		if ($month < 0) {
			// The month has been omitted (so we're fetching posts from year x)
			$firstDate = new DateTime($year."/01"."/01"." 00:00:00");
			$secondDate = new DateTime($year."/12"."/31"." 23:59:59");

			$rawDate = $firstDate->format('Y');
			$rawDate = "in ".$rawDate;
		} elseif ($day < 0) {
			// The day has been omitted (so we're fetching posts from month x)
			if ($month > 12 || $month < 1) {
				$month = 1;
			}
			$firstDate = new DateTime($year."/".$month."/01"." 00:00:00");
			$days = $firstDate->format('t');

			$secondDate = new DateTime($year."/".$month."/".$days." 23:59:59");

			$rawDate = $firstDate->format('F, Y');
			$rawDate = "in ".$rawDate;
		} else {
			// An entire year/month/day query has been included
			if ($month > 12 || $month < 1) {
				$month = 1;
			}

			// Now it even works for Feb 29th!
			if ($day > date('t', mktime(0, 0, 0, $month, 1, $year)) || $day < 1) {
				$day = 1;
			}
			$firstDate = new DateTime($year."/".$month."/".$day." 00:00:00");
			$secondDate = new DateTime($year."/".$month."/".$day." 23:59:59");

			$rawDate = $firstDate->format('jS l, F Y');
			$rawDate = "on the ".$rawDate;
		}

		$maxPerPage = 10;

		// See the main home route ("/") for information about why setQueryKey should always be commented out for paginations
		$posts = ArticleQuery::create()
			//->setQueryKey('homepage')
			->orderById('DESC')
			->filterByCreatedAt(array('min' => $firstDate, 'max' => $secondDate))
			->paginate($page, $maxPerPage);

		$maxPages = ceil($posts->getNbResults() / $maxPerPage);
		$pagelist = $posts->getLinks(5);

		if (!$maxPages > 0) {
			$this->flash->addMessage('denied', "I haven't posted anything $rawDate.");
			return $response->withStatus(302)->withHeader('Location', $this->router->pathFor('GET_HOME'));
		}

		if ($page > $maxPages) {
			$this->flash->addMessage('denied', "Fresh out of pages!");
			return $response->withStatus(302)->withHeader('Location', $this->router->pathFor('GET_HOME_FILTER_DATE', [
				'year' => $year,
				'month' => $month,
				'day' => $day
			]));
		}

		return $this->paginatedRender($response, 'home.html.twig', $posts, $page, $pagelist, $maxPages, array(
			'pagination_url' => '/'.$year."/".$month."/".$day."/",
		));
	}
}