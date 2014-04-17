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
