<?php
/**
 * Created by PhpStorm.
 * User: Greg
 * Date: 17/03/14
 * Time: 11:04
 */

namespace Gplanchat\Javascript\Lexer\Grammar;

use Gplanchat\Tokenizer\Token;

interface RecursiveGrammarInterface
    extends GrammarInterface
{
    /**
     * @return GrammarInterface[]
     */
    public function getChildren();

    /**
     * @param GrammarInterface $node
     * @return $this
     */
    public function addChild(GrammarInterface $node);
}
