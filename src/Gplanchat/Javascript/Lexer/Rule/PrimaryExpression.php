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

use Gplanchat\Javascript\Tokenizer\TokenizerInterface;
use Gplanchat\Javascript\Lexer\Exception\LexicalError;
use Gplanchat\Lexer\Grammar;
use Gplanchat\Lexer\Grammar\RecursiveGrammarInterface;
use Gplanchat\Tokenizer\TokenizerInterface as BaseTokenizerInterface;

/**
 * Class PrimaryExpression
 * @package Gplanchat\Javascript\Lexer\Rule
 *
 * PrimaryExpression:
 *     ( Expression )
 *     Identifier
 *     IntegerLiteral
 *     FloatingPointLiteral
 *     StringLiteral
 *     false
 *     true
 *     null
 *     this
 */
class PrimaryExpression
    implements RuleInterface
{
    use RuleTrait;

    protected static $validTokenTypes = [
        TokenizerInterface::OP_LEFT_BRACKET,
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
     * @param RecursiveGrammarInterface $parent
     * @param BaseTokenizerInterface $tokenizer
     * @return void
     * @throws LexicalError
     */
    public function parse(RecursiveGrammarInterface $parent, BaseTokenizerInterface $tokenizer)
    {
        $token = $this->currentToken($tokenizer);
        if (!in_array($token->getType(), static::$validTokenTypes)) {
            return;
        }

        /** @var Grammar\PrimaryExpression $node */
        $node = $this->grammar->get('PrimaryExpression');
        $parent->addChild($node);
//        echo $parent->dump();

        if ($token->getType() === TokenizerInterface::OP_LEFT_BRACKET) {
            $this->nextToken($tokenizer);

            /** @var Expression $rule */
            $rule = $this->rule->get('Expression');
            $rule->parse($node, $tokenizer);

            $token = $this->currentToken($tokenizer);
            if ($token->getType() !== TokenizerInterface::OP_RIGHT_BRACKET) {
                throw new LexicalError(static::MESSAGE_MISSING_RIGHT_BRACKET,
                    null, $token->getLine(), $token->getStart());
            }
        } else if ($token->getType() === TokenizerInterface::TOKEN_IDENTIFIER) {
            /** @var Grammar\Identifier $child */
            $child = $this->grammar
                ->get('Identifier', [$token->getValue()])
            ;
            $node->addChild($child);
        } else if ($token->getType() === TokenizerInterface::TOKEN_NUMBER_INTEGER) {
            /** @var Grammar\IntegerLiteral $child */
            $child = $this->grammar
                ->get('IntegerLiteral', [$token->getValue()])
            ;
            $node->addChild($child);
        } else if ($token->getType() === TokenizerInterface::TOKEN_NUMBER_FLOATING_POINT) {
            /** @var Grammar\FloatingPointLiteral $child */
            $child = $this->grammar
                ->get('FloatingPointLiteral', [$token->getValue()])
            ;
            $node->addChild($child);
        } else if ($token->getType() === TokenizerInterface::TOKEN_STRING) {
            /** @var Grammar\StringLiteral $child */
            $child = $this->grammar
                ->get('StringLiteral', [$token->getValue()])
            ;
            $node->addChild($child);
        } else if ($token->getType() === TokenizerInterface::KEYWORD_FALSE) {
            /** @var Grammar\BooleanLiteral $child */
            $child = $this->grammar
                ->get('BooleanLiteral', [false])
            ;
            $node->addChild($child);
        } else if ($token->getType() === TokenizerInterface::KEYWORD_TRUE) {
            /** @var Grammar\BooleanLiteral $child */
            $child = $this->grammar
                ->get('BooleanLiteral', [true])
            ;
            $node->addChild($child);
        } else if ($token->getType() === TokenizerInterface::KEYWORD_THIS) {
            /** @var Grammar\ThisKeyword $child */
            $child = $this->grammar
                ->get('ThisKeyword')
            ;
            $node->addChild($child);
        } else if ($token->getType() === TokenizerInterface::KEYWORD_NULL) {
            /** @var Grammar\NullKeyword $child */
            $child = $this->grammar
                ->get('NullKeyword')
            ;
            $node->addChild($child);
        } else {
            throw new LexicalError(static::MESSAGE_UNEXPECTED_TOKEN,
                null, $token->getLine(), $token->getStart());
        }

        $this->nextToken($tokenizer);

        $node->optimize();
    }
}
