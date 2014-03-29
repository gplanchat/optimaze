<?php
/**
 * Created by PhpStorm.
 * User: Greg
 * Date: 17/03/14
 * Time: 11:04
 */

namespace Gplanchat\Javascript\Lexer\Grammar;

use Gplanchat\Javascript\Lexer\Context;
use Gplanchat\Tokenizer\Token;

trait RightAssociativeGrammarTrait
{
    use RecursiveGrammarTrait;

    /**
     * @param GrammarInterface $node
     * @return $this
     */
    public function addChild(GrammarInterface $node)
    {
        array_unshift($this->children, $node);

        return $this;
    }
}
