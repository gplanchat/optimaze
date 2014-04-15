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
 * Class Expression
 * @package Gplanchat\Javascript\Lexer\Rule
 *
 * EqualityExpression:
 *     RelationalExpression
 *     RelationalExpression EqualityOperator EqualityExpression
 */
class EqualityExpression
    implements RuleInterface
{
    use RuleTrait;

    protected static $equalityOperators = [
        TokenizerInterface::OP_STRICT_EQ,
        TokenizerInterface::OP_EQ,
        TokenizerInterface::OP_STRICT_NE,
        TokenizerInterface::OP_NE,
    ];

    /**
     * @param RecursiveGrammarInterface $parent
     * @param BaseTokenizerInterface $tokenizer
     * @return void
     * @throws LexicalError
     */
    public function parse(RecursiveGrammarInterface $parent, BaseTokenizerInterface $tokenizer)
    {
        /** @var Grammar\EqualityExpression $node */
        $node = $this->grammar->get('EqualityExpression');
        $parent->addChild($node);

        /** @var RelationalExpression $relationalExpressionRule */
        $relationalExpressionRule = $this->rule->get('RelationalExpression');

        while (true) {
            $relationalExpressionRule->parse($node, $tokenizer);

            $token = $this->currentToken($tokenizer);
            if (!in_array($token->getType(), static::$equalityOperators)) {
                break;
            }

            /** @var Grammar\EqualityOperator $equalityOperator */
            $equalityOperator = $this->grammar
                ->get('EqualityOperator', [$token->getAssignOperator()])
            ;
            $node->addChild($equalityOperator);
            $this->nextToken($tokenizer);
        }

        $node->optimize();
    }
}
