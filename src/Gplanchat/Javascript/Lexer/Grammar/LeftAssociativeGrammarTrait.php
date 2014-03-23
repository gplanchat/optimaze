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

trait LeftAssociativeGrammarTrait
{
    use GrammarTrait;

    /** @var GrammarInterface[] */
    protected $children = [];

    /**
     * @return GrammarInterface[]
     */
    public function getChildren()
    {
        return $this->children;
    }

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
