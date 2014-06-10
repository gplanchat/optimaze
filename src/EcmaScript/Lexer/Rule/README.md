# Program rules

```
Program:
    empty
    Element Program
```
```
Element:
    DocComment
    BlockComment
    LineComment
    Statement
```

# Statement rules

```
FunctionExpression:
    function Identifier ( empty ) { StatementList }
    function Identifier ( ParameterList ) { StatementList }

ClosureExpression:
    function ( empty ) { StatementList }
    function ( ParameterList ) { StatementList }
```
```
ParameterList:
    Identifier
    Identifier , ParameterList
```
```
StatementList:
    empty
    Statement StatementList
```
```
Statement:
    ;
    if Condition Statement
    if Condition Statement else Statement
    while Condition Statement
    for ( ; Expression ; Expression ) Statement
    for ( VariableListOrExpression ; Expression ; Expression ) Statement
    for ( VariableListOrExpression in Expression ) Statement
    switch Condition { SwitchStatement }
    break ;
    continue ;
    with ( Expression ) Statement
    return AssignmentExpression ;
    { StatementList }
    VariableListOrExpression ;
    FunctionExpression
```
```
SwitchStatement:
    SwitchCase : StatementList SwitchStatement
```
```
SwitchCase:
    case StringLiteral
    case IntegerLiteral
    case FloatingPointLiteral
    case Identifier
    default
```
```
Condition:
    ( Expression )
```
```
VariableListOrExpression:
    var VariableList
    Expression
```
```
VariableList:
    Variable
    Variable , VariableList
```
```
Variable:
    Identifier
    Identifier = AssignmentExpression
```
```
Expression:
    AssignmentExpression
    AssignmentExpression , Expression
```

# Conditional rules

```
AssignmentExpression:
    ConditionalExpression
    ConditionalExpression AssignmentOperator AssignmentExpression
```
```
ConditionalExpression:
    OrExpression
    OrExpression ? AssignmentExpression : AssignmentExpression
```
```
OrExpression:
    AndExpression
    AndExpression || OrExpression
```
```
AndExpression:
    BitwiseOrExpression
    BitwiseOrExpression && AndExpression
```
```
BitwiseOrExpression:
    BitwiseXorExpression
    BitwiseXorExpression | BitwiseOrExpression
```
```
BitwiseXorExpression:
    BitwiseAndExpression
    BitwiseAndExpression ^ BitwiseXorExpression
```
```
BitwiseAndExpression:
    EqualityExpression
    EqualityExpression & BitwiseAndExpression
```
```
EqualityExpression:
    RelationalExpression
    RelationalExpression EqualityOperator EqualityExpression
```
```
RelationalExpression:
    ShiftExpression
    RelationalExpression RelationalOperator ShiftExpression
```
```
ShiftExpression:
    AdditiveExpression
    AdditiveExpression ShiftOperator ShiftExpression
```
```
AdditiveExpression:
    MultiplicativeExpression
    MultiplicativeExpression + AdditiveExpression
    MultiplicativeExpression - AdditiveExpression
```
```
MultiplicativeExpression:
    UnaryExpression
    UnaryExpression MultiplicativeOperator MultiplicativeExpression
```
# Operation rules

```
UnaryExpression:
    MemberExpression
    UnaryOperator UnaryExpression
    - UnaryExpression
    IncrementOperator MemberExpression
    MemberExpression IncrementOperator
    new Constructor
    Constructor
    delete MemberExpression
    ( Expression )
```
```
Constructor:
    this . MemberExpression
    MemberExpression
```
```
MemberExpression:
    PrimaryExpression
    PrimaryExpression . MemberExpression
    MemberExpression [ Expression ]
    MemberExpression ( ArgumentListOpt )
```
```
ArgumentList:
    empty
    AssignmentExpression
    AssignmentExpression , ArgumentList
```
```
PrimaryExpression:
    ClosureExpression
    ArrayExpression
    ObjectExpression
    Identifier
    IntegerLiteral
    FloatingPointLiteral
    StringLiteral
    RegularExpressionLiteral
    false
    true
    null
    this
```
```
ArrayExpression:
    [ Expression ]
```
```
ObjectExpression:
    { empty }
    { ObjectEntryList }
```
```
ObjectEntry:
    ObjectEntryKey : AssignmentExpression
    ObjectEntryKey : AssignmentExpression ( empty )
    ObjectEntryKey : AssignmentExpression ( ParameterList )
    get Identifier ( empty ) { StatementList }
    get Identifier ( ParameterList ) { StatementList }
    set Identifier ( empty ) { StatementList }
    set Identifier ( ParameterList ) { StatementList }
```
```
ObjectEntryKey:
    Identifier
    StringLiteral
    FloatingPointLiteral
    IntegerLiteral
```
```
ObjectEntryList:
    ObjectEntry
    ObjectEntry , ObjectEntryList
```
