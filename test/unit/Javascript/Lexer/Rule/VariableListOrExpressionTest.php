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

use Gplanchat\Javascript\Lexer\Accumulator;
use Gplanchat\Javascript\Lexer\Exception;
use Gplanchat\Lexer\Grammar;
use Gplanchat\Javascript\Lexer\Rule;
use Gplanchat\Javascript\Tokenizer\TokenizerInterface;

/**
 * VariableListOrExpression:
 *     var VariableList
 *     Expression
 */
class VariableListOrExpressionTest
    extends AbstractRuleTest
{
    /**
     *
     */
    public function testVariableList()
    {
        $tokens = [
            [TokenizerInterface::KEYWORD_VAR,      'var', null],
            [TokenizerInterface::TOKEN_IDENTIFIER,   'a', null],
            [TokenizerInterface::OP_SEMICOLON,       ';', null],
            [TokenizerInterface::TOKEN_END,         null, null]
        ];

        $ruleServices = [
            ['VariableList', new Rule\TokenSeeker(TokenizerInterface::OP_SEMICOLON, ';', true)]
        ];

        $grammarServices = [
        ];

        $root = $this->getRootGrammarMock();

        $rule = new VariableListOrExpression($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $accumulator = new Accumulator($rule, $root);
        $accumulator($this->getTokenizerMock($tokens));
    }

    /**
     *
     */
    public function testExpression()
    {
        $tokens = [
            [TokenizerInterface::TOKEN_IDENTIFIER,   'a', null],
            [TokenizerInterface::OP_SEMICOLON,       ';', null],
            [TokenizerInterface::TOKEN_END,         null, null]
        ];

        $ruleServices = [
            ['Expression', new Rule\TokenSeeker(TokenizerInterface::OP_SEMICOLON, ';', true)]
        ];

        $grammarServices = [
        ];

        $root = $this->getRootGrammarMock();

        $rule = new VariableListOrExpression($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $accumulator = new Accumulator($rule, $root);
        $accumulator($this->getTokenizerMock($tokens));
    }
}
