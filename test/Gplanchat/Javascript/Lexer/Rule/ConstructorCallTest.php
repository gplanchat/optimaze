<?php
/**
 * Created by PhpStorm.
 * User: Greg
 * Date: 22/03/14
 * Time: 20:02
 */

namespace Gplanchat\Javascript\Lexer\Rule;

use Gplanchat\Javascript\Lexer\Exception;
use Gplanchat\Javascript\Lexer\Grammar;
use Gplanchat\Javascript\Lexer\Rule;
use Gplanchat\Javascript\Tokenizer\TokenizerInterface;

/**
 * ConstructorCall:
 *     Identifier
 *     Identifier ( ArgumentListOpt )
 *     Identifier . ConstructorCall
 */
class ConstructorCallTest
    extends AbstractRuleTest
{
    /**
     *
     */
    public function testLoneIdentifier()
    {
        $tokens = [
            [TokenizerInterface::TOKEN_IDENTIFIER, 'identifier', null],
            [TokenizerInterface::TOKEN_END,                null, null]
        ];

        $ruleServices = [];

        $grammarServices = [
            ['ConstructorCall', Grammar\ConstructorCall::class],
            ['Identifier',      Grammar\Identifier::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\ConstructorCall::class))
        ;

        $rule = new ConstructorCall($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
    }

    /**
     *
     */
    public function testMultipleDottedIdentifiers()
    {
        $tokens = [
            [TokenizerInterface::TOKEN_IDENTIFIER, 'identifier1', null],
            [TokenizerInterface::OP_DOT,                     '.', null],
            [TokenizerInterface::TOKEN_IDENTIFIER, 'identifier2', null],
            [TokenizerInterface::TOKEN_END,                 null, null]
        ];

        $ruleServices = [];

        $grammarServices = [
            ['ConstructorCall', Grammar\ConstructorCall::class],
            ['Identifier',      Grammar\Identifier::class],
            ['DotOperator',     Grammar\DotOperator::class],
            ['Identifier',      Grammar\Identifier::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\ConstructorCall::class))
        ;

        $rule = new ConstructorCall($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
    }


    /**
     *
     */
    public function testIdentifierWithOptions()
    {
        $tokens = [
            [TokenizerInterface::TOKEN_IDENTIFIER, 'identifier', null],
            [TokenizerInterface::OP_LEFT_BRACKET,           '(', null],
            [TokenizerInterface::OP_RIGHT_BRACKET,          ')', null],
            [TokenizerInterface::TOKEN_END,                null, null]
        ];

        $ruleServices = [
            ['ArgumentList',    Rule\ArgumentList::class]
        ];

        $grammarServices = [
            ['ConstructorCall', Grammar\ConstructorCall::class],
            ['Identifier',      Grammar\Identifier::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\ConstructorCall::class))
        ;

        $rule = new ConstructorCall($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
    }

    /**
     *
     */
    public function testInvalidTokenMissingIdentifier()
    {
        $this->setExpectedException(Exception\LexicalError::class, 'Invalid expression : missing identifier');

        $tokens = [
            [TokenizerInterface::OP_LEFT_BRACKET,  '(', null],
            [TokenizerInterface::OP_RIGHT_BRACKET, ')', null]
        ];

        $ruleServices = [];

        $grammarServices = [
            ['ConstructorCall', Grammar\ConstructorCall::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\ConstructorCall::class))
        ;

        $rule = new ConstructorCall($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
    }

    /**
     *
     */
    public function testInvalidTokenMissingRightBracket()
    {
        $this->setExpectedException(Exception\LexicalError::class, 'Invalid expression : missing right bracket');

        $tokens = [
            [TokenizerInterface::TOKEN_IDENTIFIER, 'identifier', null],
            [TokenizerInterface::OP_LEFT_BRACKET,           '(', null],
            [TokenizerInterface::TOKEN_END,                null, null]
        ];

        $ruleServices = [
            ['ArgumentList',    Rule\ArgumentList::class]
        ];

        $grammarServices = [
            ['ConstructorCall', Grammar\ConstructorCall::class],
            ['Identifier',      Grammar\Identifier::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\ConstructorCall::class))
        ;

        $rule = new ConstructorCall($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
    }
}
