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

use Gplanchat\Javascript\Lexer\Exception;
use Gplanchat\Lexer\Grammar;
use Gplanchat\Javascript\Lexer\Rule;
use Gplanchat\Javascript\Tokenizer\TokenizerInterface;

/**
 * ParameterList:
 *     empty
 *     Identifier
 *     Identifier , ParameterList
 */
class ParameterListTest
    extends AbstractRuleTest
{
    /**
     *
     */
    public function testEmptyParameterList()
    {
        $tokens = [
            [TokenizerInterface::OP_RIGHT_BRACKET,  ')', null],
            [TokenizerInterface::TOKEN_END,        null, null]
        ];

        $ruleServices = [
        ];

        $grammarServices = [
            ['ParameterList', Grammar\ParameterList::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\ParameterList::class))
        ;

        $rule = new ParameterList($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
    }

    /**
     *
     */
    public function testParameterListWithOneParameter()
    {
        $tokens = [
            [TokenizerInterface::TOKEN_IDENTIFIER,  'a', null],
            [TokenizerInterface::OP_RIGHT_BRACKET,  ')', null],
            [TokenizerInterface::TOKEN_END,        null, null]
        ];

        $ruleServices = [
        ];

        $grammarServices = [
            ['ParameterList', Grammar\ParameterList::class],
            ['Identifier',    Grammar\Identifier::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\ParameterList::class))
        ;

        $rule = new ParameterList($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
    }

    /**
     *
     */
    public function testParameterListWithMultipleParameters()
    {
        $tokens = [
            [TokenizerInterface::TOKEN_IDENTIFIER,  'a', null],
            [TokenizerInterface::OP_COMMA,          ',', null],
            [TokenizerInterface::TOKEN_IDENTIFIER,  'b', null],
            [TokenizerInterface::OP_RIGHT_BRACKET,  ')', null],
            [TokenizerInterface::TOKEN_END,        null, null]
        ];

        $ruleServices = [
        ];

        $grammarServices = [
            ['ParameterList', Grammar\ParameterList::class],
            ['Identifier',    Grammar\Identifier::class],
            ['Identifier',    Grammar\Identifier::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\ParameterList::class))
        ;

        $rule = new ParameterList($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
    }
}
