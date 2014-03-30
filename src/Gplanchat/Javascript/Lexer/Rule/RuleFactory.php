<?php
/**
 * Created by PhpStorm.
 * User: Greg
 * Date: 30/03/14
 * Time: 11:45
 */

namespace Gplanchat\Javascript\Lexer\Rule;

use Gplanchat\ServiceManager\ServiceManagerInterface;

class RuleFactory
{
    /**
     * @var string
     */
    protected $className = null;

    /**
     * @var ServiceManagerInterface
     */
    protected $grammarServiceManager = null;

    /**
     * @var RuleInterface
     */
    protected $singletonInstance = null;

    /**
     * @param string $className
     * @param ServiceManagerInterface $grammarServiceManager
     */
    public function __construct($className, ServiceManagerInterface $grammarServiceManager)
    {
        $this->setClassName($className);
        $this->setGrammarServiceManager($grammarServiceManager);
    }

    /**
     * @param ServiceManagerInterface $ruleServiceManager
     * @param array $additionalParameters
     * @return mixed
     */
    public function __invoke(ServiceManagerInterface $ruleServiceManager, array $additionalParameters)
    {
        if (($instance = $this->getSingletonInstance()) === null) {
            $this->setSingletonInstance($ruleServiceManager->get($this->getClassName(),
                    [$ruleServiceManager, $this->getGrammarServiceManager()]));

            $instance = $this->getSingletonInstance();
        }

        return $instance;
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
     * @return RuleInterface
     */
    public function getSingletonInstance()
    {
        return $this->singletonInstance;
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
}
