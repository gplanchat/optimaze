<?php

namespace Gplanchat\Lexer\Grammar;

use Gplanchat\ServiceManager\ServiceManagerInterface;
use Gplanchat\ServiceManager\ServiceManagerTrait;

class ServiceManager
    implements ServiceManagerInterface
{
    use ServiceManagerTrait;

    public function __construct()
    {
        $this->invokables = [
            'AccessorExpression'       => AccessorExpression::class,
            'AdditiveExpression'       => AdditiveExpression::class,
            'AdditiveOperator'         => AdditiveOperator::class,
            'AndExpression'            => AndExpression::class,
            'ArgumentList'             => ArgumentList::class,
            'ArrayExpression'          => ArrayExpression::class,
            'AssignmentExpression'     => AssignmentExpression::class,
            'AssignmentOperator'       => AssignmentOperator::class,
            'BitwiseAndExpression'     => BitwiseAndExpression::class,
            'BitwiseOrExpression'      => BitwiseOrExpression::class,
            'BitwiseXorExpression'     => BitwiseXorExpression::class,
            'BlockComment'             => BlockComment::class,
            'BooleanLiteral'           => BooleanLiteral::class,
            'BreakKeyword'             => BreakKeyword::class,
            'CaseKeyword'              => CaseKeyword::class,
            'ClosureExpression'        => ClosureExpression::class,
            'CommaOperator'            => CommaOperator::class,
            'CompoundStatement'        => CompoundStatement::class,
            'Condition'                => Condition::class,
            'ConditionalExpression'    => ConditionalExpression::class,
            'ConditionChain'           => ConditionChain::class,
            'Constructor'              => Constructor::class,
            'ConstructorCall'          => ConstructorCall::class,
            'ContinueKeyword'          => ContinueKeyword::class,
            'DefaultKeyword'           => DefaultKeyword::class,
            'DeleteKeyword'            => DeleteKeyword::class,
            'DocComment'               => DocComment::class,
            'DotOperator'              => DotOperator::class,
            'Element'                  => Element::class,
            'ElseKeyword'              => ElseKeyword::class,
            'EqualityExpression'       => EqualityExpression::class,
            'EqualityOperator'         => EqualityOperator::class,
            'Expression'               => Expression::class,
            'FloatingPointLiteral'     => FloatingPointLiteral::class,
            'ForKeyword'               => ForKeyword::class,
            'FunctionExpression'       => FunctionExpression::class,
            'Identifier'               => Identifier::class,
            'IfKeyword'                => IfKeyword::class,
            'IncrementOperator'        => IncrementOperator::class,
            'IntegerLiteral'           => IntegerLiteral::class,
            'LineComment'              => LineComment::class,
            'MemberExpression'         => MemberExpression::class,
            'MultiplicativeExpression' => MultiplicativeExpression::class,
            'MultiplicativeOperator'   => MultiplicativeOperator::class,
            'MutatorExpression'        => MutatorExpression::class,
            'NewKeyword'               => NewKeyword::class,
            'NullKeyword'              => NullKeyword::class,
            'ObjectEntry'              => ObjectEntry::class,
            'ObjectExpression'         => ObjectExpression::class,
            'OrExpression'             => OrExpression::class,
            'ParameterList'            => ParameterList::class,
            'PrimaryExpression'        => PrimaryExpression::class,
            'Program'                  => Program::class,
            'RegularExpressionLiteral' => RegularExpressionLiteral::class,
            'RelationalExpression'     => RelationalExpression::class,
            'RelationalOperator'       => RelationalOperator::class,
            'ReturnKeyword'            => ReturnKeyword::class,
            'ShiftExpression'          => ShiftExpression::class,
            'ShiftOperator'            => ShiftOperator::class,
            'Statement'                => Statement::class,
            'StatementList'            => StatementList::class,
            'StringLiteral'            => StringLiteral::class,
            'SwitchKeyword'            => SwitchKeyword::class,
            'SwitchStatement'          => SwitchStatement::class,
            'ThisKeyword'              => ThisKeyword::class,
            'ThrowKeyword'             => ThrowKeyword::class,
            'UnaryExpression'          => UnaryExpression::class,
            'UnaryOperator'            => UnaryOperator::class,
            'Variable'                 => Variable::class,
            'VariableList'             => VariableList::class,
            'WhileKeyword'             => WhileKeyword::class,
            'WithKeyword'              => WithKeyword::class,
        ];
    }
}
