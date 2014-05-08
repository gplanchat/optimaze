<?php
/**
 * This file is part of gplanchat/php-javascript-tokenizer
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Grégory Planchat <g.planchat@gmail.com>
 * @licence GNU General Public Licence
 * @package Gplanchat\Javascript\Lexer
 */


namespace Gplanchat\Javascript\Lexer\Rule;

use Gplanchat\Javascript\Lexer\Exception\LexicalError;
use Gplanchat\Lexer\Grammar\RecursiveGrammarInterface;
use Gplanchat\Javascript\Tokenizer\TokenizerInterface;
use Gplanchat\Lexer\Grammar;
use Gplanchat\Javascript\Lexer\Rule;
use Gplanchat\Tokenizer\TokenizerInterface as BaseTokenizerInterface;

/**
 * Class Statement
 * @package Gplanchat\Javascript\Lexer\Rule
 *
 * Statement:
 *     ;
 *     IfExpression
 *     WhileExpression
 *     ForExpression
 *     break ;
 *     continue ;
 *     with ( Expression ) Statement
 *     return Expression ;
 *     { StatementList }
 *     VariableListOrExpression ;
 */
class Statement
    implements RuleInterface
{
    use RuleTrait;

    /**
     * @var Rule\RuleInterface
     */
    protected $expressionRule = null;

    /**
     * @var Rule\RuleInterface
     */
    protected $variableListOrExpressionRule = null;

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
        /** @var Grammar\Statement $node */
        $node = $this->grammar->get('Statement');
        $parent->addChild($node);

        while (true) {
            $token = $this->currentToken($tokenizer);

            if ($token->getType() === TokenizerInterface::OP_SEMICOLON) {
                $this->nextToken($tokenizer);
                break;
            } else if ($token->getType() === TokenizerInterface::KEYWORD_IF) {
                /** @var IfExpression $ifExpressionRule */
                $ifExpressionRule = $this->rule->get('IfExpression');
                yield $ifExpressionRule($node, $tokenizer);
                break;
            } else if ($token->getType() === TokenizerInterface::KEYWORD_WHILE) {
                /** @var WhileExpression $whileExpressionRule */
                $whileExpressionRule = $this->rule->get('WhileExpression');
                yield $whileExpressionRule($node, $tokenizer);
            } else if ($token->getType() === TokenizerInterface::KEYWORD_FOR) {
                /** @var ForExpression $forExpressionRule */
                $forExpressionRule = $this->rule->get('ForExpression');
                yield $forExpressionRule($node, $tokenizer);
            } else if ($token->getType() === TokenizerInterface::KEYWORD_BREAK) {
                $this->parseBreak($node, $tokenizer);
                break;
            } else if ($token->getType() === TokenizerInterface::KEYWORD_CONTINUE) {
                $this->parseContinue($node, $tokenizer);
                break;
            } else if ($token->getType() === TokenizerInterface::KEYWORD_WITH) {
                $this->parseWith($node, $tokenizer);
            } else if ($token->getType() === TokenizerInterface::KEYWORD_RETURN) {
                $this->parseReturn($node, $tokenizer);
                break;
            } else if ($token->getType() === TokenizerInterface::OP_LEFT_CURLY) {
                $this->nextToken($tokenizer);

                /** @var Grammar\CompoundStatement $compoundStatement */
                $compoundStatement = $this->grammar->get('CompoundStatement');
                $parent->addChild($compoundStatement);

                /** @var Rule\StatementList $statementListRule */
                $statementListRule = $this->rule->get('StatementListRule');
                yield $statementListRule($compoundStatement, $tokenizer);

                $token = $this->currentToken($tokenizer);
                if ($token->getType() !== TokenizerInterface::OP_RIGHT_CURLY) {
                    throw new LexicalError(static::MESSAGE_MISSING_RIGHT_CURLY_BRACE,
                        null, $token->getLine(), $token->getLineOffset(), $token->getStart());
                }

                $this->nextToken($tokenizer);
                break;
            } else {
                $rule = $this->getVariableListOrExpressionRule();
                $rule($node, $tokenizer);

                $token = $this->currentToken($tokenizer);
                if ($token->getType() !== TokenizerInterface::OP_SEMICOLON) {
                    throw new LexicalError(static::MESSAGE_MISSING_SEMICOLON,
                        null, $token->getLine(), $token->getLineOffset(), $token->getStart());
                }

                $this->nextToken($tokenizer);
                break;
            }
        }

        $node->optimize();
    }

    /**
     * @param RecursiveGrammarInterface $parent
     * @param BaseTokenizerInterface $tokenizer
     * @return void
     * @throws LexicalError
     */
    protected function parseBreak(RecursiveGrammarInterface $parent, BaseTokenizerInterface $tokenizer)
    {
        /** @var Grammar\BreakKeyword $breakKeyword */
        $breakKeyword = $this->grammar->get('BreakKeyword');
        $parent->addChild($breakKeyword);

        $token = $this->nextToken($tokenizer);
        if ($token->getType() !== TokenizerInterface::OP_SEMICOLON) {
            throw new LexicalError(static::MESSAGE_MISSING_SEMICOLON,
                null, $token->getLine(), $token->getLineOffset(), $token->getStart());
        }

        $this->nextToken($tokenizer);
    }

    /**
     * @param RecursiveGrammarInterface $parent
     * @param BaseTokenizerInterface $tokenizer
     * @return void
     * @throws LexicalError
     */
    protected function parseContinue(RecursiveGrammarInterface $parent, BaseTokenizerInterface $tokenizer)
    {
        /** @var Grammar\ContinueKeyword $continueKeyword */
        $continueKeyword = $this->grammar->get('ContinueKeyword');
        $parent->addChild($continueKeyword);

        $token = $this->nextToken($tokenizer);
        if ($token->getType() !== TokenizerInterface::OP_SEMICOLON) {
            throw new LexicalError(static::MESSAGE_MISSING_SEMICOLON,
                null, $token->getLine(), $token->getLineOffset(), $token->getStart());
        }

        $this->nextToken($tokenizer);
    }

    /**
     * @param RecursiveGrammarInterface $parent
     * @param BaseTokenizerInterface $tokenizer
     * @return void
     * @throws LexicalError
     */
    protected function parseWith(RecursiveGrammarInterface $parent, BaseTokenizerInterface $tokenizer)
    {
        /** @var Grammar\WithKeyword $withKeyword */
        $withKeyword = $this->grammar->get('WithKeyword');
        $parent->addChild($withKeyword);

        $token = $this->nextToken($tokenizer);
        if ($token->getType() !== TokenizerInterface::OP_LEFT_BRACKET) {
            throw new LexicalError(static::MESSAGE_MISSING_LEFT_BRACKET,
                null, $token->getLine(), $token->getLineOffset(), $token->getStart());
        }

        $this->nextToken($tokenizer);
        $rule = $this->getExpressionRule();
        $rule($withKeyword, $tokenizer);

        $token = $this->currentToken($tokenizer);
        if ($token->getType() !== TokenizerInterface::OP_RIGHT_BRACKET) {
            throw new LexicalError(static::MESSAGE_MISSING_RIGHT_BRACKET,
                null, $token->getLine(), $token->getLineOffset(), $token->getStart());
        }
        $this->nextToken($tokenizer);
    }

    /**
     * @param RecursiveGrammarInterface $parent
     * @param BaseTokenizerInterface $tokenizer
     * @return void
     * @throws LexicalError
     */
    protected function parseReturn(RecursiveGrammarInterface $parent, BaseTokenizerInterface $tokenizer)
    {
        /** @var Grammar\ReturnKeyword $returnKeyword */
        $returnKeyword = $this->grammar->get('ReturnKeyword');
        $parent->addChild($returnKeyword);

        $this->nextToken($tokenizer);
        $rule = $this->getExpressionRule();
        $rule($returnKeyword, $tokenizer);

        $token = $this->currentToken($tokenizer);
        if ($token->getType() !== TokenizerInterface::OP_SEMICOLON) {
            throw new LexicalError(static::MESSAGE_MISSING_SEMICOLON,
                null, $token->getLine(), $token->getLineOffset(), $token->getStart());
        }

        $this->nextToken($tokenizer);
    }

    /**
     * @return Rule\RuleInterface|Rule\Expression
     */
    public function getExpressionRule()
    {
        if ($this->expressionRule === null) {
            $this->expressionRule = $this->rule->get('Expression');
        }
        return $this->expressionRule;
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
     * @return Rule\RuleInterface|Rule\VariableListOrExpression
     */
    public function getVariableListOrExpressionRule()
    {
        if ($this->variableListOrExpressionRule === null) {
            $this->variableListOrExpressionRule = $this->rule->get('VariableListOrExpression');
        }

        return $this->variableListOrExpressionRule;
    }
}
