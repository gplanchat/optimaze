Program:
    empty
    Element Program

Element:
    function Identifier ( empty ) { StatementList }
    function Identifier ( ParameterList ) { StatementList }
    Statement

ParameterList:
    Identifier
    Identifier , ParameterList

StatementList:
    empty
    Statement StatementList

Statement:
    ;
    if Condition Statement
    if Condition Statement else Statement
    while Condition Statement
    for ( ; Expression ; Expression ) Statement
    for ( VariableListOrExpression ; Expression ; Expression ) Statement
    for ( VariableListOrExpression in Expression ) Statement
    break ;
    continue ;
    with ( Expression ) Statement
    return Expression ;
    { StatementList }
    VariableListOrExpression ;

Condition:
    ( Expression )

VariableListOrExpression:
    var VariableList
    Expression

VariableList:
    Variable
    Variable , VariableList

Variable:
    Identifier
    Identifier = AssignmentExpression

Expression:
    AssignmentExpression
    AssignmentExpression , Expression

AssignmentExpression:
    ConditionalExpression
    ConditionalExpression AssignmentOperator AssignmentExpression

ConditionalExpression:
    OrExpression
    OrExpression ? AssignmentExpression : AssignmentExpression

OrExpression:
    AndExpression
    AndExpression || OrExpression

AndExpression:
    BitwiseOrExpression
    BitwiseOrExpression && AndExpression

BitwiseOrExpression:
    BitwiseXorExpression
    BitwiseXorExpression | BitwiseOrExpression

BitwiseXorExpression:
    BitwiseAndExpression
    BitwiseAndExpression ^ BitwiseXorExpression

BitwiseAndExpression:
    EqualityExpression
    EqualityExpression & BitwiseAndExpression

EqualityExpression:
    RelationalExpression
    RelationalExpression EqualityualityOperator EqualityExpression

RelationalExpression:
    ShiftExpression
    RelationalExpression RelationalationalOperator ShiftExpression

ShiftExpression:
    AdditiveExpression
    AdditiveExpression ShiftOperator ShiftExpression

AdditiveExpression:
    MultiplicativeExpression
    MultiplicativeExpression + AdditiveExpression
    MultiplicativeExpression - AdditiveExpression

MultiplicativeExpression:
    UnaryExpression
    UnaryExpression MultiplicativeOperator MultiplicativeExpression

UnaryExpression:
    MemberExpression
    UnaryOperator UnaryExpression
    - UnaryExpression
    IncrementOperator MemberExpression
    MemberExpression IncrementOperator
    new Constructor
    delete MemberExpression

Constructor:
    this . ConstructorCall
    ConstructorCall

ConstructorCall:
    Identifier
    Identifier ( ArgumentListOpt )
    Identifier . ConstructorCall

MemberExpression:
    PrimaryExpression
    PrimaryExpression . MemberExpression
    PrimaryExpression [ Expression ]
    PrimaryExpression ( ArgumentListOpt )

ArgumentList:
    empty
    AssignmentExpression
    AssignmentExpression , ArgumentList

PrimaryExpression:
    ( Expression )
    Identifier
    IntegerLiteral
    FloatingPointLiteral
    StringLiteral
    false
    true
    null
    this