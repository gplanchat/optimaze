<?php
/**
 * Created by PhpStorm.
 * User: Greg
 * Date: 03/05/14
 * Time: 00:13
 */

namespace Gplanchat\Javascript\Lexer\Rule;

use Gplanchat\Javascript\Lexer\Exception\LexicalError;
use Gplanchat\Lexer\Grammar\RecursiveGrammarInterface;
use Gplanchat\Javascript\Tokenizer\TokenizerInterface;
use Gplanchat\Lexer\Grammar;
use Gplanchat\Javascript\Lexer\Rule;
use Gplanchat\Tokenizer\TokenizerInterface as BaseTokenizerInterface;

/**
 * Class WhileExpression
 * @package Gplanchat\Javascript\Lexer\Rule
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
     * @return void
     * @throws LexicalError
     */
    public function __invoke(RecursiveGrammarInterface $parent, BaseTokenizerInterface $tokenizer)
    {
        /** @var Grammar\WhileKeyword $whileKeyword */
        $whileKeyword = $this->grammar->get('WhileKeyword');
        $parent->addChild($whileKeyword);

        $this->nextToken($tokenizer);

        $rule = $this->getConditionRule();
        yield $rule($whileKeyword, $tokenizer);
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
