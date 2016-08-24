<?php

namespace Ramble\Ciconia;

use Ciconia\Common\Text;
use Ciconia\Extension\ExtensionInterface;

class CDNImageExtension implements ExtensionInterface
{
    /**
     * {@inheritdoc}
     */

    public function register(\Ciconia\Markdown $markdown)
    {
        $markdown->on('inline', [$this, 'cdnImage']);
    }

    /**
     * @param Text $text
     */
    public function cdnImage(Text $text)
    {
        $text->replace('/^{img a:([^{]+) t:([^{]+)}([^{]+){\/img}/', function (Text $w, Text $alt, Text $title, Text $image) {
            $cdn_url = "//d3dcca3zf9ihpu.cloudfront.net/";

            return "![".$alt."](".$cdn_url.$image.' "'.$title.'")';
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'CDNifies the image url so every source can easily be parsed.';
    }
}
