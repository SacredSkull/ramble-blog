<?php

namespace Ramble\Models;
use Ciconia\Extension\Gfm;
use DateTime;
use Ramble\Ciconia\CDNImageExtension;
use Ramble\Ciconia\ColourExtension;
use Ramble\Ciconia\CodeExtension;
use Ramble\Models\Base\Article as BaseArticle;

/**
 * Skeleton subclass for representing a row from the 'article' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class Article extends BaseArticle {
    public function createSlug() {
        $createdAt = $this->getCreatedAt();
        if (empty($createdAt)) {
            $createdAt = new DateTime(date('Y-m-d'));
        }

        // create the slug based on the `slug_pattern` and the object properties
        $slug = $this->createRawSlug();
        // truncate the slug to accommodate the size of the slug column
        $slug = $this->limitSlugSize($slug);

        $slug = $createdAt->format('Y-m-d_').$slug;

        // add an incremental index to make sure the slug is unique
        $slug = $this->makeSlugUnique($slug);

        return $slug;
    }
    
    public function preSave(\Propel\Runtime\Connection\ConnectionInterface $con = null) {
        $ciconia = new \Ciconia\Ciconia();
	    $ciconia->addExtension(new ColourExtension());
	    $ciconia->addExtension(new CDNImageExtension());
	    $ciconia->addExtension(new CodeExtension());
	    $ciconia->addExtension(new Gfm\FencedCodeBlockExtension());
	    $ciconia->addExtension(new Gfm\TaskListExtension());
	    $ciconia->addExtension(new Gfm\InlineStyleExtension());
	    $ciconia->addExtension(new Gfm\WhiteSpaceExtension());
	    $ciconia->addExtension(new Gfm\TableExtension());
	    $ciconia->addExtension(new Gfm\UrlAutoLinkExtension());

        $rendered = $ciconia->render($this->body);
        $this->setBodyhtml($rendered);

        return true;
    }
}
