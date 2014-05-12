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
 * Class ObjectExpression
 * @package Gplanchat\Javascript\Lexer\Rule
 *
 * ObjectExpression:
 *     { empty }
 *     { ObjectEntryList }
 *
 * ObjectEntry:
 *     Identifier : Expression
 *
 * ObjectEntryList:
 *     ObjectEntry
 *     ObjectEntry , ObjectEntryList
 */
class ObjectExpression
    implements RuleInterface
{
    use RuleTrait;

    /**
     * @var Expression
     */
    protected $expressionRule = null;

    /**
     * @param RecursiveGrammarInterface $parent
     * @param BaseTokenizerInterface $tokenizer
     * @return \Generator|null
     * @throws LexicalError
     */
    public function run(RecursiveGrammarInterface $parent, BaseTokenizerInterface $tokenizer)
    {
        /** @var Grammar\ObjectExpression $node */
        $node = $this->grammar->get('ObjectExpression');
        $parent->addChild($node);

        $token = $this->currentToken($tokenizer);
        if ($token->getType() !== TokenizerInterface::OP_LEFT_CURLY) {
            throw new LexicalError(RuleInterface::MESSAGE_MISSING_LEFT_CURLY_BRACE,
                $token->getPath(), $token->getLine(), $token->getLineOffset(), $token->getStart());
        }
        $token = $this->nextToken($tokenizer);

        if ($token->getType() === TokenizerInterface::OP_RIGHT_CURLY) {
            $this->nextToken($tokenizer);
        } else {
            while (true) {
                if ($token->getType() !== TokenizerInterface::TOKEN_IDENTIFIER) {
                    throw new LexicalError(RuleInterface::MESSAGE_MISSING_RIGHT_CURLY_BRACE,
                        $token->getPath(), $token->getLine(), $token->getLineOffset(), $token->getStart());
                }

                /** @var Grammar\ObjectEntry $objectEntry */
                $objectEntry = $this->grammar->get('ObjectEntry', [$token->getValue()]);
                $node->addChild($objectEntry);

                if ($token->getType() !== TokenizerInterface::OP_COLON) {
                    throw new LexicalError(RuleInterface::MESSAGE_MISSING_COLON,
                        $token->getPath(), $token->getLine(), $token->getLineOffset(), $token->getStart());
                }

                $this->nextToken($tokenizer);
                yield $this->getExpressionRule()->run($objectEntry, $tokenizer);

                $token = $this->currentToken($tokenizer);
                if ($token->getType() !== TokenizerInterface::OP_COMMA) {
                    break;
                }
                $token = $this->nextToken($tokenizer);
            }

            if ($token->getType() !== TokenizerInterface::OP_RIGHT_CURLY) {
                throw new LexicalError(RuleInterface::MESSAGE_MISSING_RIGHT_CURLY_BRACE,
                    $token->getPath(), $token->getLine(), $token->getLineOffset(), $token->getStart());
            }
            $this->nextToken($tokenizer);
        }

        $node->optimize();
    }

    /**
     * @return Expression
     */
    public function getExpressionRule()
    {
        if ($this->expressionRule === null) {
            $this->expressionRule = $this->rule->get('Expression');
        }

        return $this->expressionRule;
    }
}
