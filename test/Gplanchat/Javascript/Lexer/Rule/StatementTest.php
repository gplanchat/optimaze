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
 * Statement:
 *     ;
 *     if Condition Statement
 *     if Condition Statement else Statement
 *     while Condition Statement
 *     for ( ; Expression ; Expression ) Statement
 *     for ( VariableListOrExpression ; Expression ; Expression ) Statement
 *     for ( VariableListOrExpression in Expression ) Statement
 *     break ;
 *     continue ;
 *     with ( Expression ) Statement
 *     return Expression ;
 *     { StatementList }
 *     VariableListOrExpression ;
 */
class StatementTest
    extends AbstractRuleTest
{
    /**
     *
     */
    public function testSemicolon()
    {
        $tokens = [
            [TokenizerInterface::OP_SEMICOLON,  ';', null],
            [TokenizerInterface::TOKEN_END,    null, null]
        ];

        $ruleServices = [
        ];

        $grammarServices = [
            ['Statement', Grammar\Statement::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\Statement::class))
        ;

        $rule = new Statement($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
    }

    /**
     *
     */
    public function testIfControlStructure()
    {
        $tokens = [
            [TokenizerInterface::KEYWORD_IF,           'if', null],
            [TokenizerInterface::OP_LEFT_BRACKET,       '(', null],
            [TokenizerInterface::KEYWORD_TRUE,       'true', null],
            [TokenizerInterface::OP_RIGHT_BRACKET,      ')', null],
            [TokenizerInterface::OP_SEMICOLON,          ';', null],
            [TokenizerInterface::TOKEN_END,            null, null]
        ];

        $ruleServices = [
            ['Condition', new Rule\TokenSeeker(TokenizerInterface::OP_RIGHT_BRACKET, ')', true)]
        ];

        $grammarServices = [
            ['Statement',      Grammar\Statement::class],
            ['ConditionChain', Grammar\ConditionChain::class],
            ['IfKeyword',      Grammar\IfKeyword::class],
            ['Statement',      Grammar\Statement::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\Statement::class))
        ;

        $rule = new Statement($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
    }

    /**
     *
     */
    public function testIfControlStructureWithReturnStatement()
    {
        $tokens = [
            [TokenizerInterface::KEYWORD_IF,           'if', null],
            [TokenizerInterface::OP_LEFT_BRACKET,       '(', null],
            [TokenizerInterface::KEYWORD_TRUE,       'true', null],
            [TokenizerInterface::OP_RIGHT_BRACKET,      ')', null],
            [TokenizerInterface::KEYWORD_RETURN,   'return', null],
            [TokenizerInterface::OP_SEMICOLON,          ';', null],
            [TokenizerInterface::TOKEN_END,            null, null]
        ];

        $ruleServices = [
            ['Condition', new Rule\TokenSeeker(TokenizerInterface::OP_RIGHT_BRACKET, ')', true)],
            ['Expression', new Rule\TokenSeeker(TokenizerInterface::OP_SEMICOLON, ';')]
        ];

        $grammarServices = [
            ['Statement',     Grammar\Statement::class],
            ['ConditionChain', Grammar\ConditionChain::class],
            ['IfKeyword',     Grammar\IfKeyword::class],
            ['Statement',     Grammar\Statement::class],
            ['ReturnKeyword', Grammar\ReturnKeyword::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\Statement::class))
        ;

        $rule = new Statement($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
    }

    /**
     *
     */
    public function testIfElseControlStructure()
    {
        $tokens = [
            [TokenizerInterface::KEYWORD_IF,           'if', null],
            [TokenizerInterface::OP_LEFT_BRACKET,       '(', null],
            [TokenizerInterface::KEYWORD_TRUE,       'true', null],
            [TokenizerInterface::OP_RIGHT_BRACKET,      ')', null],
            [TokenizerInterface::OP_SEMICOLON,          ';', null],
            [TokenizerInterface::KEYWORD_ELSE,       'else', null],
            [TokenizerInterface::OP_SEMICOLON,          ';', null],
            [TokenizerInterface::TOKEN_END,            null, null]
        ];

        $ruleServices = [
            ['Condition', new Rule\TokenSeeker(TokenizerInterface::OP_RIGHT_BRACKET, ')', true)]
        ];

        $grammarServices = [
            ['Statement', Grammar\Statement::class],
            ['ConditionChain', Grammar\ConditionChain::class],
            ['IfKeyword', Grammar\IfKeyword::class],
            ['Statement', Grammar\Statement::class],
            ['ElseKeyword', Grammar\ElseKeyword::class],
            ['Statement', Grammar\Statement::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\Statement::class))
        ;

        $rule = new Statement($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
    }

    /**
     *
     */
    public function testIfElseControlStructureWithReturnStatement()
    {
        $tokens = [
            [TokenizerInterface::KEYWORD_IF,           'if', null],
            [TokenizerInterface::OP_LEFT_BRACKET,       '(', null],
            [TokenizerInterface::KEYWORD_TRUE,       'true', null],
            [TokenizerInterface::OP_RIGHT_BRACKET,      ')', null],
            [TokenizerInterface::KEYWORD_RETURN,   'return', null],
            [TokenizerInterface::OP_SEMICOLON,          ';', null],
            [TokenizerInterface::KEYWORD_ELSE,       'else', null],
            [TokenizerInterface::KEYWORD_RETURN,   'return', null],
            [TokenizerInterface::OP_SEMICOLON,          ';', null],
            [TokenizerInterface::TOKEN_END,            null, null]
        ];

        $ruleServices = [
            ['Condition',  new Rule\TokenSeeker(TokenizerInterface::OP_RIGHT_BRACKET, ')', true)],
            ['Expression', new Rule\TokenSeeker(TokenizerInterface::OP_SEMICOLON, ';')]
        ];

        $grammarServices = [
            ['Statement', Grammar\Statement::class],
            ['ConditionChain', Grammar\ConditionChain::class],
            ['IfKeyword', Grammar\IfKeyword::class],
            ['Statement', Grammar\Statement::class],
            ['ReturnKeyword', Grammar\ReturnKeyword::class],
            ['ElseKeyword', Grammar\ElseKeyword::class],
            ['Statement', Grammar\Statement::class],
            ['ReturnKeyword', Grammar\ReturnKeyword::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\Statement::class))
        ;

        $rule = new Statement($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
    }

    /**
     *
     */
    public function testChainedIfElseControlStructure()
    {
        $tokens = [
            [TokenizerInterface::KEYWORD_IF,           'if', null],
            [TokenizerInterface::OP_LEFT_BRACKET,       '(', null],
            [TokenizerInterface::KEYWORD_TRUE,       'true', null],
            [TokenizerInterface::OP_RIGHT_BRACKET,      ')', null],
            [TokenizerInterface::OP_SEMICOLON,          ';', null],
            [TokenizerInterface::KEYWORD_ELSE,       'else', null],
            [TokenizerInterface::KEYWORD_IF,           'if', null],
            [TokenizerInterface::OP_LEFT_BRACKET,       '(', null],
            [TokenizerInterface::KEYWORD_TRUE,       'false', null],
            [TokenizerInterface::OP_RIGHT_BRACKET,      ')', null],
            [TokenizerInterface::OP_SEMICOLON,          ';', null],
            [TokenizerInterface::KEYWORD_ELSE,       'else', null],
            [TokenizerInterface::OP_SEMICOLON,          ';', null],
            [TokenizerInterface::TOKEN_END,            null, null]
        ];

        $ruleServices = [
            ['Condition', new Rule\TokenSeeker(TokenizerInterface::OP_RIGHT_BRACKET, ')', true)]
        ];

        $grammarServices = [
            ['Statement', Grammar\Statement::class],
            ['ConditionChain', Grammar\ConditionChain::class],
            ['IfKeyword', Grammar\IfKeyword::class],
            ['Statement', Grammar\Statement::class],
            ['IfKeyword', Grammar\IfKeyword::class],
            ['Statement', Grammar\Statement::class],
            ['ElseKeyword', Grammar\ElseKeyword::class],
            ['Statement', Grammar\Statement::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\Statement::class))
        ;

        $rule = new Statement($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
    }

    /**
     *
     */
    public function testChainedIfElseControlStructureWithReturnStatement()
    {
        $tokens = [
            [TokenizerInterface::KEYWORD_IF,           'if', null],
            [TokenizerInterface::OP_LEFT_BRACKET,       '(', null],
            [TokenizerInterface::KEYWORD_TRUE,       'true', null],
            [TokenizerInterface::OP_RIGHT_BRACKET,      ')', null],
            [TokenizerInterface::KEYWORD_RETURN,   'return', null],
            [TokenizerInterface::OP_SEMICOLON,          ';', null],
            [TokenizerInterface::KEYWORD_ELSE,       'else', null],
            [TokenizerInterface::KEYWORD_IF,           'if', null],
            [TokenizerInterface::OP_LEFT_BRACKET,       '(', null],
            [TokenizerInterface::KEYWORD_TRUE,       'false', null],
            [TokenizerInterface::OP_RIGHT_BRACKET,      ')', null],
            [TokenizerInterface::KEYWORD_RETURN,   'return', null],
            [TokenizerInterface::OP_SEMICOLON,          ';', null],
            [TokenizerInterface::KEYWORD_ELSE,       'else', null],
            [TokenizerInterface::KEYWORD_RETURN,   'return', null],
            [TokenizerInterface::OP_SEMICOLON,          ';', null],
            [TokenizerInterface::TOKEN_END,            null, null]
        ];

        $ruleServices = [
            ['Condition',  new Rule\TokenSeeker(TokenizerInterface::OP_RIGHT_BRACKET, ')', true)],
            ['Expression', new Rule\TokenSeeker(TokenizerInterface::OP_SEMICOLON, ';')]
        ];

        $grammarServices = [
            ['Statement', Grammar\Statement::class],
            ['ConditionChain', Grammar\ConditionChain::class],
            ['IfKeyword', Grammar\IfKeyword::class],
            ['Statement', Grammar\Statement::class],
            ['ReturnKeyword', Grammar\ReturnKeyword::class],
            ['IfKeyword', Grammar\ElseKeyword::class],
            ['Statement', Grammar\Statement::class],
            ['ReturnKeyword', Grammar\ReturnKeyword::class],
            ['ElseKeyword', Grammar\ElseKeyword::class],
            ['Statement', Grammar\Statement::class],
            ['ReturnKeyword', Grammar\ReturnKeyword::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\Statement::class))
        ;

        $rule = new Statement($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
    }
}
