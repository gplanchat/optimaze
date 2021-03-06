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

use Gplanchat\ServiceManager\ServiceManagerInterface;
use PHPUnit_Framework_TestCase as TestCase;

class GrammarFactoryMock
{
    /**
     * @var TestCase
     */
    protected $testCase = null;

    /**
     * @var string
     */
    protected $className = null;

    /**
     * @var RuleInterface
     */
    protected $singletonInstance = null;

    /**
     * @param TestCase $testCase
     * @param string $className
     */
    public function __construct(TestCase $testCase, $className)
    {
        $this->setTestCase($testCase);
        $this->setClassName($className);
    }

    /**
     * @param string $serviceName
     * @param array $constructorParams
     * @param bool $ignoreInexistent
     * @param bool $ignorePeering
     * @return RuleInterface
     */
    public function __invoke($serviceName, array $constructorParams = [], $ignoreInexistent = false, $ignorePeering = false)
    {
        return $this->getTestCase()
            ->getMock($this->getClassName(), ['parse'], $constructorParams);
    }

    /**
     * @param string $className
     * @return $this
     */
    public function setClassName($className)
    {
        $this->className = $className;

        return $this;
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * @param TestCase $testCase
     * @return $this
     */
    public function setTestCase(TestCase $testCase)
    {
        $this->testCase = $testCase;

        return $this;
    }

    /**
     * @return TestCase
     */
    public function getTestCase()
    {
        return $this->testCase;
    }
}
