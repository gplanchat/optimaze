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

use Gplanchat\EcmaScript\Tokenizer\TokenizerInterface;
use Gplanchat\EcmaScript\Lexer\Exception\LexicalError;
use Gplanchat\Lexer\Grammar;
use Gplanchat\Lexer\Grammar\RecursiveGrammarInterface;
use Gplanchat\Tokenizer\TokenizerInterface as BaseTokenizerInterface;

/**
 * Class PrimaryExpression
 * @package Gplanchat\EcmaScript\Lexer\Rule
 *
 * PrimaryExpression:
 *     ClosureExpression
 *     ( Expression )
 *     ArrayExpression
 *     ObjectExpression
 *     Identifier
 *     IntegerLiteral
 *     FloatingPointLiteral
 *     StringLiteral
 *     RegularExpressionLiteral
 *     false
 *     true
 *     null
 *     this
 */
class PrimaryExpression
    implements RuleInterface
{
    use RuleTrait;

    /**
     * @var Expression
     */
    protected $expressionRule = null;

    /**
     * @var ClosureExpression
     */
    protected $closureExpressionRule = null;

    /**
     * @var ArrayExpression
     */
    protected $arrayExpressionRule = null;

    /**
     * @var ObjectExpression
     */
    protected $objectExpressionRule = null;

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

        /** @var Grammar\PrimaryExpression $node */
        $node = $this->grammar->get('PrimaryExpression');
        $parent->addChild($node);

        if ($token->getType() === TokenizerInterface::OP_LEFT_CURLY) {
            yield $this->getObjectExpressionRule()->run($node, $tokenizer, $level + 1);
            $node->optimize();
        } else if ($token->getType() === TokenizerInterface::OP_LEFT_SQUARE_BRACKET) {
            yield $this->getArrayExpressionRule()->run($node, $tokenizer, $level + 1);
            $node->optimize();
        } else if ($token->getType() === TokenizerInterface::TOKEN_IDENTIFIER) {
            /** @var Grammar\Identifier $child */
            $child = $this->grammar
                ->get('Identifier', [$token->getValue()])
            ;
            $node->addChild($child);
            $this->nextToken($tokenizer);
            $node->optimize();
        } else if ($token->getType() === TokenizerInterface::TOKEN_NUMBER_INTEGER) {
            /** @var Grammar\IntegerLiteral $child */
            $child = $this->grammar
                ->get('IntegerLiteral', [$token->getValue()])
            ;
            $node->addChild($child);
            $this->nextToken($tokenizer);
            $node->optimize();
        } else if ($token->getType() === TokenizerInterface::TOKEN_NUMBER_FLOATING_POINT) {
            /** @var Grammar\FloatingPointLiteral $child */
            $child = $this->grammar
                ->get('FloatingPointLiteral', [$token->getValue()])
            ;
            $node->addChild($child);
            $this->nextToken($tokenizer);
            $node->optimize();
        } else if ($token->getType() === TokenizerInterface::TOKEN_STRING) {
            /** @var Grammar\StringLiteral $child */
            $child = $this->grammar
                ->get('StringLiteral', [$token->getValue()])
            ;
            $node->addChild($child);
            $this->nextToken($tokenizer);
            $node->optimize();
        } else if ($token->getType() === TokenizerInterface::TOKEN_REGEXP) {
            /** @var Grammar\RegularExpressionLiteral $child */
            $child = $this->grammar
                ->get('RegularExpressionLiteral', [$token->getValue()])
            ;
            $node->addChild($child);
            $this->nextToken($tokenizer);
            $node->optimize();
        } else if ($token->getType() === TokenizerInterface::KEYWORD_FALSE) {
            /** @var Grammar\BooleanLiteral $child */
            $child = $this->grammar
                ->get('BooleanLiteral', [false])
            ;
            $node->addChild($child);
            $this->nextToken($tokenizer);
            $node->optimize();
        } else if ($token->getType() === TokenizerInterface::KEYWORD_TRUE) {
            /** @var Grammar\BooleanLiteral $child */
            $child = $this->grammar
                ->get('BooleanLiteral', [true])
            ;
            $node->addChild($child);
            $this->nextToken($tokenizer);
            $node->optimize();
        } else if ($token->getType() === TokenizerInterface::KEYWORD_THIS) {
            /** @var Grammar\ThisKeyword $child */
            $child = $this->grammar
                ->get('ThisKeyword')
            ;
            $node->addChild($child);
            $this->nextToken($tokenizer);
            $node->optimize();
        } else if ($token->getType() === TokenizerInterface::KEYWORD_NULL) {
            /** @var Grammar\NullKeyword $child */
            $child = $this->grammar
                ->get('NullKeyword')
            ;
            $node->addChild($child);
            $this->nextToken($tokenizer);
            $node->optimize();
        } else if ($token->getType() === TokenizerInterface::KEYWORD_FUNCTION) {
            yield $this->getClosureExpressionRule()->run($node, $tokenizer, $level + 1);
            $node->optimize();
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

    /**
     * @return ClosureExpression
     */
    public function getClosureExpressionRule()
    {
        if ($this->closureExpressionRule === null) {
            $this->closureExpressionRule = $this->rule->get('ClosureExpression');
        }

        return $this->closureExpressionRule;
    }

    /**
     * @return ArrayExpression
     */
    public function getArrayExpressionRule()
    {
        if ($this->arrayExpressionRule === null) {
            $this->arrayExpressionRule = $this->rule->get('ArrayExpression');
        }

        return $this->arrayExpressionRule;
    }

    /**
     * @return ObjectExpression
     */
    public function getObjectExpressionRule()
    {
        if ($this->objectExpressionRule === null) {
            $this->objectExpressionRule = $this->rule->get('ObjectExpression');
        }

        return $this->objectExpressionRule;
    }
}
