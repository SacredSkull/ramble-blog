<?php
/**
 * This extension simply needs you to have required Parsedown,
 * probably in Composer, or manually with an autoloader.
 *
 * Any Parsedown extensions need to be defined in this class, which is
 * then passed to Twig.
 *
 * @author Peter Clotworthy <clotters@gmail.com>
 * @tutorial
 */

namespace Ramble\Twig;

class ParsedownExtension extends \Twig_Extension
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
        $parser = new Parsedown();

        return $parser->text($stringToParse);
    }
    public function getName()
    {
        return "Twig_Parsedown_Extension";
    }
}
