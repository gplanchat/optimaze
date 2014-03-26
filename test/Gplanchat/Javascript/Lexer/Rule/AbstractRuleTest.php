<?php
/**
 * Created by PhpStorm.
 * User: Greg
 * Date: 22/03/14
 * Time: 20:02
 */

namespace Gplanchat\Javascript\Lexer\Rule;

use Gplanchat\Javascript\Lexer\Grammar\RecursiveGrammarInterface;
use Gplanchat\Javascript\Tokenizer\Tokenizer;
use Gplanchat\Tokenizer\Token;
use Gplanchat\ServiceManager\ServiceManagerInterface;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_MockObject_MockBuilder as MockBuilder;
use Gplanchat\Javascript\Lexer\Rule;
use Gplanchat\Javascript\Lexer\Grammar;

abstract class AbstractRuleTest
    extends \PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    protected $defaultRuleServiceList = [
            'AdditiveExpression'       => Rule\AdditiveExpression::class,
            'AndExpression'            => Rule\AndExpression::class,
            'ArgumentList'             => Rule\ArgumentList::class,
            'AssignmentExpression'     => Rule\AssignmentExpression::class,
            'BitwiseAndExpression'     => Rule\BitwiseAndExpression::class,
            'BitwiseOrExpression'      => Rule\BitwiseOrExpression::class,
            'BitwiseXorExpression'     => Rule\BitwiseXorExpression::class,
            'ConditionalExpression'    => Rule\ConditionalExpression::class,
            'Constructor'              => Rule\Constructor::class,
            'ConstructorCall'          => Rule\ConstructorCall::class,
            'EqualityExpression'       => Rule\EqualityExpression::class,
            'Expression'               => Rule\Expression::class,
            'MemberExpression'         => Rule\MemberExpression::class,
            'MultiplicativeExpression' => Rule\MultiplicativeExpression::class,
            'OrExpression'             => Rule\OrExpression::class,
            'PrimaryExpression'        => Rule\PrimaryExpression::class,
            'RelationalExpression'     => Rule\RelationalExpression::class,
            'ShiftExpression'          => Rule\ShiftExpression::class,
            'UnaryExpression'          => Rule\UnaryExpression::class,
        ];

    /**
     * @var array
     */
    protected $defaultGrammarServiceList = [
            'AdditiveExpression'       => Grammar\AdditiveExpression::class,
            'AdditiveOperator'         => Grammar\AdditiveOperator::class,
            'AndExpression'            => Grammar\AndExpression::class,
            'ArgumentList'             => Grammar\ArgumentList::class,
            'AssignmentOperator'       => Grammar\AssignmentOperator::class,
            'BitwiseAndExpression'     => Grammar\BitwiseAndExpression::class,
            'BitwiseOrExpression'      => Grammar\BitwiseOrExpression::class,
            'BitwiseXorExpression'     => Grammar\BitwiseXorExpression::class,
            'BooleanLiteral'           => Grammar\BooleanLiteral::class,
            'CommaOperator'            => Grammar\CommaOperator::class,
            'ConditionalExpression'    => Grammar\ConditionalExpression::class,
            'Constructor'              => Grammar\Constructor::class,
            'ConstructorCall'          => Grammar\ConstructorCall::class,
            'DeleteKeyword'            => Grammar\DeleteKeyword::class,
            'DotOperator'              => Grammar\DotOperator::class,
            'EqualityExpression'       => Grammar\EqualityExpression::class,
            'EqualityOperator'         => Grammar\EqualityOperator::class,
            'Expression'               => Grammar\Expression::class,
            'FloatingPointLiteral'     => Grammar\FloatingPointLiteral::class,
            'Identifier'               => Grammar\Identifier::class,
            'IncrementOperator'        => Grammar\IncrementOperator::class,
            'IntegerLiteral'           => Grammar\IntegerLiteral::class,
            'MemberExpression'         => Grammar\MemberExpression::class,
            'MultiplicativeExpression' => Grammar\MultiplicativeExpression::class,
            'MultiplicativeOperator'   => Grammar\MultiplicativeOperator::class,
            'NewKeyword'               => Grammar\NewKeyword::class,
            'NullKeyword'              => Grammar\NullKeyword::class,
            'OrExpression'             => Grammar\OrExpression::class,
            'PrimaryExpression'        => Grammar\PrimaryExpression::class,
            'RelationalExpression'     => Grammar\RelationalExpression::class,
            'RelationalOperator'       => Grammar\RelationalOperator::class,
            'ShiftExpression'          => Grammar\ShiftExpression::class,
            'ShiftOperator'            => Grammar\ShiftOperator::class,
            'StringLiteral'            => Grammar\StringLiteral::class,
            'ThisKeyword'              => Grammar\ThisKeyword::class,
            'UnaryExpression'          => Grammar\UnaryExpression::class,
            'UnaryOperator'            => Grammar\UnaryOperator::class,
        ];

    /**
     * @param array $tokenStream
     * @return Tokenizer|MockObject
     */
    public function getTokenizerMock(array $tokenStream)
    {
        /** @var Tokenizer|MockObject $serviceManager */
        $tokenizer = $this->getMock(Tokenizer::class, ['current', 'next', 'key', 'valid', 'rewind'], [], '', false);

        $iterator = new \ArrayIterator();
        foreach ($tokenStream as $tokenData) {
            $iterator->append($this->getTokenMock($tokenData));
        }

        $tokenizer->expects($this->any())
            ->method('current')
            ->will($this->returnCallback([$iterator, 'current']))
        ;
        $tokenizer->expects($this->any())
            ->method('next')
            ->will($this->returnCallback([$iterator, 'next']))
        ;
        $tokenizer->expects($this->any())
            ->method('key')
            ->will($this->returnCallback([$iterator, 'key']))
        ;
        $tokenizer->expects($this->any())
            ->method('valid')
            ->will($this->returnCallback([$iterator, 'valid']))
        ;
        $tokenizer->expects($this->any())
            ->method('rewind')
            ->will($this->returnCallback([$iterator, 'rewind']))
        ;

        return $tokenizer;
    }

    /**
     * @param array $constructorParams
     * @return Token|MockObject
     */
    protected function getTokenMock(array $constructorParams)
    {
        return $this->getMock(Token::class, [], $constructorParams, '', true, true, true, false, true);
    }

    /**
     * @param array $serviceList
     * @return ServiceManagerInterface|MockObject
     */
    protected function getRuleServiceManagerMock(array $serviceList = [])
    {
        /** @var \Gplanchat\ServiceManager\ServiceManagerInterface|\PHPUnit_Framework_MockObject_MockObject $serviceManager */
        $serviceManager = $this->getMockForAbstractClass('Gplanchat\ServiceManager\ServiceManagerInterface', ['get']);

        $mockObjectGenerator = $this->getMockObjectGenerator();
        foreach ($serviceList as $callId => list($serviceName, $serviceClass)) {
            $serviceManager->expects($this->at($callId))
                ->method('get')
                ->with($serviceName)
                ->will($this->returnCallback(function($serviceName, array $constructorParams = [], $ignoreInexistent = false, $ignorePeering = false)
                    use ($serviceClass, $mockObjectGenerator) {

                    return $mockObjectGenerator->getMock(
                        $serviceClass,
                        [],
                        $constructorParams
                    );
                }))
            ;
        }

        return $serviceManager;
    }

    /**
     * @param array $serviceList
     * @return ServiceManagerInterface|MockObject
     */
    protected function getGrammarServiceManagerMock(array $serviceList = [])
    {
        /** @var \Gplanchat\ServiceManager\ServiceManagerInterface|\PHPUnit_Framework_MockObject_MockObject $serviceManager */
        $serviceManager = $this->getMockForAbstractClass('Gplanchat\ServiceManager\ServiceManagerInterface', ['get']);

        $mockObjectGenerator = $this->getMockObjectGenerator();
        foreach ($serviceList as $callId => list($serviceName, $serviceClass)) {
            $serviceManager->expects($this->at($callId))
                ->method('get')
                ->with($serviceName)
                ->will($this->returnCallback(function($serviceName, array $constructorParams = [], $ignoreInexistent = false, $ignorePeering = false)
                        use ($serviceClass, $mockObjectGenerator) {

                    return $mockObjectGenerator->getMock(
                        $serviceClass,
                        [],
                        $constructorParams
                    );
                }))
            ;
        }

        return $serviceManager;
    }

    /**
     * @return MockObject|RecursiveGrammarInterface
     */
    protected function getRootGrammarMock()
    {
        return $this->getMockForAbstractClass(RecursiveGrammarInterface::class, [], '', false);
    }
}
