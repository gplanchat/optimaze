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
 * Class Constructor
 * @package Gplanchat\Javascript\Lexer\Rule
 *
 * Constructor:
 *     this . ConstructorCall
 *     ConstructorCall
 */
class Constructor
    implements RuleInterface
{
    use RuleTrait;

    /**
     * @param RecursiveGrammarInterface $parent
     * @param TokenizerInterface $tokenizer
     * @return void
     * @throws LexicalError
     */
    public function parse(RecursiveGrammarInterface $parent, TokenizerInterface $tokenizer)
    {
        $token = $this->currentToken($tokenizer);

        /** @var Grammar\Constructor $node */
        $node = $this->grammar->get('Constructor');
        $parent->addChild($node);
//        echo $parent->dump();

        if ($token->getType() === TokenizerInterface::KEYWORD_THIS) {
            /** @var Grammar\ThisKeyword $thisKeyword */
            $thisKeyword = $this->grammar
                ->get('ThisKeyword')
            ;
            $node->addChild($thisKeyword);

            $token = $this->nextToken($tokenizer);
            if ($token->getType() !== TokenizerInterface::OP_DOT) {
                throw new LexicalError('Invalid expression : missing constructor call',
                    null, $token->getLine(), $token->getStart());
            }

            /** @var Grammar\DotOperator $dotOperator */
            $dotOperator = $this->grammar
                ->get('DotOperator')
            ;
            $node->addChild($dotOperator);

            $this->nextToken($tokenizer);
        }

        /** @var ConstructorCall $rule */
        $rule = $this->rule->get('ConstructorCall');;
        $rule->parse($node, $tokenizer);
    }
}
