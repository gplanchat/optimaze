<?php
/**
 * Created by PhpStorm.
 * User: Greg
 * Date: 17/03/14
 * Time: 11:04
 */

namespace Gplanchat\Javascript\Lexer\Grammar;

interface GrammarInterface
{
    /**
     * @return int
     */
    public function getType();

    /**
     * @return GrammarInterface|null
     */
    public function getParent();

    /**
     * @param RecursiveGrammarInterface $parent
     * @return $this
     */
    public function setParent(RecursiveGrammarInterface $parent);

    /**
     * @return string
     */
    public function dump();
}
