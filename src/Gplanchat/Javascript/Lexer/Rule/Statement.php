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
use Gplanchat\Javascript\Lexer\Grammar\RecursiveGrammarInterface;
use Gplanchat\Javascript\Tokenizer\TokenizerInterface;
use Gplanchat\Javascript\Lexer\Grammar;
use Gplanchat\Javascript\Lexer\Rule;

/**
 * Class Statement
 * @package Gplanchat\Javascript\Lexer\Rule
 *
 * Statement:
 *     ;
 *     if Condition Statement
 *     if Condition Statement else Statement
 *     while Condition Statement
 *     for ( ; Expression ; Expression ) Statement
 *     for ( VariableListOrExpression ; Expression ; Expression ) Statement
 *     for ( VariableListOrExpression in Expression ) Statement
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
     * @param TokenizerInterface $tokenizer
     * @return void
     * @throws LexicalError
     */
    public function parse(RecursiveGrammarInterface $parent, TokenizerInterface $tokenizer)
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
                $this->parseConditionChain($node, $tokenizer);
                break;
            } else if ($token->getType() === TokenizerInterface::KEYWORD_WHILE) {
//                echo $token->dump();
//                return;
                $this->parseWhile($node, $tokenizer);
            } else if ($token->getType() === TokenizerInterface::KEYWORD_FOR) {
//                echo $token->dump();
//                return;
                $this->parseFor($node, $tokenizer, $this->getExpressionRule());
            } else if ($token->getType() === TokenizerInterface::KEYWORD_BREAK) {
//                echo $token->dump();
//                return;
                $this->parseBreak($node, $tokenizer);
                break;
            } else if ($token->getType() === TokenizerInterface::KEYWORD_CONTINUE) {
//                echo $token->dump();
//                return;
                $this->parseContinue($node, $tokenizer);
                break;
            } else if ($token->getType() === TokenizerInterface::KEYWORD_WITH) {
//                echo $token->dump();
//                return;
                $this->parseWith($node, $tokenizer, $this->getExpressionRule());
            } else if ($token->getType() === TokenizerInterface::KEYWORD_RETURN) {
//                echo $token->dump();
//                return;
                $this->parseReturn($node, $tokenizer, $this->getExpressionRule());
                break;
            } else if ($token->getType() === TokenizerInterface::OP_LEFT_CURLY) {
//                echo $token->dump();
//                return;
                $this->parseCoumpoundStatement($node, $tokenizer);
                break;
            } else {
                $this->getVariableListOrExpressionRule()->parse($node, $tokenizer);
                $token = $this->nextToken($tokenizer);
//                echo $token->dump();

                if ($token->getType() === TokenizerInterface::OP_SEMICOLON) {
                    break;
                }
            }
        }
    }

    /**
     * @param RecursiveGrammarInterface $parent
     * @param TokenizerInterface $tokenizer
     * @return void
     * @throws LexicalError
     */
    protected function parseConditionChain(RecursiveGrammarInterface $parent, TokenizerInterface $tokenizer)
    {
        /** @var Grammar\ConditionChain $conditionChain */
        $conditionChain = $this->grammar->get('ConditionChain');
        $parent->addChild($conditionChain);

        while (true) {
            /** @var Grammar\IfKeyword $ifKeyword */
            $ifKeyword = $this->grammar->get('IfKeyword');
            $conditionChain->addChild($ifKeyword);

            $this->nextToken($tokenizer);
            $this->getConditionRule()->parse($ifKeyword, $tokenizer);

            $this->parse($parent, $tokenizer);

            $token = $this->currentToken($tokenizer);
            if ($token->getType() !== TokenizerInterface::KEYWORD_ELSE) {
                break;
            }

            /** @var Grammar\ElseKeyword $elseKeyword */
            $elseKeyword = $this->grammar->get('ElseKeyword');
            $ifKeyword->addChild($elseKeyword);

            $token = $this->nextToken($tokenizer);
            if ($token->getType() !== TokenizerInterface::KEYWORD_IF) {
                $this->parse($parent, $tokenizer);
                break;
            }
        }
    }

    /**
     * @param RecursiveGrammarInterface $parent
     * @param TokenizerInterface $tokenizer
     * @return void
     * @throws LexicalError
     */
    protected function parseWhile(RecursiveGrammarInterface $parent, TokenizerInterface $tokenizer)
    {
        /** @var Grammar\WhileKeyword $whileKeyword */
        $whileKeyword = $this->grammar->get('WhileKeyword');
        $parent->addChild($whileKeyword);

        $this->nextToken($tokenizer);

        $this->getConditionRule()->parse($whileKeyword, $tokenizer);
    }

    /**
     * @param RecursiveGrammarInterface $parent
     * @param TokenizerInterface $tokenizer
     * @return void
     * @throws LexicalError
     */
    protected function parseFor(
        RecursiveGrammarInterface $parent,
        TokenizerInterface $tokenizer)
    {
        /** @var Grammar\ForKeyword $forKeyword */
        $forKeyword = $this->grammar->get('ForKeyword');
        $parent->addChild($forKeyword);

        $token = $this->nextToken($tokenizer);
        if ($token->getType() !== TokenizerInterface::OP_LEFT_BRACKET) {
            throw new LexicalError('Invalid expression : missing left bracket',
                null, $token->getLine(), $token->getStart());
        }

        $token = $this->nextToken($tokenizer);
        if ($token->getType() === TokenizerInterface::OP_SEMICOLON) {
            $token = $this->nextToken($tokenizer);
        } else {
            $this->getVariableListOrExpressionRule()->parse($forKeyword, $tokenizer);
            $token = $this->currentToken($tokenizer);

            if ($token->getType() === TokenizerInterface::OP_SEMICOLON) {
                $this->nextToken($tokenizer);

                $this->getExpressionRule()->parse($forKeyword, $tokenizer);
                $token = $this->currentToken($tokenizer);

                if ($token->getType() !== TokenizerInterface::OP_SEMICOLON) {
                    throw new LexicalError('Invalid expression : missing semicolon',
                        null, $token->getLine(), $token->getStart());
                }

                $this->getExpressionRule()->parse($forKeyword, $tokenizer);
                $token = $this->currentToken($tokenizer);
            } else if ($token->getType() !== TokenizerInterface::KEYWORD_IN) {
                $this->nextToken($tokenizer);

                $this->getExpressionRule()->parse($forKeyword, $tokenizer);
                $token = $this->currentToken($tokenizer);
            } else {
                throw new LexicalError('Invalid expression : missing semicolon or "in" keyword',
                    null, $token->getLine(), $token->getStart());
            }
        }

        if ($token->getType() !== TokenizerInterface::OP_RIGHT_BRACKET) {
            throw new LexicalError('Invalid expression : missing right bracket',
                null, $token->getLine(), $token->getStart());
        }
        $this->nextToken($tokenizer);
    }

    /**
     * @param RecursiveGrammarInterface $parent
     * @param TokenizerInterface $tokenizer
     * @return void
     * @throws LexicalError
     */
    protected function parseBreak(RecursiveGrammarInterface $parent, TokenizerInterface $tokenizer)
    {
        /** @var Grammar\BreakKeyword $breakKeyword */
        $breakKeyword = $this->grammar->get('BreakKeyword');
        $parent->addChild($breakKeyword);

        $token = $this->nextToken($tokenizer);
        if ($token->getType() !== TokenizerInterface::OP_SEMICOLON) {
            throw new LexicalError('Invalid expression : missing semicolon',
                null, $token->getLine(), $token->getStart());
        }

        $this->nextToken($tokenizer);
    }

    /**
     * @param RecursiveGrammarInterface $parent
     * @param TokenizerInterface $tokenizer
     * @return void
     * @throws LexicalError
     */
    protected function parseContinue(RecursiveGrammarInterface $parent, TokenizerInterface $tokenizer)
    {
        /** @var Grammar\ContinueKeyword $continueKeyword */
        $continueKeyword = $this->grammar->get('ContinueKeyword');
        $parent->addChild($continueKeyword);

        $token = $this->nextToken($tokenizer);
        if ($token->getType() !== TokenizerInterface::OP_SEMICOLON) {
            throw new LexicalError('Invalid expression : missing semicolon',
                null, $token->getLine(), $token->getStart());
        }

        $this->nextToken($tokenizer);
    }

    /**
     * @param RecursiveGrammarInterface $parent
     * @param TokenizerInterface $tokenizer
     * @return void
     * @throws LexicalError
     */
    protected function parseWith(RecursiveGrammarInterface $parent, TokenizerInterface $tokenizer)
    {
        /** @var Grammar\WithKeyword $withKeyword */
        $withKeyword = $this->grammar->get('WithKeyword');
        $parent->addChild($withKeyword);

        $token = $this->nextToken($tokenizer);
        if ($token->getType() !== TokenizerInterface::OP_LEFT_BRACKET) {
            throw new LexicalError('Invalid expression : missing left bracket',
                null, $token->getLine(), $token->getStart());
        }

        $this->nextToken($tokenizer);
        $this->getExpressionRule()->parse($withKeyword, $tokenizer);

        $token = $this->currentToken($tokenizer);
        if ($token->getType() !== TokenizerInterface::OP_RIGHT_BRACKET) {
            throw new LexicalError('Invalid expression : missing right bracket',
                null, $token->getLine(), $token->getStart());
        }
    }

    /**
     * @param RecursiveGrammarInterface $parent
     * @param TokenizerInterface $tokenizer
     * @return void
     * @throws LexicalError
     */
    protected function parseReturn(RecursiveGrammarInterface $parent, TokenizerInterface $tokenizer)
    {
        /** @var Grammar\ReturnKeyword $returnKeyword */
        $returnKeyword = $this->grammar->get('ReturnKeyword');
        $parent->addChild($returnKeyword);

        $token = $this->nextToken($tokenizer);
        $this->getExpressionRule()->parse($returnKeyword, $tokenizer);

        if ($token->getType() !== TokenizerInterface::OP_SEMICOLON) {
            throw new LexicalError('Invalid expression : missing semicolon',
                null, $token->getLine(), $token->getStart());
        }

        $this->nextToken($tokenizer);
    }

    /**
     * @param RecursiveGrammarInterface $parent
     * @param TokenizerInterface $tokenizer
     * @return void
     * @throws LexicalError
     */
    protected function parseCoumpoundStatement(RecursiveGrammarInterface $parent, TokenizerInterface $tokenizer)
    {
        /** @var Grammar\CompoundStatement $compoundStatement */
        $compoundStatement = $this->grammar->get('CompoundStatement');
        $parent->addChild($compoundStatement);

        $this->nextToken($tokenizer);

        /** @var Rule\StatementList $statementListRule */
        $statementListRule = $this->rule->get('StatementListRule');;
        $statementListRule->parse($compoundStatement, $tokenizer);

        $token = $this->currentToken($tokenizer);
        if ($token->getType() !== TokenizerInterface::OP_RIGHT_CURLY) {
            throw new LexicalError('Invalid expression : missing right curly brace',
                null, $token->getLine(), $token->getStart());
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
