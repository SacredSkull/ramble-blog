<?php

define('DEBUG', true);
define('WIREFRAME', false);
define('CERT_AUTH', false);
define ('SITE_ROOT', realpath(dirname(__FILE__)));
define('USING_WINDOWS', (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN'));

if(DEBUG == true)
{
	ini_set('display_errors', 'On');
	error_reporting(E_ALL);
}
else
{
	ini_set('display_errors', 'Off');
	error_reporting(0);
}

// Composer
require './vendor/autoload.php';
// Propel auto-conf
require './generated-conf/config.php';
// BBCode Twig Extension
//require './lib/Twig_Extension_BBCode.php';
// Markdown Twig Extension
require './lib/Twig_Extension_Parsedown.php';
// Defined BBCodes
require './lib/bbcodes.php';

use Aws\S3\S3Client;

//session_start();

$defaultTheme = new ThemeQuery();
if(!$defaultTheme->findPK(1)){
	$theme = new Theme();
	$theme->setName('Stuff');
	$theme->setRoot('/');
	$theme->save();
}

$app = new \Slim\Slim(array(
	'view' => new \Slim\Views\Twig(),
	'templates.path' => './templates'
));

$view = $app->view();

$view->parserOptions = array(
	'cache' => dirname(__FILE__) . '/cache',
	'debug' => DEBUG,
);

$view->parserExtensions = array(
	new \Slim\Views\TwigExtension(),
	new Twig_Extension_Parsedown(),
	new Twig_Extensions_Extension_Text(),
);

$sayings = explode("\n", file_get_contents('include/etc/skull-phrases.txt'));
$random = rand(0,sizeof($sayings)-1);

$quote = $sayings[$random];

$jsonThemes = array();
$themes = ThemeQuery::create()->setQueryKey('get all themes')->find();
foreach ($themes as $theme) {
	$jsonThemes[$theme->getId()] = $theme->getName();
}
$jsonThemes = json_encode($jsonThemes);

$app->get('/test/', function() use ($app, $quote, $defaultTheme){
	/*
	$generator = Faker\Factory::create('en_UK');


	for ($i=1; $i < 60; $i++) {
		$theme = new Theme();
		$theme->setName($generator->company);
		$theme->setRoot('/');
		$theme->setColour($generator->hexcolor);
		$theme->save();

		$post = new Article();
		$post->setTitle($generator->realText(25));
		$post->setBody($generator->realText(700));
		$preparedFromWebFromArray = array('New Post', '#Excited');
		$tagArray = implode(',', $preparedFromWebFromArray);
		$post->setTags($tagArray);
		$post->setTheme($theme);
		$post->save();
	}

	$post = ArticleQuery::create()
		->orderById()
		->findOne();
	/*
	$post = new Article();
	$post->setTitle('');
	$post->setBody('');
	$preparedFromWebFromArray = array('New Post', '#Excited');
	$tagArray = implode(',', $preparedFromWebFromArray);
	$post->setTags($tagArray);
	$post->setTheme($defaultTheme->findPK(1));
	$post->save();
	*/
	$app->render('test.php', array(
		'admin' => isAdmin(),
		'debug' => DEBUG,
		'wireframe' => WIREFRAME,
		'skull_greeting' => $quote,
		//'posts' => $posts,
		//'json_theme' => $jsonThemes
	));
});

$app->get('/', function () use ($app, $quote, $defaultTheme, $jsonThemes) {
	$page = 1;
	$posts = ArticleQuery::create()
		->setQueryKey('get paginated descending by ID articles')
		->orderById('DESC')
		->paginate($page, $maxPerPage = 10);

	$pagelist = $posts->getLinks(5);
	$maxPages = round($posts->getNbResults() / $maxPerPage);

	$app->render('home.php', array(
		'admin' => isAdmin(),
		'debug' => DEBUG,
		'wireframe' => WIREFRAME,
		'skull_greeting' => $quote,
		'posts' => $posts,
		'current_page' => $page,
		'page_list' => $pagelist,
		'max_pages' => $maxPages,
		'json_theme' => $jsonThemes,
	));
});

$app->get('/:page', function ($page) use ($app, $quote, $defaultTheme, $jsonThemes) {

	$maxPerPage = 10;

	$posts = ArticleQuery::create()
		->setQueryKey('get paginated descending by ID articles')
		->orderById('DESC')
		->paginate($page, $maxPerPage);

	$maxPages = ceil($posts->getNbResults() / $maxPerPage);
	$pagelist = $posts->getLinks(5);

	if($page > $maxPages){
		$app->flash('denied', "Don't have that many pages!");
		$app->redirect('/1');
	}

	$app->render('home.php', array(
		'admin' => isAdmin(),
		'debug' => DEBUG,
		'wireframe' => WIREFRAME,
		'skull_greeting' => $quote,
		'posts' => $posts,
		'current_page' => $page,
		'page_list' => $pagelist,
		'max_pages' => $maxPages,
		'json_theme' => $jsonThemes,
	));
})->conditions(array('page' => '\d{1,4}'));

$app->get('/post/:idArticle', function($idArticle) use ($app, $quote){
	$post = ArticleQuery::create()->findPK($idArticle);
	$app->render('post.php', array(
		'admin' => isAdmin(),
		'debug' => DEBUG,
		'wireframe' => WIREFRAME,
		'skull_greeting' => $quote,
		'post' => $post,
	));
})->conditions(array('idArticle' => '\d{1,10}'));

$app->get('/post/:slugArticle', function($slugArticle) use ($app, $quote) {
	$post = ArticleQuery::create()->findOneBySlug($slugArticle);
	$app->render('post.php', array(
		'admin' => isAdmin(),
		'debug' => DEBUG,
		'wireframe' => WIREFRAME,
		'skull_greeting' => $quote,
		'post' => $post,
	));
});

$app->get('/category/:slugTheme', function($slugTheme) use ($app, $quote){
	$theme = ThemeQuery::create()->findOneBySlug($slugTheme);
	$themedPosts = ArticleQuery::create()->setQueryKey('find posts with particular id')->filterByTheme($theme);
	echo "Hi!";
});

// Either use cheap network access control list or expensive
// but quality client certificate authorisation
// Defined via the CERT_AUTH constant.
// Unfortunate that Cloudflare doesn't support two-way AUTH :(
function isAdmin(){
	if(CERT_AUTH){
		if($_SERVER['HTTPS'] == "on" && $_SERVER['VERIFIED'] == "SUCCESS"){
			return true;
		}
		return false;
	} else{
		// use a cookie for auth?
		$allowedips = array('127.0.0.1', '192.168.1.102');
		$ip = $_SERVER['REMOTE_ADDR'];
		if(in_array($ip, $allowedips)){
			return true;
		}
		return false;
	}
}

// Admin page
$app->get('/admin', function() use ($app, $defaultTheme){

	if(!isAdmin()){
		$req = $app->request;
		$app->flash('denied', "Sorry, you aren't allowed in ". $req->getResourceUri() . "!");
		$app->redirect('/');
	}

    $post = new Article();
    $post->setTitle('');
    $post->setBody('');
    $preparedFromWebFromArray = array('New Post', '#Excited');
    $tagArray = implode(',', $preparedFromWebFromArray);
    $post->setTags($tagArray);
    $post->setTheme($defaultTheme->findPK(1));

    $post->save();

    $app->flash('mode', 'new');
    $app->redirect('/admin/' . $post->getId());

	$quote = "  CREATION MODE!  ";
	$app->render('create.php', array(
		'debug' => DEBUG,
		'wireframe' => WIREFRAME,
		'skull_greeting' => $quote,
		'mode' => 'new',
	));
});
// Create base, redirect form for create/admin
/*
$app->post('/admin', function() use ($app, $defaultTheme){
	$allPostVars = $app->request->post();
	$post = new Article();
	$post->setTitle($allPostVars['title']);
	$post->setBody($allPostVars['body']);
	$preparedFromWebFromArray = array('New Post', '#Excited');
	$tagArray = implode(',', $preparedFromWebFromArray);
	$post->setTags($tagArray);
	$post->setTheme($defaultTheme->findPK(1));
	$post->save();
	echo $post->getId();
});
*/

// Admin page, edit an existing post.
$app->get('/admin/:id', function($id) use ($app){
	// Add an update

	if(!isAdmin()){
		$req = $app->request;
		$app->flash('denied', "Sorry, you aren't allowed in ". $req->getResourceUri() . "!");
		$app->redirect('/');
	}

	$quote = "  EDITING #". $id . "!  ";

	$post = ArticleQuery::create()->findPK($id);

	$app->render('create.php', array(
		'debug' => DEBUG,
		'wireframe' => WIREFRAME,
		'skull_greeting' => $quote,
		'mode' => 'edit',
		'post' => $post,
	));
})->conditions(array('id' => '\d{1,10}'));


// Update post by ID
$app->post('/admin/:id', function($id) use ($app){
    $post = ArticleQuery::create()->findOneById($id);
    $allPostVars = $app->request->post();
    $post->setTitle($allPostVars['title']);
    $post->setBody($allPostVars['body']);
    $post->save();
    echo $post->getId();
})->conditions(array('id' => '\d{1,10}'));

$app->get('/admin/:slugArticle', function($slugArticle) use ($app) {
	$quote = "  EDITING SLUG ". $slugArticle . "!  ";
	$post = ArticleQuery::create()->findOneBySlug($slugArticle);
	$app->render('create.php', array(
		'debug' => DEBUG,
		'wireframe' => WIREFRAME,
		'skull_greeting' => $quote,
		'mode' => 'edit',
	));
});

function jsFriendly($string){
	return htmlspecialchars($string, ENT_QUOTES);
}

$app->group('/api', function() use ($app){
	$app->get('/post', function() use($app){
		$app->redirect('/api/posts/', 301);
	});
	$app->get('/posts/', function(){
		$allPosts = ArticleQuery::create()->find();
		$posts[] = null;
		foreach($allPosts as $post){
			$title_js_ready = jsFriendly( $post->getTitle() );
			$theme_js_ready = jsFriendly( $post->getTheme()->getName() );
			$posts[$post->getId()] = array('title' => $title_js_ready, 'theme' =>  $theme_js_ready, 'id' => $post->getId() );
		}
		unset($posts[0]);
		echo json_encode((object)$posts);
    });
    $app->get('/post/:id', function($id){
        // Output JSON snippet of specific post Id
    })->conditions(array('id' => '\d{1,10}'));

    $app->get('/post/:slugArticle', function($slugArticle){
        // Output JSON snippet of specific post slug
    });
});

$app->get('/upload/:post', function($post) use ($app){
	isAdmin() ? $app->redirect('/admin/' . $post) : $app->redirect('/');
});

$app->post('/upload/:post', function($post) use ($app){
    if(isAdmin()){

		$local_path = SITE_ROOT . "\\images\\uploads\\" . $post . "\\" . str_replace(' ', '_', $_FILES['file']['name']);
		$remote_path = "images/" . $post . "/" . str_replace(' ', '_', $_FILES['file']['name']);

		// Trivia: @ operator suppresses error messages!
		@mkdir(SITE_ROOT . "\\images\\uploads\\" . $post);

		$temp = $_FILES['file']['tmp_name'];
		move_uploaded_file( $temp, $local_path);

		if(USING_WINDOWS){
			// Use ugly, inefficient exec() work-around for PATH issues & general DLL nonsense on Windows.
			exec("E:\\WPNXM\\bin\\imagick\\convert.exe " . $local_path . " -resize 600x600 " . $local_path);
		} else {
			// Nice, it's not a Windows environment! We can actually use OOP
			// programming without having to exec() something!
			die("This section hasn't been set up for proper linux operation. Please complete the
				relevant ImageMagick / GraphicsMagick code");
		}

		$client = S3Client::factory(array(
			'key' => 'AKIAJ6LFRWILE2M7YCGA',
			'secret' => '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',
		));

		$filepath = './include/img/skull.png';
		$result = "";
		try{
			$result = $client->putObject(array(
				'Bucket' => 'sacredskull-blog',
				'Key'          => $remote_path,
				'SourceFile'   => $local_path,
				'ContentType'  => $_FILES['file']['type'],
				'ACL'          => 'public-read',
				'StorageClass' => 'STANDARD',
				'Metadata'     => array(
					'category' => 'image',
				)
			));
			echo '{"url": "https://d3dcca3zf9ihpu.cloudfront.net/' . $remote_path . '"}';
		} catch(S3Exception $e){
			echo '{"error": "'.$e->getMessage().'"}';
		}
    } else{
        $app->flash('denied', "Sorry, you aren't allowed in ". $req->getResourceUri() . "!");
        $app->redirect('/');
    }
})->conditions(array('post' => '\d{1,10}'));

$app->run();

?>
