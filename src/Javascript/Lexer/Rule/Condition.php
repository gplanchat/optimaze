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
 * Condition:
 *     ( Expression )
 */
class Condition
    implements RuleInterface
{
    use RuleTrait;

    /**
     * @param RecursiveGrammarInterface $parent
     * @param BaseTokenizerInterface $tokenizer
     * @return void
     * @throws LexicalError
     */
    public function __invoke(RecursiveGrammarInterface $parent, BaseTokenizerInterface $tokenizer)
    {
        /** @var Grammar\Condition $node */
        $node = $this->grammar->get('Condition');
        $parent->addChild($node);

        $token = $this->currentToken($tokenizer);
        if ($token->getType() !== TokenizerInterface::OP_LEFT_BRACKET) {
            throw new LexicalError(RuleInterface::MESSAGE_MISSING_LEFT_BRACKET,
                null, $token->getLine(), $token->getLineOffset(), $token->getStart());
        }

        $this->nextToken($tokenizer);

        /** @var Expression $expressionRule */
        $expressionRule = $this->rule->get('Expression');
        yield $expressionRule($node, $tokenizer);

        $token = $this->currentToken($tokenizer);
        if ($token->getType() !== TokenizerInterface::OP_RIGHT_BRACKET) {
            throw new LexicalError(RuleInterface::MESSAGE_MISSING_RIGHT_BRACKET,
                null, $token->getLine(), $token->getLineOffset(), $token->getStart());
        }

        $this->nextToken($tokenizer);

        $node->optimize();
    }
}
