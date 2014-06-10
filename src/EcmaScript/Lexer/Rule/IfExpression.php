<?php
/**
 * Created by PhpStorm.
 * User: Greg
 * Date: 03/05/14
 * Time: 00:03
 */

namespace Gplanchat\EcmaScript\Lexer\Rule;

use Gplanchat\EcmaScript\Lexer\Exception\LexicalError;
use Gplanchat\Lexer\Grammar\RecursiveGrammarInterface;
use Gplanchat\EcmaScript\Tokenizer\TokenizerInterface;
use Gplanchat\Lexer\Grammar;
use Gplanchat\EcmaScript\Lexer\Rule;
use Gplanchat\Tokenizer\TokenizerInterface as BaseTokenizerInterface;

/**
 * Class IfExpression
 * @package Gplanchat\EcmaScript\Lexer\Rule
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
     * @param int $level
     * @return \Generator|null
     * @throws LexicalError
     */
    public function run(RecursiveGrammarInterface $parent, BaseTokenizerInterface $tokenizer, $level = 0)
    {
        /** @var Grammar\ConditionChain $conditionChain */
        $conditionChain = $this->grammar->get('ConditionChain');
        $parent->addChild($conditionChain);

        while (true) {
            /** @var Grammar\IfKeyword $ifKeyword */
            $ifKeyword = $this->grammar->get('IfKeyword');
            $conditionChain->addChild($ifKeyword);

            $this->nextToken($tokenizer);
            yield $this->getConditionRule()->run($ifKeyword, $tokenizer, $level + 1);

            yield $this->getStatementRule()->run($ifKeyword, $tokenizer, $level + 1);

            $token = $this->currentToken($tokenizer);
            if (!$token->is(TokenizerInterface::KEYWORD_ELSE)) {
                break;
            }

            $token = $this->nextToken($tokenizer);
            if (!$token->is(TokenizerInterface::KEYWORD_IF)) {
                /** @var Grammar\ElseKeyword $elseKeyword */
                $elseKeyword = $this->grammar->get('ElseKeyword');
                $conditionChain->addChild($elseKeyword);

                yield $this->getStatementRule()->run($elseKeyword, $tokenizer, $level + 1);
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
