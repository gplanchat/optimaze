<?php
/**
 * Created by PhpStorm.
 * User: Greg
 * Date: 17/03/14
 * Time: 11:04
 */

namespace Gplanchat\Javascript\Lexer\Grammar;

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
     * @param GrammarInterface $node
     * @return $this
     */
    public function removeChild(GrammarInterface $node)
    {
        $key = array_search($node, $this->children);
        unset($this->children[$key]);

        return $this;
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

    /**
     * @return $this
     */
    public function flatten()
    {
        $count = count($children = $this->getChildren());
        if ($count <= 0) {
            $parent = $this->getParent();
            if ($parent !== null) {
                $parent->removeChild($this);
            }
        } else if ($count <= 1) {
            $parent = $this->getParent();
            if ($parent !== null) {
                $parent->removeChild($this);

                foreach ($children as $child) {
                    $parent->addChild($child);
                }
            }
        }

        return $this;
    }
}
