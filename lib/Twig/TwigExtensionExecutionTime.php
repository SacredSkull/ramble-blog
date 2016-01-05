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

class TwigExtensionExecutionTime extends \Twig_Extension
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

        $app = "</code>s.";

        $secs = $durationMS / 1000;
        if($secs < 0.01){
            $pre = "a scorchingly fast <code>";
        } elseif ($secs < 0.5) {
            $pre = " <code>";
        } elseif ($secs < 1) {
            $pre = "a decent <code>";
        } elseif ($secs > 1 && $secs < 1.5) {
            $pre = "a lengthy <code>";
        } else {
            $pre = "a very slow <code>";
        }

        return "Generated in ".$pre.number_format($secs, 3, '.', '').$app;
    }

    public function getName()
    {
        return "performance_extension";
    }
}
