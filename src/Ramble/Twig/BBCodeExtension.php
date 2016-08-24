<?php
/**
 * This extension requires the PHP extension BBCode to be installed,
 * as well as a BBCode_Container created with bbcode_create().
 *
 *
 *
 * See http://php.net/manual/en/book.bbcode.php for more information.
 *
 * @author Peter Clotworthy <clotters@gmail.com>
 */

namespace Ramble\Twig;

class BBCodeExtension extends Twig_Extension
{
    public $bbcode_array;
    public function __construct($bbcode_array)
    {
        $this->bbcode_array = $bbcode_array;
    }
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('bbcode',
                    array($this, 'bbcodeFilter'),
                    // Required, otherwise converted to character codes.
                    // See bbcodeFilter for additional processing
                    array('is_safe' => array('html'),
                )
            ),
        );
    }
    public function bbcodeFilter($stringToParse)
    {
        /**
         *   If a form is publically fillable, who knows that might be going on in there?
         *   Filter any real HTML entities to bog standard plain-text, then render
         *   the real BBCodes as required.
         */
        $stringToParse = strip_tags($stringToParse);
        $stringToParse = html_entity_decode($stringToParse, ENT_QUOTES, 'UTF-8');
        $parsed = bbcode_parse($this->bbcode_array, $stringToParse);

        return $parsed;
    }
    public function getName()
    {
        return "Twig_BBCode_Extension";
    }
}
