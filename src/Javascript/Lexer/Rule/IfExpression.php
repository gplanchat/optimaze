<?php
/**
 * Created by PhpStorm.
 * User: Greg
 * Date: 03/05/14
 * Time: 00:03
 */

namespace Gplanchat\Javascript\Lexer\Rule;

use Gplanchat\Javascript\Lexer\Exception\LexicalError;
use Gplanchat\Lexer\Grammar\RecursiveGrammarInterface;
use Gplanchat\Javascript\Tokenizer\TokenizerInterface;
use Gplanchat\Lexer\Grammar;
use Gplanchat\Javascript\Lexer\Rule;
use Gplanchat\Tokenizer\TokenizerInterface as BaseTokenizerInterface;

/**
 * Class IfExpression
 * @package Gplanchat\Javascript\Lexer\Rule
 *
 * IfExpression:
 *     if Condition Statement
 *     if Condition Statement else Statement
 */
class IfExpression
    implements RuleInterface
{
    use RuleTrait;

    /**
     * @var Rule\RuleInterface
     */
    protected $conditionRule = null;

    /**
     * @var Rule\RuleInterface
     */
    protected $statementRule = null;

    /**
     * @param RecursiveGrammarInterface $parent
     * @param BaseTokenizerInterface $tokenizer
     * @return void
     * @throws LexicalError
     */
    public function __invoke(RecursiveGrammarInterface $parent, BaseTokenizerInterface $tokenizer)
    {
        /** @var Grammar\ConditionChain $conditionChain */
        $conditionChain = $this->grammar->get('ConditionChain');
        $parent->addChild($conditionChain);

        while (true) {
            /** @var Grammar\IfKeyword $ifKeyword */
            $ifKeyword = $this->grammar->get('IfKeyword');
            $conditionChain->addChild($ifKeyword);

            $this->nextToken($tokenizer);
            $rule = $this->getConditionRule();
            yield $rule($ifKeyword, $tokenizer);

            $rule = $this->getStatementRule();
            yield $rule($ifKeyword, $tokenizer);

            $token = $this->currentToken($tokenizer);
            if ($token->getType() !== TokenizerInterface::KEYWORD_ELSE) {
                break;
            }

            $token = $this->nextToken($tokenizer);
            if ($token->getType() !== TokenizerInterface::KEYWORD_IF) {
                /** @var Grammar\ElseKeyword $elseKeyword */
                $elseKeyword = $this->grammar->get('ElseKeyword');
                $conditionChain->addChild($elseKeyword);

                $rule = $this->getStatementRule();
                yield $rule($elseKeyword, $tokenizer);
                break;
            }
        }
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

    /**
     * @return Rule\RuleInterface|Rule\Condition
     */
    public function getStatementRule()
    {
        if ($this->statementRule === null) {
            $this->statementRule = $this->rule->get('Statement');
        }

        return $this->statementRule;
    }
}
