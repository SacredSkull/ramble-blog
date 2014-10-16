<?php
$arrayBB = bbcode_create(array(
        // Italics
        'i' =>      array('type' => BBCODE_TYPE_NOARG,
                          'open_tag' => '<i>',
                          'close_tag' => '</i>',
                          'childs' => 'b'
                          ),

        // Bold
        'b'=>       array('type' => BBCODE_TYPE_NOARG,
                          'open_tag' => '<b>',
                          'close_tag' => '</b>',
                          'childs' => 'i'
                          ),

        // URL
        'url'=>     array('type' => BBCODE_TYPE_OPTARG,
                          'open_tag' => '<a href="{PARAM}">',
                          'close_tag' => '</a>',
                          'default_arg' => '{CONTENT}', 'childs' => 'b,i'
                          ),

        // Strikethrough/deleted
        'deleted'=> array('type' => BBCODE_TYPE_NOARG,
                          'open_tag' => '<del>',
                          'close_tag' => '</del>'
                          ),
));
?>
