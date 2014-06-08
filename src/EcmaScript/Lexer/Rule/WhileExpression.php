<?php
/**
 * Created by PhpStorm.
 * User: Greg
 * Date: 03/05/14
 * Time: 00:13
 */

namespace Gplanchat\EcmaScript\Lexer\Rule;

use Gplanchat\EcmaScript\Lexer\Exception\LexicalError;
use Gplanchat\Lexer\Grammar\RecursiveGrammarInterface;
use Gplanchat\EcmaScript\Tokenizer\TokenizerInterface;
use Gplanchat\Lexer\Grammar;
use Gplanchat\EcmaScript\Lexer\Rule;
use Gplanchat\Tokenizer\TokenizerInterface as BaseTokenizerInterface;

/**
 * Class WhileExpression
 * @package Gplanchat\EcmaScript\Lexer\Rule
 *
 * WhileExpression:
 *     while Condition Statement
 */
class WhileExpression
    implements RuleInterface
{
    use RuleTrait;

    /**
     * @var Rule\RuleInterface
     */
    protected $conditionRule = null;

    /**
     * @param RecursiveGrammarInterface $parent
     * @param BaseTokenizerInterface $tokenizer
     * @param int $level
     * @return \Generator|null
     * @throws LexicalError
     */
    public function run(RecursiveGrammarInterface $parent, BaseTokenizerInterface $tokenizer, $level = 0)
    {
        /** @var Grammar\WhileKeyword $whileKeyword */
        $whileKeyword = $this->grammar->get('WhileKeyword');
        $parent->addChild($whileKeyword);

        $this->nextToken($tokenizer);

        yield $this->getConditionRule()->run($whileKeyword, $tokenizer, $level + 1);
    }

    /**
     * @return Rule\RuleInterface|Rule\Condition
     */
    public function getConditionRule()
    {
        if ($this->conditionRule === null) {
            $this->conditionRule = $this->rule->get('Condition');
        }

        return $this->conditionRule;
    }
}
