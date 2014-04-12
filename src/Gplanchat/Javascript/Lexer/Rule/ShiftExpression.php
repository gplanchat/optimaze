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
 * ShiftExpression:
 *     AdditiveExpression
 *     AdditiveExpression ShiftOperator ShiftExpression
 */
class ShiftExpression
    implements RuleInterface
{
    use RuleTrait;

    protected $shiftOperators = [
        TokenizerInterface::OP_RSH,
        TokenizerInterface::OP_LSH,
        TokenizerInterface::OP_URSH
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

        /** @var Grammar\ShiftExpression $node */
        $node = $this->grammar->get('ShiftExpression');
        $parent->addChild($node);
//        echo $parent->dump();

        /** @var AdditiveExpression $additiveExpressionRule */
        $additiveExpressionRule = $this->rule->get('AdditiveExpression');;

        while (true) {
            $additiveExpressionRule->parse($node, $tokenizer);

            $token = $this->currentToken($tokenizer);
            if (!in_array($token->getType(), $this->shiftOperators)) {
                break;
            }

            /** @var Grammar\ShiftOperator $shiftOperator */
            $shiftOperator = $this->grammar
                ->get('ShiftOperator', [$token->getValue()])
            ;
            $node->addChild($shiftOperator);
            $this->nextToken($tokenizer);
        }

        $node->flatten();
    }
}
