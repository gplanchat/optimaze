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
use Gplanchat\Tokenizer\TokenizerInterface as BaseTokenizerInterface;

/**
 * Class Expression
 * @package Gplanchat\EcmaScript\Lexer\Rule
 *
 * UnaryExpression:
 *     MemberExpression
 *     UnaryOperator UnaryExpression
 *     - UnaryExpression
 *     IncrementOperator MemberExpression
 *     MemberExpression IncrementOperator
 *     new Constructor
 *     delete MemberExpression
 */
class UnaryExpression
    implements RuleInterface
{
    use RuleTrait;

    /**
     * @var Expression
     */
    protected $expressionRule = null;

    /**
     * @var array
     */
    protected static $unaryOperators = [
        TokenizerInterface::OP_NOT,
        TokenizerInterface::KEYWORD_TYPEOF,
        TokenizerInterface::KEYWORD_VOID,
        TokenizerInterface::OP_PLUS,
        TokenizerInterface::OP_MINUS
    ];

    /**
     * @var array
     */
    protected static $incrementOperators = [
        TokenizerInterface::OP_INCREMENT,
        TokenizerInterface::OP_DECREMENT
    ];

    /**
     * @var array
     */
    protected static $primaryExpressionTokens = [
        TokenizerInterface::TOKEN_IDENTIFIER,
        TokenizerInterface::TOKEN_NUMBER_INTEGER,
        TokenizerInterface::TOKEN_NUMBER_FLOATING_POINT,
        TokenizerInterface::TOKEN_STRING,
        TokenizerInterface::KEYWORD_TRUE,
        TokenizerInterface::KEYWORD_FALSE,
        TokenizerInterface::KEYWORD_NULL,
        TokenizerInterface::KEYWORD_THIS
    ];

    /**
     * @var array
     */
    protected static $excludedTokens = [
        TokenizerInterface::OP_SEMICOLON,
        TokenizerInterface::OP_STRICT_EQ,
        TokenizerInterface::OP_STRICT_NE,
        TokenizerInterface::OP_EQ,
        TokenizerInterface::OP_NE
    ];

    /**
     * @param RecursiveGrammarInterface $parent
     * @param BaseTokenizerInterface $tokenizer
     * @param int $level
     * @return \Generator|null
     * @throws LexicalError
     */
    public function run(RecursiveGrammarInterface $parent, BaseTokenizerInterface $tokenizer, $level = 0)
    {
        $token = $this->currentToken($tokenizer);

        /** @var Grammar\UnaryExpression $node */
        $node = $this->grammar->get('UnaryExpression');
        $parent->addChild($node);

        /** @var MemberExpression $memberExpressionRule */
        $memberExpressionRule = $this->rule->get('MemberExpression');

        while (true) {
            if ($token->isIn(static::$unaryOperators)) {
                /** @var Grammar\UnaryOperator $unaryOperator */
                $unaryOperator = $this->grammar
                    ->get('UnaryOperator', [$token->getValue()])
                ;
                $node->addChild($unaryOperator);
                $this->nextToken($tokenizer);

                yield $memberExpressionRule->run($node, $tokenizer, $level + 1);

                $token = $this->currentToken($tokenizer);
            } else if ($token->isIn(static::$incrementOperators)) {
                /** @var Grammar\IncrementOperator $incrementOperator */
                $incrementOperator = $this->grammar
                    ->get('IncrementOperator', [$token->getValue()])
                ;
                $node->addChild($incrementOperator);

                $this->nextToken($tokenizer);
                yield $memberExpressionRule->run($node, $tokenizer, $level + 1);
                $node->optimize();
                break;
            } else if ($token->is(TokenizerInterface::KEYWORD_DELETE)) {
                /** @var Grammar\DeleteKeyword $deleteKeyword */
                $deleteKeyword = $this->grammar
                    ->get('DeleteKeyword')
                ;
                $node->addChild($deleteKeyword);

                $this->nextToken($tokenizer);
                yield $memberExpressionRule->run($node, $tokenizer, $level + 1);
                break;
            } else if ($token->is(TokenizerInterface::KEYWORD_NEW)) {
                /** @var Grammar\NewKeyword $newKeyword */
                $newKeyword = $this->grammar
                    ->get('NewKeyword')
                ;
                $node->addChild($newKeyword);

                $this->nextToken($tokenizer);
                yield $this->rule->get('Constructor')->run($node, $tokenizer, $level + 1);
                break;
            } else if ($token->is(TokenizerInterface::KEYWORD_THIS)) {
                yield $this->rule->get('Constructor')->run($node, $tokenizer, $level + 1);
                $node->optimize();
                break;
            } else if ($token->is(TokenizerInterface::OP_LEFT_BRACKET)) {
                $this->nextToken($tokenizer);

                yield $this->getExpressionRule()->run($node, $tokenizer, $level + 1);

                $token = $this->currentToken($tokenizer);
                if (!$token->is(TokenizerInterface::OP_RIGHT_BRACKET)) {
                    throw new LexicalError(static::MESSAGE_MISSING_RIGHT_BRACKET,
                        $token->getPath(), $token->getLine(), $token->getLineOffset(), $token->getStart());
                }
                $this->nextToken($tokenizer);
            } else if (!$token->isIn(static::$excludedTokens)) {
                yield $memberExpressionRule->run($node, $tokenizer, $level + 1);

                $token = $this->currentToken($tokenizer);
                if ($token->isIn(static::$incrementOperators)) {
                    /** @var Grammar\IncrementOperator $incrementOperator */
                    $incrementOperator = $this->grammar
                        ->get('IncrementOperator', [$token->getValue()])
                    ;
                    $node->addChild($incrementOperator);

                    $this->nextToken($tokenizer);
                }
                $node->optimize();
                break;
            } else  {
                break;
            }
        }
    }

    /**
     * @return Expression
     */
    public function getExpressionRule()
    {
        if ($this->expressionRule === null) {
            $this->expressionRule = $this->rule->get('Expression');
        }

        return $this->expressionRule;
    }
}
