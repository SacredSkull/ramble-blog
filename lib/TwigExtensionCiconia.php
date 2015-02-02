<?php
/**
 * This extension simply needs you to have required Ciconia,
 * probably in Composer, or manually with an autoloader.
 *
 * Any Ciconia extensions need to be required and added in this class.
 *
 * @author Peter Clotworthy <clotters@gmail.com>
 * @tutorial
 */

use Ciconia\Ciconia;
use Ciconia\Extension\Gfm;

require 'CiconiaExtColourTest.php';

class TwigExtensionCiconia extends Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter(
                'markdown',
                array($this, 'markdownFilter'),
                // Required, otherwise converted to character codes.
                // See bbcodeFilter for additional processing
                array('is_safe' => array('html'),
                )
            ),
        );
    }
    public function markdownFilter($stringToParse)
    {
        $ciconia = new \Ciconia\Ciconia();
        $ciconia->addExtension(new Gfm\FencedCodeBlockExtension());
        $ciconia->addExtension(new Gfm\TaskListExtension());
        $ciconia->addExtension(new Gfm\InlineStyleExtension());
        $ciconia->addExtension(new Gfm\WhiteSpaceExtension());
        $ciconia->addExtension(new Gfm\TableExtension());
        $ciconia->addExtension(new Gfm\UrlAutoLinkExtension());
        $ciconia->addExtension(new CiconiaExtColourTest());

        return $ciconia->render($stringToParse);
    }
    public function getName()
    {
        return "Twig extension for Ciconia.";
    }
}
