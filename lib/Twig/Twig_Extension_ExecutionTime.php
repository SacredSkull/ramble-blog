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

namespace SacredSkull\Blog;

class Twig_Extension_ExecutionTime extends \Twig_Extension
{
    public function getFunctions()
    {
        return array(
            'executeTime' => new \Twig_Function_Method($this, 'getExecTime', array(
                'is_safe' => array('html'),
            )),
        );
    }

    public function getExecTime()
    {
        if (!isset($GLOBALS['execute_time'])) {
            return "";
        }

        $durationMS = (microtime(true) - $GLOBALS['execute_time']) * 1000;

        return "Took <code>".number_format($durationMS, 3, '.', '')."ms</code> to load this page";
    }

    public function getName()
    {
        return "performance_extension";
    }
}
