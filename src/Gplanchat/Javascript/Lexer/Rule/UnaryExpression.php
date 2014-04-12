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

/**
 * Class Expression
 * @package Gplanchat\Javascript\Lexer\Rule
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

    protected $unaryOperators = [
        TokenizerInterface::OP_BITWISE_NOT,
        TokenizerInterface::KEYWORD_DELETE,
        TokenizerInterface::KEYWORD_TYPEOF,
        TokenizerInterface::KEYWORD_VOID,
        TokenizerInterface::OP_MINUS
    ];

    protected $incrementOperators = [
        TokenizerInterface::OP_INCREMENT,
        TokenizerInterface::OP_DECREMENT
    ];

    protected $primaryExpressionTokens = [
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
     * @param TokenizerInterface $tokenizer
     * @return void
     * @throws LexicalError
     */
    public function parse(RecursiveGrammarInterface $parent, TokenizerInterface $tokenizer)
    {
        $token = $this->currentToken($tokenizer);

        /** @var Grammar\UnaryExpression $node */
        $node = $this->grammar->get('UnaryExpression');
        $parent->addChild($node);
//        echo $parent->dump();

        /** @var MemberExpression $memberExpressionRule */
        $memberExpressionRule = $this->rule->get('MemberExpression');;

        while (true) {
//            var_dump($token->getType(), !in_array($token->getType(), $this->unaryOperators));
            if (in_array($token->getType(), $this->unaryOperators)) {
                /** @var Grammar\UnaryOperator $unaryOperator */
                $unaryOperator = $this->grammar
                    ->get('UnaryOperator', [$token->getValue()])
                ;
                $node->addChild($unaryOperator);
                $token = $this->nextToken($tokenizer);
                continue;
            }

            if (in_array($token->getType(), $this->primaryExpressionTokens)) {
                $memberExpressionRule->parse($node, $tokenizer);

                $token = $this->currentToken($tokenizer);
                if (in_array($token->getType(), $this->incrementOperators)) {
                    /** @var Grammar\IncrementOperator $incrementOperator */
                    $incrementOperator = $this->grammar
                        ->get('IncrementOperator', [$token->getValue()])
                    ;
                    $node->addChild($incrementOperator);
                }
                break;
            } else if (in_array($token->getType(), $this->incrementOperators)) {
                /** @var Grammar\IncrementOperator $incrementOperator */
                $incrementOperator = $this->grammar
                    ->get('IncrementOperator', [$token->getValue()])
                ;
                $node->addChild($incrementOperator);

                $this->nextToken($tokenizer);
                $memberExpressionRule->parse($node, $tokenizer);
                break;
            } else if ($token->getType() === TokenizerInterface::KEYWORD_DELETE) {
                /** @var Grammar\DeleteKeyword $deleteKeyword */
                $deleteKeyword = $this->grammar
                    ->get('DeleteKeyword', [$token->getValue()])
                ;
                $node->addChild($deleteKeyword);

                $this->nextToken($tokenizer);
                $memberExpressionRule->parse($node, $tokenizer);
                return;
            } else if ($token->getType() === TokenizerInterface::KEYWORD_NEW) {
                /** @var Grammar\NewKeyword $newKeyword */
                $newKeyword = $this->grammar
                    ->get('NewKeyword', [$token->getValue()])
                ;
                $node->addChild($newKeyword);

                /** @var Constructor $constructorRule */
                $constructorRule = $this->rule->get('Constructor');;

                $this->nextToken($tokenizer);
                $constructorRule->parse($node, $tokenizer);
                break;
            }
        }

        $node->optimize();
    }
}
