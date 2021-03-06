<?php

namespace Ramble\Ciconia;

use Ciconia\Common\Text;
use Ciconia\Extension\ExtensionInterface;

class ColourExtension implements ExtensionInterface {
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
