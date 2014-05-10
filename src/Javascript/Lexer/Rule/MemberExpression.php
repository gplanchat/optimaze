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

use Gplanchat\Lexer\Grammar;
use Gplanchat\Lexer\Grammar\RecursiveGrammarInterface;
use Gplanchat\Javascript\Lexer\Exception\LexicalError;
use Gplanchat\Javascript\Tokenizer\TokenizerInterface;
use Gplanchat\Tokenizer\TokenizerInterface as BaseTokenizerInterface;

/**
 * Class MemberExpression
 * @package Gplanchat\Javascript\Lexer\Rule
 *
 * MemberExpression:
 *     PrimaryExpression
 *     PrimaryExpression . MemberExpression
 *     PrimaryExpression [ Expression ]
 *     PrimaryExpression ( ArgumentListOpt )
 */
class MemberExpression
    implements RuleInterface
{
    use RuleTrait;

    /**
     * @var PrimaryExpression
     */
    protected $primaryExpressionRule = null;

    /**
     * @var Expression
     */
    protected $expressionRule = null;

    /**
     * @var ArgumentList
     */
    protected $argumentListRule = null;

    /**
     * @param RecursiveGrammarInterface $parent
     * @param BaseTokenizerInterface $tokenizer
     * @return \Generator|null
     * @throws LexicalError
     */
    public function run(RecursiveGrammarInterface $parent, BaseTokenizerInterface $tokenizer)
    {
        /** @var Grammar\MemberExpression $node */
        $node = $this->grammar->get('MemberExpression');
        $parent->addChild($node);

        while (true) {
            yield $this->getPrimaryExpressionRule()->run($node, $tokenizer);

            $token = $this->currentToken($tokenizer);
            if ($token->getType() === TokenizerInterface::OP_LEFT_SQUARE_BRACKET) {
                $this->nextToken($tokenizer);

                yield $this->getExpressionRule()->run($node, $tokenizer);

                $token = $this->currentToken($tokenizer);
                if ($token->getType() !== TokenizerInterface::OP_RIGHT_SQUARE_BRACKET) {
                    throw new LexicalError(static::MESSAGE_MISSING_RIGHT_SQUARE_BRACKET,
                        null, $token->getLine(), $token->getLineOffset(), $token->getStart());
                }
                break;
            } else if ($token->getType() === TokenizerInterface::OP_LEFT_BRACKET) {
                $this->nextToken($tokenizer);

                yield $this->getArgumentListrule()->run($node, $tokenizer);

                $token = $this->currentToken($tokenizer);
                if ($token->getType() !== TokenizerInterface::OP_RIGHT_BRACKET) {
                    throw new LexicalError(static::MESSAGE_MISSING_RIGHT_BRACKET,
                        null, $token->getLine(), $token->getLineOffset(), $token->getStart());
                }
                break;
            } else if ($token->getType() === TokenizerInterface::OP_DOT) {
                /** @var Grammar\DotOperator $dotOperator */
                $dotOperator = $this->grammar
                    ->get('DotOperator')
                ;
                $node->addChild($dotOperator);
            } else {
                break;
            }
            $this->nextToken($tokenizer);
        }

        $node->optimize();
    }

    /**
     * @return PrimaryExpression
     */
    public function getPrimaryExpressionRule()
    {
        if ($this->primaryExpressionRule === null) {
            $this->primaryExpressionRule = $this->rule->get('PrimaryExpression');
        }

        return $this->primaryExpressionRule;
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

    /**
     * @return ArgumentList
     */
    public function getArgumentListRule()
    {
        if ($this->argumentListRule === null) {
            $this->argumentListRule = $this->rule->get('ArgumentList');
        }

        return $this->argumentListRule;
    }
}
