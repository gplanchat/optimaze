<?php

namespace Gplanchat\Javascript\Lexer\Rule;

use Gplanchat\ServiceManager\ServiceManagerInterface;
use Gplanchat\ServiceManager\ServiceManagerTrait;

class ServiceManager
    implements ServiceManagerInterface
{
    use ServiceManagerTrait {
        ServiceManagerTrait::get as realGet;
    }

    /**
     * @var ServiceManagerInterface
     */
    protected $grammarServiceManager = null;

    /**
     * @param ServiceManagerInterface $grammarServiceManager
     */
    public function __construct(ServiceManagerInterface $grammarServiceManager)
    {
        $this->setGrammarServiceManager($grammarServiceManager);

        $services = [
            'AdditiveExpression'       => AdditiveExpression::class,
            'AndExpression'            => AndExpression::class,
            'ArgumentList'             => ArgumentList::class,
            'AssignmentExpression'     => AssignmentExpression::class,
            'BitwiseAndExpression'     => BitwiseAndExpression::class,
            'BitwiseOrExpression'      => BitwiseOrExpression::class,
            'BitwiseXorExpression'     => BitwiseXorExpression::class,
            'Condition'                => Condition::class,
            'ConditionalExpression'    => ConditionalExpression::class,
            'Constructor'              => Constructor::class,
            'ConstructorCall'          => ConstructorCall::class,
            'Element'                  => Element::class,
            'EqualityExpression'       => EqualityExpression::class,
            'Expression'               => Expression::class,
            'MemberExpression'         => MemberExpression::class,
            'MultiplicativeExpression' => MultiplicativeExpression::class,
            'OrExpression'             => OrExpression::class,
            'ParameterList'            => ParameterList::class,
            'PrimaryExpression'        => PrimaryExpression::class,
            'RelationalExpression'     => RelationalExpression::class,
            'ShiftExpression'          => ShiftExpression::class,
            'Statement'                => Statement::class,
            'StatementList'            => StatementList::class,
            'UnaryExpression'          => UnaryExpression::class,
            'VariableList'             => VariableList::class,
            'VariableListOrExpression' => VariableListOrExpression::class
        ];

        foreach ($services as $serviceName => $className) {
            $this->registerInvokable($className, $className);
            $this->registerFactory($serviceName, $this->buildFactory($className, $this->getGrammarServiceManager()));
        }
    }

    /**
     * @param $className
     * @param ServiceManagerInterface $serviceManager
     * @return callable
     */
    protected function buildFactory($className, ServiceManagerInterface $serviceManager)
    {
        return new RuleFactory($className, $serviceManager);
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

    public function get($serviceName, array $constructorParams = [], $ignoreInexistent = false, $ignorePeering = false)
    {
//        var_dump(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2));
//        var_dump($serviceName);
        return $this->realGet($serviceName, $constructorParams, $ignoreInexistent, $ignorePeering);
    }
}
