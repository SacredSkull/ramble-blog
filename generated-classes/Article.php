<?php
use Ciconia\Ciconia;
use Ciconia\Extension\Gfm;
use Base\Article as BaseArticle;

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
class Article extends BaseArticle
{
    public function preSave(Propel\Runtime\Connection\ConnectionInterface $con = null)
    {
        $ciconia = new \Ciconia\Ciconia();
        $ciconia->addExtension(new Gfm\FencedCodeBlockExtension());
        $ciconia->addExtension(new Gfm\TaskListExtension());
        $ciconia->addExtension(new Gfm\InlineStyleExtension());
        $ciconia->addExtension(new Gfm\WhiteSpaceExtension());
        $ciconia->addExtension(new Gfm\TableExtension());
        $ciconia->addExtension(new Gfm\UrlAutoLinkExtension());
        $ciconia->addExtension(new CiconiaExtColourTest());

        $rendered = $ciconia->render($this->body);
        $this->setBodyhtml($rendered);

        return true;
    }
}
