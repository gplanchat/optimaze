<?php
/**
 * Created by PhpStorm.
 * User: Greg
 * Date: 12/04/14
 * Time: 00:21
 */

namespace Gplanchat\Javascript\Lexer\Rule;

use Gplanchat\Javascript\Lexer\Exception;
use Gplanchat\Javascript\Lexer\Grammar;
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
            [TokenizerInterface::OP_RIGHT_BRACKET, ')', null]
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
            [TokenizerInterface::TOKEN_IDENTIFIER, 'a', null],
            [TokenizerInterface::OP_RIGHT_BRACKET, ')', null]
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
            [TokenizerInterface::TOKEN_IDENTIFIER, 'a', null],
            [TokenizerInterface::OP_COMMA,         ',', null],
            [TokenizerInterface::TOKEN_IDENTIFIER, 'b', null],
            [TokenizerInterface::OP_RIGHT_BRACKET, ')', null]
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
