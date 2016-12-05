<?php
/**
 * Created by PhpStorm.
 * User: sacredskull
 * Date: 25/08/16
 * Time: 18:35
 */

namespace Ramble\Ciconia;


use Ciconia\Common\Text;
use Ciconia\Extension\ExtensionInterface;

class CodeExtension implements ExtensionInterface {
	/**
	 * {@inheritdoc}
	 */
	public function register(\Ciconia\Markdown $markdown)
	{
		$markdown->on('block', [$this, 'fancyCode']);
	}

	/**
	 * {@inheritdoc}
	 */
	public function fancyCode(Text $text){
		$text->replace("/#!([\w-]+)[\n ]([^#]+)!#/m", function (Text $match, Text $language, Text $code) {
			$code->escapeHtml();
			$linesOfCode = preg_split("/\r\n|\n|\r|<br[ ]*[\/]?>/", $code);
			$lined = '';
			$lineNo = '<span class="line-number-column">';
			for($i = 0; $i < sizeof($linesOfCode); $i++){
				if(!empty($linesOfCode[$i])) {
					$lined .= $linesOfCode[$i] . '<br>';
					$lineNo .= '<span class="line-number">'. ($i + 1) .'<br></span>';
				}
			}
			$lineNo .= "</span>";

			return sprintf('<pre><span class="language"><p>%s</p></span><br>%s<code class="%s">%s</code></pre>', strtoupper($language), $lineNo, $language, $lined);
		});
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName()
	{
		return 'Advanced code block with line numbers';
	}
}