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
use Gplanchat\Tokenizer\TokenizerInterface as BaseTokenizerInterface;

/**
 * Class MultiplicativeExpression
 * @package Gplanchat\Javascript\Lexer\Rule
 *
 * MultiplicativeExpression:
 *     UnaryExpression
 *     UnaryExpression MultiplicativeOperator MultiplicativeExpression
 */
class MultiplicativeExpression
    implements RuleInterface
{
    use RuleTrait;

    protected static $multiplicativeOperators = [
        TokenizerInterface::OP_MUL,
        TokenizerInterface::OP_DIV,
        TokenizerInterface::OP_MOD
    ];

    /**
     * @param RecursiveGrammarInterface $parent
     * @param BaseTokenizerInterface $tokenizer
     * @return void
     * @throws LexicalError
     */
    public function parse(RecursiveGrammarInterface $parent, BaseTokenizerInterface $tokenizer)
    {
        /** @var Grammar\MultiplicativeExpression $node */
        $node = $this->grammar->get('MultiplicativeExpression');
        $parent->addChild($node);

        /** @var UnaryExpression $unaryExpressionRule */
        $unaryExpressionRule = $this->rule->get('UnaryExpression');

        while (true) {
            $unaryExpressionRule->parse($node, $tokenizer);

            $token = $this->currentToken($tokenizer);
            if (!in_array($token->getType(), static::$multiplicativeOperators)) {
                break;
            }

            /** @var Grammar\MultiplicativeOperator $multiplicativeOperator */
            $multiplicativeOperator = $this->grammar
                ->get('MultiplicativeOperator', [$token->getValue()])
            ;
            $node->addChild($multiplicativeOperator);
            $this->nextToken($tokenizer);
        }

        $node->optimize();
    }
}
