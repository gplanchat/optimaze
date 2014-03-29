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

trait RecursiveGrammarTrait
{
    use GrammarTrait {
        GrammarTrait::dump as protected grammarTraitDump;
    }

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
     * @param int $level
     * @return string
     */
    public function dump($level = 0)
    {
        $output = $this->grammarTraitDump($level);

        foreach ($this->getChildren() as $child) {
            $output .= $child->dump($level + 1);
        }

        return $output;
    }
}
