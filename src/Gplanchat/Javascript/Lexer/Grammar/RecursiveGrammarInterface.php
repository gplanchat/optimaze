<?php
/**
 * Created by PhpStorm.
 * User: Greg
 * Date: 17/03/14
 * Time: 11:04
 */

namespace Gplanchat\Javascript\Lexer\Grammar;

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

    /**
     * @param GrammarInterface $node
     * @return $this
     */
    public function removeChild(GrammarInterface $node);

    /**
     * @return $this
     */
    public function flatten();
}
