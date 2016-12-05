<?php
/**
 * Created by PhpStorm.
 * User: sacredskull
 * Date: 25/08/16
 * Time: 02:48
 */

namespace Ramble\Controllers;


use Interop\Container\ContainerInterface;
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

    protected function render(ResponseInterface $res, string $template, array $args = []) : ResponseInterface {
        return parent::render($res, $template, array_merge(array(
            'quote' => $this->ci['ramble']['quote'] ?? "",
            'random_flickr_image' => $this->getRandomImage()
        ), $args));
    }

    private function getRandomImage() : array {
        $flickr_api_key = $this->ci['settings']['flickr']['api_key']; // http://www.flickr.com/services/apps/create/apply/
        $flickr_user_id = $this->ci['settings']['flickr']['user_id']; // http://idgettr.com/
        $flickr_album_id = $this->ci['settings']['flickr']['album_id']; // ID of the photoset


        /** @var Redis $redis */
        $redis = $this->ci->get('memoryDB');
        $flickr_list_of_photos_xml = "";

        if($redis != null && $redis->exists('flickr_album_' . $flickr_album_id)){
            $flickr_list_of_photos_xml = $redis->get('flickr_album_' . $flickr_album_id);
        } else {
            $flickr_list_of_photos_xml = $this->getAlbumInfo($flickr_api_key, $flickr_user_id, $flickr_album_id);
            if($redis != null)
                $redis->set('flickr_album_' . $flickr_album_id, $flickr_list_of_photos_xml, 7200);
        }

        $flickr_photos = simplexml_load_string($flickr_list_of_photos_xml);

        // Get a random image from the list
        $flickr_item = rand(0, count($flickr_photos->photoset->photo) - 1);

        // Get the data for that photo so that we can build the image URL
        $flickr_photo_id = $flickr_photos->photoset->photo[$flickr_item]['id'];
        $flickr_secret = $flickr_photos->photoset->photo[$flickr_item]['secret'];
        $flickr_server = $flickr_photos->photoset->photo[$flickr_item]['server'];
        $flickr_farm = $flickr_photos->photoset->photo[$flickr_item]['farm'];

        // Build URL to the photo (appending _b to the filename gets the large size)
        // See here for more on building URLs: http://www.flickr.com/services/api/misc.urls.html
        $flickr_img_url_large = "https://farm$flickr_farm.static.flickr.com/$flickr_server/" . $flickr_photo_id . "_" . $flickr_secret . "_b.jpg";

        // Get information on the photo, including description
        $flickr_img_info_xml = null;
        if($redis != null && $redis->exists('flickr_photo_' . $flickr_photo_id)){
            $flickr_img_info_xml = $redis->get('flickr_photo_' . $flickr_photo_id);
        } else {
            $flickr_img_info_xml = $this->getPhotoDetails($flickr_api_key, $flickr_photo_id);
            if($redis != null)
                $redis->set('flickr_photo_' . $flickr_photo_id, $flickr_img_info_xml, 14000);
        }

        return ['url' => $flickr_img_url_large, 'info' => simplexml_load_string($flickr_img_info_xml)];
    }

    private function getAlbumInfo(string $api_key, string $user_id, string $album_id) : string {
        // Get a list of all photos in specified photoset and load the result into an array
        $flickr_list_of_photos = "https://api.flickr.com/services/rest/?method=flickr.photosets.getPhotos&api_key=$api_key&photoset_id=$album_id&user_id=$user_id";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $flickr_list_of_photos);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $flickr_list_of_photos_xml = curl_exec($ch);
        curl_close($ch);
        return $flickr_list_of_photos_xml;
    }

    private function getPhotoDetails(string $api_key, string $photo_id) : string {
        $flickr_photo_info_xml_url = "https://api.flickr.com/services/rest/?method=flickr.photos.getInfo&api_key=$api_key&photo_id=$photo_id";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $flickr_photo_info_xml_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $flickr_img_info_xml = curl_exec($ch);
        curl_close($ch);
        return $flickr_img_info_xml;
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