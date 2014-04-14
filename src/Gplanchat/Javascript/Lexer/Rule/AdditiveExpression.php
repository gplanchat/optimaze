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
 * AdditiveExpression:
 *     MultiplicativeExpression
 *     MultiplicativeExpression + AdditiveExpression
 *     MultiplicativeExpression - AdditiveExpression
 */
class AdditiveExpression
    implements RuleInterface
{
    use RuleTrait;

    protected $additiveOperators = [
        TokenizerInterface::OP_PLUS,
        TokenizerInterface::OP_MINUS
    ];

    /**
     * @param RecursiveGrammarInterface $parent
     * @param BaseTokenizerInterface $tokenizer
     * @return void
     * @throws LexicalError
     */
    public function parse(RecursiveGrammarInterface $parent, BaseTokenizerInterface $tokenizer)
    {
        /** @var Grammar\AdditiveExpression $node */
        $node = $this->grammar->get('AdditiveExpression');
        $parent->addChild($node);
//        echo $parent->dump();

        /** @var MultiplicativeExpression $multiplicativeExpressionRule */
        $multiplicativeExpressionRule = $this->rule->get('MultiplicativeExpression');

        while (true) {
            $multiplicativeExpressionRule->parse($node, $tokenizer);

            $token = $this->currentToken($tokenizer);
            if (!in_array($token->getType(), $this->additiveOperators)) {
                break;
            }

            /** @var Grammar\AdditiveOperator $additiveOperator */
            $additiveOperator = $this->grammar
                ->get('AdditiveOperator', [$token->getValue()])
            ;
            $node->addChild($additiveOperator);
            $this->nextToken($tokenizer);
        }

        $node->optimize();
    }
}
