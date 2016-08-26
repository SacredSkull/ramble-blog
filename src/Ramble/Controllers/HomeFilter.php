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
use Ramble\Models\TagQuery;

class HomeFilter extends Home {


	public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args) {

	}

	public function filterByCategory(ServerRequestInterface $request, ResponseInterface $response, array $args){
		$categorySlug = null;

		if(!empty($args['category']))
			$categorySlug = $args['category'];
		else {
			return $response->withStatus(302)->withHeader("Location", $this->router->pathFor("GET_HOME"));
		}


		$page = $args['page'] ?? 1;

		$specificCategory = CategoryQuery::create()
			->findOneBySlug($categorySlug);

		$maxPerPage = 10;

		if ($specificCategory == null) {
			$this->flash->addMessage('denied', "$categorySlug doesn't exist!");
			return $response->withStatus(302)->withHeader("Location", $this->router->pathFor("GET_HOME"));
		}

		$posts = ArticleQuery::create()
			//->setQueryKey('homepage')
			->orderById('DESC')
			->filterByCategory($specificCategory)
			->filterByDraft(false)
			->paginate($page, $maxPerPage);

		if ($posts->count() == 0) {
			$this->flash->addMessage('denied', "I haven't posted anything in $categorySlug");
			return $response->withStatus(302)->withHeader("Location", $this->router->pathFor("GET_HOME"));
		}

		$maxPages = ceil($posts->getNbResults() / $maxPerPage);

		if ($page > $maxPages) {
			$this->flash->addMessage('denied', "Fresh out of pages! Taking you to page #$maxPages");
			return $response->withStatus(302)->withHeader('Location', $this->router->pathFor('GET_HOME_FILTER_CATEGORY', [
				'category' => $categorySlug,
				'page' => $maxPages
			]));
		}

		$pagelist = $posts->getLinks(5);

		return $this->paginatedRender($response, 'home.html.twig', $posts, $page, $pagelist, $maxPages, array(
			'pagination_url' => $this->router->pathFor('GET_HOME_FILTER_CATEGORY', [
				'category' => $categorySlug
			]),
		));
	}

	public function filterByPost(ServerRequestInterface $request, ResponseInterface $response, array $args){
		$slug = $args['year']."-".$args['month']."-".$args['day']."_".$args['slugArticle'];
		$post = ArticleQuery::create()->findOneBySlug($slug);

		if ($post == null) {
			$this->flash->addMessage('error', 'No post found with that information, sorry!');
			return $response->withStatus(302)->withHeader('Location', $this->router->pathFor('GET_HOME'));
		}

		return $this->homepageRender($response, 'post.html.twig', [
			'post' => $post
		]);
	}

	public function filterByDate(ServerRequestInterface $request, ResponseInterface $response, array $args){
		$day = $args['day'] ?? false;
		$month = $args['month'] ?? false;
		$year = $args['year'] ?? false;
		$page = $args['page'] ?? 1;

		$badDate = false;

		if ($month === false) {
			// The month has been omitted (so we're fetching posts from year x)
			$firstDate = new DateTime($year."/01"."/01"." 00:00:00");
			$secondDate = new DateTime($year."/12"."/31"." 23:59:59");

			$rawDate = $firstDate->format('Y');
			$rawDate = "in ".$rawDate;
		} elseif ($day === false ) {
			// The day has been omitted (so we're fetching posts from month x)
			if ($month > 12 || $month < 1) {
				$month = 1;
				$badDate = true;
			}

			if($badDate){
				$this->flash->addMessage('denied', 'Do you need me to tell you what year it is?');
				return $response->withStatus(302)->withHeader('Location', $this->router->pathFor('GET_HOME_FILTER_DATE', [
					'year' => $year,
					'month' => $month,
					'day' => $day,
					'page' => $page == 1 ? null : $page
				]));
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
				$badDate = true;
			}

			// Now it even works for Feb 29th!
			if ($day > date('t', mktime(0, 0, 0, $month, 1, $year)) || $day < 1) {
				$day = 1;
				$badDate = true;
			}

			if($badDate){
				$this->flash->addMessage('denied', 'Do you need me to tell you what year it is?');
				return $response->withStatus(302)->withHeader('Location', $this->router->pathFor('GET_HOME_FILTER_DATE', [
					'year' => $year,
					'month' => $month,
					'day' => $day,
					'page' => $page
				]));
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

		if ($posts->count() == 0) {
			$this->flash->addMessage('denied', "I haven't posted anything $rawDate.");
			return $response->withStatus(302)->withHeader('Location', $this->router->pathFor('GET_HOME'));
		}

		if ($page > $maxPages) {
			$this->flash->addMessage('denied', "Fresh out of pages! Taking you to page #$maxPages");
			return $response->withStatus(302)->withHeader('Location', $this->router->pathFor('GET_HOME_FILTER_DATE', [
				'year' => $year,
				'month' => $month,
				'day' => $day,
				'page' => $maxPages
			]));
		}

		return $this->paginatedRender($response, 'home.html.twig', $posts, $page, $pagelist, $maxPages, array(
			'pagination_url' => $this->router->pathFor('GET_HOME_FILTER_DATE', [
				'year' => $year,
				'month' => $month,
				'day' => $day
			]),
		));
	}

	public function filterByTag(ServerRequestInterface $request, ResponseInterface $response, array $args){
		$tag = $args['tag'];
		$page = $args['page'] ?? 1;

		$maxPerPage = 10;
		$selectedTag = TagQuery::create()->findOneByName($tag);

		if ($selectedTag != null) {
			$posts = ArticleQuery::create()
				->filterByTag($selectedTag)
				->filterByDraft(false)
				->paginate($page, $maxPerPage);

			if($posts->count() == 0){
				$this->flash->addMessage('denied', "No posts tagged with $tag");
				return $response->withStatus(302)->withHeader('Location', $this->router->pathFor('GET_HOME'));
			}

			$maxPages = ceil($posts->getNbResults() / $maxPerPage);
			$pagelist = $posts->getLinks(5);

			if ($page > $maxPages) {
				$this->flash->addMessage('denied', "Fresh out of pages! Taking you to page #$maxPages");
				return $response->withStatus(302)->withHeader('Location', $this->router->pathFor('GET_HOME_FILTER_TAG', [
					'tag' => $tag,
					'page' => $maxPages
				]));
			}

			$this->paginatedRender($response, 'home.html.twig', $posts, $page, $pagelist, $maxPages);
		} else {
			$this->flash->addMessage('denied', "Tag $tag doesn't currently exist");
			return $response->withStatus(302)->withHeader('Location', $this->router->pathFor('GET_HOME'));
		}
	}
}