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
 * @package Gplanchat\EcmaScript\Lexer
 */


namespace Gplanchat\EcmaScript\Lexer\Rule;

use Gplanchat\EcmaScript\Lexer\Exception\LexicalError;
use Gplanchat\Lexer\Grammar\RecursiveGrammarInterface;
use Gplanchat\EcmaScript\Tokenizer\TokenizerInterface;
use Gplanchat\Lexer\Grammar;
use Gplanchat\EcmaScript\Lexer\Rule;
use Gplanchat\Tokenizer\TokenizerInterface as BaseTokenizerInterface;

/**
 * Class Statement
 * @package Gplanchat\EcmaScript\Lexer\Rule
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
 *     throw Expression ;
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
    protected $ifExpressionRule = null;

    /**
     * @var Rule\RuleInterface
     */
    protected $whileExpressionRule = null;

    /**
     * @var Rule\RuleInterface
     */
    protected $forExpressionRule = null;

    /**
     * @var Rule\RuleInterface
     */
    protected $switchStatementRule = null;

    /**
     * @var Rule\RuleInterface
     */
    protected $expressionRule = null;

    /**
     * @var Rule\RuleInterface
     */
    protected $assignmentExpressionRule = null;

    /**
     * @var Rule\RuleInterface
     */
    protected $variableListOrExpressionRule = null;

    /**
     * @var Rule\RuleInterface
     */
    protected $conditionRule = null;

    /**
     * @var Rule\RuleInterface
     */
    protected $statementListRule = null;

    /**
     * @var FunctionExpression
     */
    protected $functionExpressionRule = null;

    /**
     * @param RecursiveGrammarInterface $parent
     * @param BaseTokenizerInterface $tokenizer
     * @param int $level
     * @return \Generator|null
     * @throws LexicalError
     */
    public function run(RecursiveGrammarInterface $parent, BaseTokenizerInterface $tokenizer, $level = 0)
    {
        /** @var Grammar\Statement $node */
        $node = $this->grammar->get('Statement');
        $parent->addChild($node);

        while (true) {
            $token = $this->currentToken($tokenizer);

            if ($token->is(TokenizerInterface::OP_SEMICOLON)) {
                $this->nextToken($tokenizer);
                break;
            } else if ($token->is(TokenizerInterface::KEYWORD_IF)) {
                yield $this->getIfExpressionRule()->run($node, $tokenizer, $level + 1);
                break;
            } else if ($token->is(TokenizerInterface::KEYWORD_WHILE)) {
                yield $this->getWhileExpressionRule()->run($node, $tokenizer, $level + 1);
            } else if ($token->is(TokenizerInterface::KEYWORD_FOR)) {
                yield $this->getForExpressionRule()->run($node, $tokenizer, $level + 1);
            } else if ($token->is(TokenizerInterface::KEYWORD_SWITCH)) {
                /** @var Grammar\SwitchKeyword $switchKeyword */
                $switchKeyword = $this->grammar->get('SwitchKeyword');
                $parent->addChild($switchKeyword);

                $token = $this->nextToken($tokenizer);
                if (!$token->is(TokenizerInterface::OP_LEFT_BRACKET)) {
                    throw new LexicalError(static::MESSAGE_MISSING_LEFT_BRACKET,
                        $token->getPath(), $token->getLine(), $token->getLineOffset(), $token->getStart());
                }

                $this->nextToken($tokenizer);
                yield $this->getExpressionRule()->run($switchKeyword, $tokenizer, $level + 1);

                $token = $this->currentToken($tokenizer);
                if (!$token->is(TokenizerInterface::OP_RIGHT_BRACKET)) {
                    throw new LexicalError(static::MESSAGE_MISSING_RIGHT_BRACKET,
                        $token->getPath(), $token->getLine(), $token->getLineOffset(), $token->getStart());
                }

                $token = $this->nextToken($tokenizer);
                if (!$token->is(TokenizerInterface::OP_LEFT_CURLY)) {
                    throw new LexicalError(static::MESSAGE_MISSING_LEFT_CURLY_BRACE,
                        $token->getPath(), $token->getLine(), $token->getLineOffset(), $token->getStart());
                }
                $this->nextToken($tokenizer);

                while (true) {
                    yield $this->getSwitchStatementRule()->run($node, $tokenizer, $level + 1);

                    $token = $this->currentToken($tokenizer);
                    if ($token->is(TokenizerInterface::OP_RIGHT_CURLY)) {
                        break;
                    }
                }
                $this->nextToken($tokenizer);
            } else if ($token->is(TokenizerInterface::KEYWORD_BREAK)) {
                $this->parseBreak($node, $tokenizer);
                break;
            } else if ($token->is(TokenizerInterface::KEYWORD_CONTINUE)) {
                $this->parseContinue($node, $tokenizer);
                break;
            } else if ($token->is(TokenizerInterface::KEYWORD_WITH)) {
                /** @var Grammar\WithKeyword $withKeyword */
                $withKeyword = $this->grammar->get('WithKeyword');
                $parent->addChild($withKeyword);

                $token = $this->nextToken($tokenizer);
                if (!$token->is(TokenizerInterface::OP_LEFT_BRACKET)) {
                    throw new LexicalError(static::MESSAGE_MISSING_LEFT_BRACKET,
                        $token->getPath(), $token->getLine(), $token->getLineOffset(), $token->getStart());
                }

                $this->nextToken($tokenizer);
                yield $this->getExpressionRule()->run($withKeyword, $tokenizer, $level + 1);

                $token = $this->currentToken($tokenizer);
                if (!$token->is(TokenizerInterface::OP_RIGHT_BRACKET)) {
                    throw new LexicalError(static::MESSAGE_MISSING_RIGHT_BRACKET,
                        $token->getPath(), $token->getLine(), $token->getLineOffset(), $token->getStart());
                }
                $this->nextToken($tokenizer);
            } else if ($token->is(TokenizerInterface::KEYWORD_RETURN)) {
                /** @var Grammar\ReturnKeyword $returnKeyword */
                $returnKeyword = $this->grammar->get('ReturnKeyword');
                $parent->addChild($returnKeyword);

                $this->nextToken($tokenizer);
                yield $this->getAssignmentExpressionRule()->run($returnKeyword, $tokenizer, $level + 1);

                $token = $this->currentToken($tokenizer);
                if ($token->is(TokenizerInterface::OP_SEMICOLON)) {
                    $this->nextToken($tokenizer);
                }
                break;
            } else if ($token->is(TokenizerInterface::KEYWORD_THROW)) {
                /** @var Grammar\ThrowKeyword $throwKeyword */
                $throwKeyword = $this->grammar->get('ThrowKeyword');
                $parent->addChild($throwKeyword);

                $this->nextToken($tokenizer);
                yield $this->getAssignmentExpressionRule()->run($throwKeyword, $tokenizer, $level + 1);

                $token = $this->currentToken($tokenizer);
                if ($token->is(TokenizerInterface::OP_SEMICOLON)) {
                    $this->nextToken($tokenizer);
                }
                break;
            } else if ($token->is(TokenizerInterface::OP_LEFT_CURLY)) {
                $this->nextToken($tokenizer);

                /** @var Grammar\CompoundStatement $compoundStatement */
                $compoundStatement = $this->grammar->get('CompoundStatement');
                $parent->addChild($compoundStatement);

                yield $this->getStatementListRule()->run($compoundStatement, $tokenizer, $level + 1);

                $token = $this->currentToken($tokenizer);
                if (!$token->is(TokenizerInterface::OP_RIGHT_CURLY)) {
                    throw new LexicalError(static::MESSAGE_MISSING_RIGHT_CURLY_BRACE,
                        $token->getPath(), $token->getLine(), $token->getLineOffset(), $token->getStart());
                }

                $this->nextToken($tokenizer);
                break;
            } else if ($token->is(TokenizerInterface::KEYWORD_FUNCTION)) {
                yield $this->getFunctionExpressionRule()->run($node, $tokenizer, $level + 1);
                break;
            } else {
                yield $this->getVariableListOrExpressionRule()->run($node, $tokenizer, $level + 1);

                $token = $this->currentToken($tokenizer, false);
                if ($token->is(TokenizerInterface::OP_SEMICOLON)) {
                    $this->nextToken($tokenizer);
                }
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
        if ($token->is(TokenizerInterface::OP_SEMICOLON)) {
            $this->nextToken($tokenizer);
        }
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
        if ($token->is(TokenizerInterface::OP_SEMICOLON)) {
            $this->nextToken($tokenizer);
        }
    }

    /**
     * @return Rule\RuleInterface|Rule\IfExpression
     */
    public function getIfExpressionRule()
    {
        if ($this->ifExpressionRule === null) {
            $this->ifExpressionRule = $this->rule->get('IfExpression');
        }
        return $this->ifExpressionRule;
    }

    /**
     * @return Rule\RuleInterface|Rule\WhileExpression
     */
    public function getWhileExpressionRule()
    {
        if ($this->whileExpressionRule === null) {
            $this->whileExpressionRule = $this->rule->get('WhileExpression');
        }
        return $this->whileExpressionRule;
    }

    /**
     * @return Rule\RuleInterface|Rule\ForExpression
     */
    public function getForExpressionRule()
    {
        if ($this->forExpressionRule === null) {
            $this->forExpressionRule = $this->rule->get('ForExpression');
        }
        return $this->forExpressionRule;
    }

    /**
     * @return Rule\RuleInterface|Rule\SwitchStatement
     */
    public function getSwitchStatementRule()
    {
        if ($this->switchStatementRule === null) {
            $this->switchStatementRule = $this->rule->get('SwitchStatement');
        }
        return $this->switchStatementRule;
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
     * @return Rule\RuleInterface|Rule\AssignmentExpression
     */
    public function getAssignmentExpressionRule()
    {
        if ($this->assignmentExpressionRule === null) {
            $this->assignmentExpressionRule = $this->rule->get('AssignmentExpression');
        }
        return $this->assignmentExpressionRule;
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

    /**
     * @return Rule\RuleInterface|Rule\StatementList
     */
    public function getStatementListRule()
    {
        if ($this->statementListRule === null) {
            $this->statementListRule = $this->rule->get('StatementList');
        }

        return $this->statementListRule;
    }

    /**
     * @return FunctionExpression
     */
    public function getFunctionExpressionRule()
    {
        if ($this->functionExpressionRule === null) {
            $this->functionExpressionRule = $this->rule->get('FunctionExpression');
        }

        return $this->functionExpressionRule;
    }
}
