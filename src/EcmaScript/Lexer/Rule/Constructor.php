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
 * Class Constructor
 * @package Gplanchat\EcmaScript\Lexer\Rule
 *
 * Constructor:
 *     this . MemberExpression
 *     MemberExpression
 */
class Constructor
    implements RuleInterface
{
    use RuleTrait;

    /**
     * @var MemberExpression
     */
    protected $memberExpressionRule = null;

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

        /** @var Grammar\Constructor $node */
        $node = $this->grammar->get('Constructor');
        $parent->addChild($node);

        if ($token->getType() === TokenizerInterface::KEYWORD_THIS) {
            /** @var Grammar\ThisKeyword $thisKeyword */
            $thisKeyword = $this->grammar
                ->get('ThisKeyword')
            ;
            $node->addChild($thisKeyword);

            $token = $this->nextToken($tokenizer);
            if ($token->getType() === TokenizerInterface::OP_DOT) {
                /** @var Grammar\DotOperator $dotOperator */
                $dotOperator = $this->grammar
                    ->get('DotOperator')
                ;
                $node->addChild($dotOperator);

                $this->nextToken($tokenizer);

                yield $this->getMemberExpressionRule()->run($node, $tokenizer, $level + 1);
            }
        } else {
            yield $this->getMemberExpressionRule()->run($node, $tokenizer, $level + 1);
        }

        $node->optimize();
    }

    /**
     * @return Expression
     */
    public function getMemberExpressionRule()
    {
        if ($this->memberExpressionRule === null) {
            $this->memberExpressionRule = $this->rule->get('MemberExpression');
        }

        return $this->memberExpressionRule;
    }
}
