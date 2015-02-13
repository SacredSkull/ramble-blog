<?php

namespace SacredSkull\Blog;

use Ciconia\Common\Text;
use Ciconia\Extension\ExtensionInterface;

class CiconiaExtColour implements ExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(\Ciconia\Markdown $markdown)
    {
        $markdown->on('inline', [$this, 'colourText']);
    }

    /**
     * @param Text $text
     */
    public function colourText(Text $text)
    {
        // Turn @username into [@username](http://example.com/user/username)
        $text->replace('/^{c:([#\w]\w+)}([^{]+){\/c}/', function (Text $w, Text $colour, Text $string) {
            return sprintf("<span style='color: %s'>%s</span>", $colour, $string);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Colours the text';
    }
}
