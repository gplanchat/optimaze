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
 * @package Gplanchat\Tokenizer
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
     * @param array $tokenStream
     * @return Tokenizer|MockObject
     */
    public function getTokenizerMock(array $tokenStream)
    {
        /** @var Tokenizer|MockObject $serviceManager */
        $tokenizer = $this->getMock(Tokenizer::class, ['current', 'next', 'key', 'valid', 'rewind'], [], '', false);

        $iterator = new \ArrayIterator();
        $startOffset = 0;
        $endOffset = 0;
        foreach ($tokenStream as list($tokenType, $tokenValue, $assignOperator)) {
            $endOffset += strlen($tokenValue);
            $iterator->append($this->getTokenMock([$tokenType, $tokenValue, $startOffset, $endOffset, 1, $assignOperator]));
            $startOffset = $endOffset;
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
        $re = new \ReflectionClass(Token::class);
        return $re->newInstanceArgs($constructorParams);

//        return $this->getMock(Token::class, [], $constructorParams, '', true, true, true, false, true);
    }

    /**
     * @param array $serviceList
     * @return ServiceManagerInterface|MockObject
     */
    protected function getRuleServiceManagerMock(array $serviceList = [])
    {
        /** @var ServiceManagerInterface|MockObject $serviceManager */
        $serviceManager = $this->getMockForAbstractClass('Gplanchat\ServiceManager\ServiceManagerInterface', ['get']);
        /** @var ServiceManagerInterface|MockObject $grammarServiceManager */
        $grammarServiceManager = $this->getGrammarServiceManagerMock();

        foreach ($serviceList as $callId => list($serviceName, $service)) {
            if (is_string($service)) {
                $factory = new RuleFactoryMock($this, $service, $serviceManager, $grammarServiceManager);

                $serviceManager->expects($this->at($callId))
                    ->method('get')
                    ->with($serviceName)
                    ->will($this->returnCallback($factory))
                ;
            } else {
                $serviceManager->expects($this->at($callId))
                    ->method('get')
                    ->with($serviceName)
                    ->will($this->returnValue($service))
                ;
            }
        }

        return $serviceManager;
    }

    /**
     * @param array $serviceList
     * @return ServiceManagerInterface|MockObject
     */
    protected function getGrammarServiceManagerMock(array $serviceList = [])
    {
        /** @var ServiceManagerInterface|MockObject $serviceManager */
        $serviceManager = $this->getMockForAbstractClass('Gplanchat\ServiceManager\ServiceManagerInterface', ['get']);

        foreach ($serviceList as $callId => list($serviceName, $service)) {
            if (is_string($service)) {
                $factory = new GrammarFactoryMock($this, $service);

                $serviceManager->expects($this->at($callId))
                    ->method('get')
                    ->with($serviceName)
                    ->will($this->returnCallback($factory))
                ;
            } else {
                $serviceManager->expects($this->at($callId))
                    ->method('get')
                    ->with($serviceName)
                    ->will($this->returnValue($service))
                ;
            }
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
