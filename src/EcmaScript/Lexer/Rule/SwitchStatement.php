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

use Gplanchat\Lexer\Grammar;
use Gplanchat\Lexer\Grammar\RecursiveGrammarInterface;
use Gplanchat\EcmaScript\Lexer\Exception\LexicalError;
use Gplanchat\EcmaScript\Tokenizer\TokenizerInterface;
use Gplanchat\Tokenizer\TokenizerInterface as BaseTokenizerInterface;

/**
 * Class MemberExpression
 * @package Gplanchat\EcmaScript\Lexer\Rule
 *
 * SwitchStatement:
 *     SwitchCase : StatementList SwitchStatement
 *
 * SwitchCase:
 *     case StringLiteral
 *     case IntegerLiteral
 *     case FloatingPointLiteral
 *     case Identifier
 *     default
 */
class SwitchStatement
    implements RuleInterface
{
    use RuleTrait;

    /**
     * @var null
     */
    protected $switchCaseRule = null;

    /**
     * @var StatementList
     */
    protected $statementListRule = null;

    /**
     * @param RecursiveGrammarInterface $parent
     * @param BaseTokenizerInterface $tokenizer
     * @param int $level
     * @return \Generator|null
     * @throws LexicalError
     */
    public function run(RecursiveGrammarInterface $parent, BaseTokenizerInterface $tokenizer, $level = 0)
    {
        /** @var Grammar\SwitchStatement $node */
        $node = $this->grammar->get('SwitchStatement');
        $parent->addChild($node);

        while (true) {
            $token = $this->currentToken($tokenizer);
            if ($token->getType() === TokenizerInterface::OP_RIGHT_CURLY) {
                break;
            }

            yield $this->getSwitchCaseRule()->run($node, $tokenizer, $level + 1);

            if ($node->count() <= 0) {
                throw new LexicalError(static::MESSAGE_UNEXPECTED_TOKEN,
                    $token->getPath(), $token->getLine(), $token->getLineOffset(), $token->getStart());
            }

            yield $this->getStatementListRule()->run($node, $tokenizer, $level + 1);
        }

        $node->optimize();
    }

    /**
     * @return SwitchCase
     */
    public function getSwitchCaseRule()
    {
        if ($this->switchCaseRule === null) {
            $this->switchCaseRule = $this->rule->get('SwitchCase');
        }

        return $this->switchCaseRule;
    }

    /**
     * @return StatementList
     */
    public function getStatementListRule()
    {
        if ($this->statementListRule === null) {
            $this->statementListRule = $this->rule->get('StatementList');
        }

        return $this->statementListRule;
    }
}
