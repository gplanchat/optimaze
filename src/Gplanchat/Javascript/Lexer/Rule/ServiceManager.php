<?php

namespace Gplanchat\Javascript\Lexer\Rule;

use Gplanchat\ServiceManager\ServiceManagerInterface;
use Gplanchat\ServiceManager\ServiceManagerTrait;

class ServiceManager
    implements ServiceManagerInterface
{
    use ServiceManagerTrait;

    /**
     * @var array
     */
    protected $invokables = [
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
}
