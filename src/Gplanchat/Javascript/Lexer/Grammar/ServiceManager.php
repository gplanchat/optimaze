<?php

namespace Gplanchat\Javascript\Lexer\Grammar;

use Gplanchat\ServiceManager\ServiceManagerInterface;
use Gplanchat\ServiceManager\ServiceManagerTrait;

class ServiceManager
    implements ServiceManagerInterface
{
    use ServiceManagerTrait;

    public function __construct()
    {
        $this->invokables = [
            'AdditiveExpression'       => AdditiveExpression::class,
            'AdditiveOperator'         => AdditiveOperator::class,
            'AndExpression'            => AndExpression::class,
            'ArgumentList'             => ArgumentList::class,
            'AssignmentOperator'       => AssignmentOperator::class,
            'BitwiseAndExpression'     => BitwiseAndExpression::class,
            'BitwiseOrExpression'      => BitwiseOrExpression::class,
            'BitwiseXorExpression'     => BitwiseXorExpression::class,
            'BooleanLiteral'           => BooleanLiteral::class,
            'BreakKeyword'             => BreakKeyword::class,
            'CommaOperator'            => CommaOperator::class,
            'CompoundStatement'        => CompoundStatement::class,
            'Condition'                => Condition::class,
            'ConditionalExpression'    => ConditionalExpression::class,
            'Constructor'              => Constructor::class,
            'ConstructorCall'          => ConstructorCall::class,
            'ContinueKeyword'          => ContinueKeyword::class,
            'DeleteKeyword'            => DeleteKeyword::class,
            'DotOperator'              => DotOperator::class,
            'ElseKeyword'              => ElseKeyword::class,
            'EqualityExpression'       => EqualityExpression::class,
            'EqualityOperator'         => EqualityOperator::class,
            'Expression'               => Expression::class,
            'FloatingPointLiteral'     => FloatingPointLiteral::class,
            'ForKeyword'               => ForKeyword::class,
            'FunctionKeyword'          => FunctionKeyword::class,
            'Identifier'               => Identifier::class,
            'IfKeyword'                => IfKeyword::class,
            'IncrementOperator'        => IncrementOperator::class,
            'IntegerLiteral'           => IntegerLiteral::class,
            'MemberExpression'         => MemberExpression::class,
            'MultiplicativeExpression' => MultiplicativeExpression::class,
            'MultiplicativeOperator'   => MultiplicativeOperator::class,
            'NewKeyword'               => NewKeyword::class,
            'NullKeyword'              => NullKeyword::class,
            'OrExpression'             => OrExpression::class,
            'ParameterList'            => ParameterList::class,
            'PrimaryExpression'        => PrimaryExpression::class,
            'Program'                  => Program::class,
            'RelationalExpression'     => RelationalExpression::class,
            'RelationalOperator'       => RelationalOperator::class,
            'ReturnKeyword'            => ReturnKeyword::class,
            'ShiftExpression'          => ShiftExpression::class,
            'ShiftOperator'            => ShiftOperator::class,
            'Statement'                => Statement::class,
            'StatementList'            => StatementList::class,
            'StringLiteral'            => StringLiteral::class,
            'ThisKeyword'              => ThisKeyword::class,
            'UnaryExpression'          => UnaryExpression::class,
            'UnaryOperator'            => UnaryOperator::class,
            'Variable'                 => Variable::class,
            'VariableList'             => VariableList::class,
            'WhileKeyword'             => WhileKeyword::class,
            'WithKeyword'              => WithKeyword::class,
        ];
    }
}
