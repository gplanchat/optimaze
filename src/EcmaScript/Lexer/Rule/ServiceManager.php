<?php

namespace Gplanchat\EcmaScript\Lexer\Rule;

use Gplanchat\ServiceManager\ServiceManagerInterface;
use Gplanchat\ServiceManager\ServiceManagerTrait;

class ServiceManager
    implements ServiceManagerInterface
{
    use ServiceManagerTrait;

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
            'ArrayExpression'          => ArrayExpression::class,
            'AssignmentExpression'     => AssignmentExpression::class,
            'BitwiseAndExpression'     => BitwiseAndExpression::class,
            'BitwiseOrExpression'      => BitwiseOrExpression::class,
            'BitwiseXorExpression'     => BitwiseXorExpression::class,
            'ClosureExpression'        => ClosureExpression::class,
            'Condition'                => Condition::class,
            'ConditionalExpression'    => ConditionalExpression::class,
            'Constructor'              => Constructor::class,
            'Element'                  => Element::class,
            'EqualityExpression'       => EqualityExpression::class,
            'Expression'               => Expression::class,
            'ForExpression'            => ForExpression::class,
            'FunctionExpression'       => FunctionExpression::class,
            'IfExpression'             => IfExpression::class,
            'MemberExpression'         => MemberExpression::class,
            'MultiplicativeExpression' => MultiplicativeExpression::class,
            'ObjectEntry'              => ObjectEntry::class,
            'ObjectExpression'         => ObjectExpression::class,
            'OrExpression'             => OrExpression::class,
            'ParameterList'            => ParameterList::class,
            'PrimaryExpression'        => PrimaryExpression::class,
            'RelationalExpression'     => RelationalExpression::class,
            'ShiftExpression'          => ShiftExpression::class,
            'SwitchStatement'          => SwitchStatement::class,
            'SwitchCase'               => SwitchCase::class,
            'SpecialObjectEntry'       => SpecialObjectEntry::class,
            'Statement'                => Statement::class,
            'StatementList'            => StatementList::class,
            'UnaryExpression'          => UnaryExpression::class,
            'VariableList'             => VariableList::class,
            'VariableListOrExpression' => VariableListOrExpression::class,
            'WhileExpression'          => WhileExpression::class
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
}
