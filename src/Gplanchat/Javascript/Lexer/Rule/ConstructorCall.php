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
use Gplanchat\Javascript\Lexer\Rule;
use Gplanchat\Tokenizer\TokenizerInterface as BaseTokenizerInterface;

/**
 * Class Expression
 * @package Gplanchat\Javascript\Lexer\Rule
 *
 * ConstructorCall:
 *     Identifier
 *     Identifier ( ArgumentListOpt )
 *     Identifier . ConstructorCall
 */
class ConstructorCall
    implements RuleInterface
{
    use RuleTrait;

    /**
     * @param RecursiveGrammarInterface $parent
     * @param BaseTokenizerInterface $tokenizer
     * @return void
     * @throws LexicalError
     */
    public function parse(RecursiveGrammarInterface $parent, BaseTokenizerInterface $tokenizer)
    {
        /** @var Grammar\ConstructorCall $node */
        $node = $this->grammar->get('ConstructorCall');
        $parent->addChild($node);
        echo $parent->dump();

        $token = $this->currentToken($tokenizer);
        while (true) {
            if ($token->getType() !== TokenizerInterface::TOKEN_IDENTIFIER) {
                throw new LexicalError('Invalid expression : missing identifier',
                    null, $token->getLine(), $token->getStart());
            }
            /** @var Grammar\Identifier $identifier */
            $identifier = $this->grammar->get('Identifier', [$token->getValue()]);
            $node->addChild($identifier);

            $token = $this->nextToken($tokenizer);
            if ($token->getType() === TokenizerInterface::OP_LEFT_BRACKET) {
                $this->nextToken($tokenizer);

                /** @var Rule\ArgumentList $argumentListRule */
                $argumentListRule = $this->rule->get('ArgumentList');
                $argumentListRule->parse($node, $tokenizer);

                $token = $this->currentToken($tokenizer);
                if ($token->getType() !== TokenizerInterface::OP_RIGHT_BRACKET) {
                    throw new LexicalError('Invalid expression : missing right bracket',
                        null, $token->getLine(), $token->getStart());
                }

                $this->nextToken($tokenizer);
            } else if ($token->getType() === TokenizerInterface::OP_DOT) {
                /** @var Grammar\DotOperator $dotOperator */
                $dotOperator = $this->grammar->get('DotOperator');
                $node->addChild($dotOperator);
                $token = $this->nextToken($tokenizer);
                continue;
            }
            break;
        }
    }
}
