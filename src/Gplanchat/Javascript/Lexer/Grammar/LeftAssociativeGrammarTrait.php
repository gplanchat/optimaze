<?php
/**
 * Created by PhpStorm.
 * User: Greg
 * Date: 17/03/14
 * Time: 11:04
 */

namespace Gplanchat\Javascript\Lexer\Grammar;

use Gplanchat\Tokenizer\Token;

trait LeftAssociativeGrammarTrait
{
    use RecursiveGrammarTrait;

    /**
     * @param GrammarInterface $node
     * @return $this
     */
    public function addChild(GrammarInterface $node)
    {
        $this->children[] = $node;

        return $this;
    }
}
