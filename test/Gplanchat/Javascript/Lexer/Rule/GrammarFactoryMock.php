<?php
/**
 * Created by PhpStorm.
 * User: Greg
 * Date: 30/03/14
 * Time: 11:45
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
     * @param ServiceManagerInterface $grammarServiceManager
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
