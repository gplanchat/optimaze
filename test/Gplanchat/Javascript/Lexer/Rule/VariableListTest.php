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
use Gplanchat\Javascript\Lexer\Grammar;
use Gplanchat\Javascript\Lexer\Rule;
use Gplanchat\Javascript\Tokenizer\TokenizerInterface;

/**
 * VariableList:
 *     Variable
 *     Variable , VariableList
 *
 * Variable:
 *     Identifier
 *     Identifier = AssignmentExpression
 */
class VariableListTest
    extends AbstractRuleTest
{
    /**
     *
     */
    public function testOneVariableWithoutAssignment()
    {
        $tokens = [
            [TokenizerInterface::TOKEN_IDENTIFIER,   'a', null],
            [TokenizerInterface::OP_SEMICOLON,       ';', null],
            [TokenizerInterface::TOKEN_END,         null, null]
        ];

        $ruleServices = [
        ];

        $grammarServices = [
            ['VariableList', Grammar\VariableList::class],
            ['Variable', Grammar\Variable::class],
            ['Identifier', Grammar\Identifier::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\VariableList::class))
        ;

        $rule = new VariableList($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
    }

    /**
     *
     */
    public function testMultipleVariableWithoutAssignment()
    {
        $tokens = [
            [TokenizerInterface::TOKEN_IDENTIFIER,   'a', null],
            [TokenizerInterface::OP_COMMA,           ',', null],
            [TokenizerInterface::TOKEN_IDENTIFIER,   'b', null],
            [TokenizerInterface::OP_COMMA,           ',', null],
            [TokenizerInterface::TOKEN_IDENTIFIER,   'c', null],
            [TokenizerInterface::OP_SEMICOLON,       ';', null],
            [TokenizerInterface::TOKEN_END,         null, null]
        ];

        $ruleServices = [
        ];

        $grammarServices = [
            ['VariableList', Grammar\VariableList::class],
            ['Variable', Grammar\Variable::class],
            ['Identifier', Grammar\Identifier::class],
            ['Variable', Grammar\Variable::class],
            ['Identifier', Grammar\Identifier::class],
            ['Variable', Grammar\Variable::class],
            ['Identifier', Grammar\Identifier::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\VariableList::class))
        ;

        $rule = new VariableList($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
    }

    /**
     *
     */
    public function testOneVariableWithAssignment()
    {
        $tokens = [
            [TokenizerInterface::TOKEN_IDENTIFIER,      'a', null],
            [TokenizerInterface::OP_EQ,                 '=', null],
            [TokenizerInterface::TOKEN_NUMBER_INTEGER,  '1', null],
            [TokenizerInterface::OP_SEMICOLON,          ';', null],
            [TokenizerInterface::TOKEN_END,            null, null]
        ];

        $ruleServices = [
            ['AssignmentExpression', new TokenSeeker(TokenizerInterface::TOKEN_NUMBER_INTEGER, '1', true)]
        ];

        $grammarServices = [
            ['VariableList', Grammar\VariableList::class],
            ['Variable', Grammar\Variable::class],
            ['Identifier', Grammar\Identifier::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\VariableList::class))
        ;

        $rule = new VariableList($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
    }

    /**
     *
     */
    public function testMultipleVariableWithAssignment()
    {
        $tokens = [
            [TokenizerInterface::TOKEN_IDENTIFIER,      'a', null],
            [TokenizerInterface::OP_EQ,                 '=', null],
            [TokenizerInterface::TOKEN_NUMBER_INTEGER,  '1', null],
            [TokenizerInterface::OP_COMMA,              ',', null],
            [TokenizerInterface::TOKEN_IDENTIFIER,      'b', null],
            [TokenizerInterface::OP_SEMICOLON,          ';', null],
            [TokenizerInterface::TOKEN_END,            null, null]
        ];

        $ruleServices = [
            ['AssignmentExpression', new TokenSeeker(TokenizerInterface::TOKEN_NUMBER_INTEGER, '1', true)]
        ];

        $grammarServices = [
            ['VariableList', Grammar\VariableList::class],
            ['Variable', Grammar\Variable::class],
            ['Identifier', Grammar\Identifier::class],
            ['Variable', Grammar\Variable::class],
            ['Identifier', Grammar\Identifier::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\VariableList::class))
        ;

        $rule = new VariableList($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
    }

    /**
     *
     */
    public function testOneVariableWithoutIdentifier()
    {
        $this->setExpectedException(Exception\LexicalError::class, 'Invalid expression : missing identifier');

        $tokens = [
            [TokenizerInterface::OP_SEMICOLON,  ';', null],
            [TokenizerInterface::TOKEN_END,    null, null]
        ];

        $ruleServices = [
        ];

        $grammarServices = [
            ['VariableList', Grammar\VariableList::class],
            ['Variable', Grammar\Variable::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\VariableList::class))
        ;

        $rule = new VariableList($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
    }
}
