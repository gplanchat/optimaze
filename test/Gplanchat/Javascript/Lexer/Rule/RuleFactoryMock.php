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

class RuleFactoryMock
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
     * @var ServiceManagerInterface
     */
    protected $ruleServiceManager = null;

    /**
     * @var ServiceManagerInterface
     */
    protected $grammarServiceManager = null;

    /**
     * @var RuleInterface
     */
    protected $singletonInstance = null;

    /**
     * @param TestCase $testCase
     * @param string $className
     * @param ServiceManagerInterface $grammarServiceManager
     */
    public function __construct(TestCase $testCase, $className, ServiceManagerInterface $ruleServiceManager, ServiceManagerInterface $grammarServiceManager)
    {
        $this->setTestCase($testCase);
        $this->setClassName($className);
        $this->setRuleServiceManager($ruleServiceManager);
        $this->setGrammarServiceManager($grammarServiceManager);
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
            ->getMock($this->getClassName(), ['parse'], [$this->getRuleServiceManager(), $this->getGrammarServiceManager()]);
    }

    /**
     * @param ServiceManagerInterface $ruleServiceManager
     * @return $this
     */
    public function setRuleServiceManager(ServiceManagerInterface $ruleServiceManager)
    {
        $this->ruleServiceManager = $ruleServiceManager;

        return $this;
    }

    /**
     * @return \Gplanchat\ServiceManager\ServiceManagerInterface
     */
    public function getRuleServiceManager()
    {
        return $this->ruleServiceManager;
    }

    /**
     * @param ServiceManagerInterface $grammarServiceManager
     * @return $this
     */
    public function setGrammarServiceManager($grammarServiceManager)
    {
        $this->grammarServiceManager = $grammarServiceManager;

        return $this;
    }

    /**
     * @return ServiceManagerInterface
     */
    public function getGrammarServiceManager()
    {
        return $this->grammarServiceManager;
    }

    /**
     * @param RuleInterface $singletonInstance
     * @return $this
     */
    public function setSingletonInstance(RuleInterface $singletonInstance)
    {
        $this->singletonInstance = $singletonInstance;

        return $this;
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
