<?php
/**
 * Created by PhpStorm.
 * User: Greg
 * Date: 17/03/14
 * Time: 11:04
 */

namespace Gplanchat\Javascript\Lexer\Grammar;

trait RightAssociativeGrammarTrait
{
    use RecursiveGrammarTrait;

    /**
     * @param GrammarInterface $node
     * @return $this
     */
    public function addChild(GrammarInterface $node)
    {
        $node->setParent($this);

        array_unshift($this->children, $node);

        return $this;
    }
}
