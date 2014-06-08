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
 * EqualityExpression:
 *     RelationalExpression
 *     RelationalExpression EqualityOperator EqualityExpression
 */
class EqualityExpression
    implements RuleInterface
{
    use RuleTrait;

    /**
     * @var RelationalExpression
     */
    protected $relationalExpressionRule = null;

    /**
     * @var array
     */
    protected static $equalityOperators = [
        TokenizerInterface::OP_STRICT_EQ,
        TokenizerInterface::OP_EQ,
        TokenizerInterface::OP_STRICT_NE,
        TokenizerInterface::OP_NE,
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
        /** @var Grammar\EqualityExpression $node */
        $node = $this->grammar->get('EqualityExpression');
        $parent->addChild($node);

        while (true) {
            yield $this->getRelationalExpressionRule()->run($node, $tokenizer, $level + 1);

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

    /**
     * @return RelationalExpression
     */
    public function getRelationalExpressionRule()
    {
        if ($this->relationalExpressionRule === null) {
            $this->relationalExpressionRule = $this->rule->get('RelationalExpression');
        }

        return $this->relationalExpressionRule;
    }
}
