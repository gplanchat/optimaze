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

use Gplanchat\Javascript\Lexer\Debug;
use Gplanchat\Javascript\Lexer\Exception\LexicalError;
use Gplanchat\Lexer\Grammar\RecursiveGrammarInterface;
use Gplanchat\Javascript\Tokenizer\TokenizerInterface;
use Gplanchat\Lexer\Grammar;
use Gplanchat\Javascript\Lexer\Rule;
use Gplanchat\Tokenizer\TokenizerInterface as BaseTokenizerInterface;

/**
 * Class Element
 * @package Gplanchat\Javascript\Lexer\Rule
 *
 * Element:
 *     function Identifier ( empty ) { StatementList }
 *     function Identifier ( ParameterList ) { StatementList }
 *     Statement
 */
class Element
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
        /** @var Grammar\Element $node */
        $node = $this->grammar->get('Element');
        $parent->addChild($node);

        $token = $this->currentToken($tokenizer);

        if ($token->getType() === TokenizerInterface::KEYWORD_FUNCTION) {
            /** @var Grammar\FunctionKeyword $functionKeyword */
            $functionKeyword = $this->grammar->get('FunctionKeyword');
            $node->addChild($functionKeyword);

            $token = $this->nextToken($tokenizer);
            if ($token->getType() === TokenizerInterface::TOKEN_IDENTIFIER) {
                /** @var Grammar\Identifier $identifier */
                $identifier = $this->grammar->get('Identifier', [$token->getValue()]);
                $functionKeyword->addChild($identifier);

                $token = $this->nextToken($tokenizer);
            }

            if ($token->getType() !== TokenizerInterface::OP_LEFT_BRACKET) {
                throw new LexicalError(static::MESSAGE_MISSING_LEFT_BRACKET,
                    null, $token->getLine(), $token->getStart());
            }
            $this->nextToken($tokenizer);

            /** @var Rule\ParameterList $parameterListRule */
            $parameterListRule = $this->rule->get('ParameterList');
            $parameterListRule->parse($functionKeyword, $tokenizer);

            $token = $this->currentToken($tokenizer);
            if ($token->getType() !== TokenizerInterface::OP_RIGHT_BRACKET) {
                throw new LexicalError(static::MESSAGE_MISSING_RIGHT_BRACKET,
                    null, $token->getLine(), $token->getStart());
            }
            $token = $this->nextToken($tokenizer);

            if ($token->getType() !== TokenizerInterface::OP_LEFT_CURLY) {
                throw new LexicalError(static::MESSAGE_MISSING_LEFT_CURLY_BRACE,
                    null, $token->getLine(), $token->getStart());
            }
            $this->nextToken($tokenizer);


            /** @var Rule\StatementList $statementListRule */
            $statementListRule = $this->rule->get('StatementList');
            $statementListRule->parse($functionKeyword, $tokenizer);

            $token = $this->currentToken($tokenizer);
            if ($token->getType() !== TokenizerInterface::OP_RIGHT_CURLY) {
                throw new LexicalError(static::MESSAGE_MISSING_RIGHT_CURLY_BRACE,
                    null, $token->getLine(), $token->getStart());
            }
            $this->nextToken($tokenizer);
        } else {
            /** @var Rule\Statement $statementRule */
            $statementRule = $this->rule->get('Statement');
            $statementRule->parse($node, $tokenizer);
        }

        $node->optimize();
    }
}
