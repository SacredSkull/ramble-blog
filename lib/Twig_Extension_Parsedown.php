<?php
/**
 * This extension simply requires you to have require'd Parsedown,
 * probably in Composer, or manually with an autoloader.
 *
 *
 *
 * @author Peter Clotworthy <clotters@gmail.com>
 * @tutorial
 */

class Twig_Extension_Parsedown extends Twig_Extension
{
    public function getFilters(){
        return array(
            new \Twig_SimpleFilter('markdown',
                    array($this, 'markdownFilter'),
                    // Required, otherwise converted to character codes.
                    // See bbcodeFilter for additional processing
                    array('is_safe' => array('html')
                )
            )
        );
    }
    public function markdownFilter($stringToParse){
        $parser = new Parsedown();
        return $parser->text($stringToParse);
    }
    public function getName(){
        return "Twig_Parsedown_Extension";
    }
}

?>
