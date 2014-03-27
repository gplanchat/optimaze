<?php
/**
 * Created by PhpStorm.
 * User: Greg
 * Date: 17/03/14
 * Time: 10:01
 */

namespace Gplanchat\Javascript\Tokenizer;

use Gplanchat\Tokenizer\DataSource\DataSourceInterface;
use Gplanchat\Tokenizer\Token;

class Tokenizer
    implements TokenizerInterface
{
    /**
     * @var bool
     */
    public $scanOperand = true;

    /**
     * @var array
     */
    private $keywords = [
        TokenizerInterface::KEYWORD_BREAK,
        TokenizerInterface::KEYWORD_CASE,
        TokenizerInterface::KEYWORD_CATCH,
        TokenizerInterface::KEYWORD_CONST,
        TokenizerInterface::KEYWORD_CONTINUE,
        TokenizerInterface::KEYWORD_DEBUGGER,
        TokenizerInterface::KEYWORD_DEFAULT,
        TokenizerInterface::KEYWORD_DELETE,
        TokenizerInterface::KEYWORD_DO,
        TokenizerInterface::KEYWORD_ELSE,
        TokenizerInterface::KEYWORD_ENUM,
        TokenizerInterface::KEYWORD_FALSE,
        TokenizerInterface::KEYWORD_FINALLY,
        TokenizerInterface::KEYWORD_FOR,
        TokenizerInterface::KEYWORD_FUNCTION,
        TokenizerInterface::KEYWORD_IF,
        TokenizerInterface::KEYWORD_IN,
        TokenizerInterface::KEYWORD_INSTANCEOF,
        TokenizerInterface::KEYWORD_NEW,
        TokenizerInterface::KEYWORD_NULL,
        TokenizerInterface::KEYWORD_RETURN,
        TokenizerInterface::KEYWORD_SWITCH,
        TokenizerInterface::KEYWORD_THIS,
        TokenizerInterface::KEYWORD_THROW,
        TokenizerInterface::KEYWORD_TRUE,
        TokenizerInterface::KEYWORD_TRY,
        TokenizerInterface::KEYWORD_TYPEOF,
        TokenizerInterface::KEYWORD_VAR,
        TokenizerInterface::KEYWORD_VOID,
        TokenizerInterface::KEYWORD_WHILE,
        TokenizerInterface::KEYWORD_WITH
    ];

    /**
     * @var array
     */
    private $opTypeNames = [
        TokenizerInterface::OP_SEMICOLON,
        TokenizerInterface::OP_COMMA,
        TokenizerInterface::OP_HOOK,
        TokenizerInterface::OP_COLON,
        TokenizerInterface::OP_OR,
        TokenizerInterface::OP_AND,
        TokenizerInterface::OP_BITWISE_OR,
        TokenizerInterface::OP_BITWISE_XOR,
        TokenizerInterface::OP_BITWISE_AND,
        TokenizerInterface::OP_STRICT_EQ,
        TokenizerInterface::OP_EQ,
        TokenizerInterface::OP_ASSIGN,
        TokenizerInterface::OP_STRICT_NE,
        TokenizerInterface::OP_NE,
        TokenizerInterface::OP_LSH,
        TokenizerInterface::OP_LE,
        TokenizerInterface::OP_LT,
        TokenizerInterface::OP_URSH,
        TokenizerInterface::OP_RSH,
        TokenizerInterface::OP_GE,
        TokenizerInterface::OP_GT,
        TokenizerInterface::OP_INCREMENT,
        TokenizerInterface::OP_DECREMENT,
        TokenizerInterface::OP_PLUS,
        TokenizerInterface::OP_MINUS,
        TokenizerInterface::OP_MUL,
        TokenizerInterface::OP_DIV,
        TokenizerInterface::OP_MOD,
        TokenizerInterface::OP_NOT,
        TokenizerInterface::OP_BITWISE_NOT,
        TokenizerInterface::OP_DOT,
        TokenizerInterface::OP_LEFT_SQUARE_BRACKET,
        TokenizerInterface::OP_RIGHT_SQUARE_BRACKET,
        TokenizerInterface::OP_LEFT_CURLY,
        TokenizerInterface::OP_RIGHT_CURLY,
        TokenizerInterface::OP_LEFT_BRACKET,
        TokenizerInterface::OP_RIGHT_BRACKET,
        TokenizerInterface::OP_CONDCOMMENT_END
    ];

    /**
     * @var array
     */
    private $assignOps = [
        TokenizerInterface::OP_BITWISE_OR,
        TokenizerInterface::OP_BITWISE_XOR,
        TokenizerInterface::OP_BITWISE_AND,
        TokenizerInterface::OP_LSH,
        TokenizerInterface::OP_RSH,
        TokenizerInterface::OP_URSH,
        TokenizerInterface::OP_PLUS,
        TokenizerInterface::OP_MINUS,
        TokenizerInterface::OP_MUL,
        TokenizerInterface::OP_DIV,
        TokenizerInterface::OP_MOD
    ];

    /**
     * @var string
     */
    private $opRegExp = null;

    /**
     * @var array
     */
    private $tokens = [];

    /**
     * @var int
     */
    private $tokenCount = 0;

    /**
     * @var int
     */
    private $tokenIndex = 0;

    /**
     * @var int
     */
    private $lookAhead = 0;

    /**
     * @var bool
     */
    private $scanNewLines = false;

    /**
     * @var string|null
     */
    private $filename = null;

    /**
     * @var int
     */
    private $line;

    /**
     * @var int
     */
    private $cursor = 0;

    /**
     * @var DataSourceInterface
     */
    private $source = null;

    /**
     * @param DataSourceInterface $source
     */
    public function __construct(DataSourceInterface $source)
    {
        $this->source = $source;
        $this->opRegExp = '#^(' . implode('|', array_map('preg_quote', $this->opTypeNames)) . ')#';
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     */
    public function current()
    {
        return $this->tokens[$this->tokenIndex];
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        $this->tokenIndex++;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     */
    public function key()
    {
        return $this->tokenIndex;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid()
    {
        /** @var Token $token */
        $token = $this->get();
        if ($token->getType() !== TokenizerInterface::TOKEN_END) {
            return true;
        }

        return false;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        $this->tokenIndex = 0;
    }

    public function get()
    {
        if ($this->tokenIndex < $this->tokenCount) {
            return $this->tokens[$this->tokenIndex];
        }

        // whitespace handling; gobble up \r as well (effectively we don't have support for MAC newlines!)
        $newLineExpression = $this->scanNewLines ? '/^[ \r\t]+/' : '/^\s+/';
        $input = '';
        $chunkSize = 0;
        while (true) {
            $input = $this->source->get(null, $this->cursor);
            $chunkSize = strlen($input);

            if (preg_match($newLineExpression, $input, $match) > 0) {
                $spacesLength = strlen($match[0]);
                $this->cursor += $spacesLength;
                if (!$this->scanNewLines) {
                    $this->line += substr_count($match[0], "\n");
                }

                if ($spacesLength == $chunkSize) {
                    continue; // complete chunk contained whitespace
                }

                $input = $this->source->get($chunkSize, $this->cursor);
                if ($input == '' || $input[0] != '/') {
                    break;
                }
            }

            if (!preg_match('/^\/(?:\*.*?\*\/|\/[^\n]*)/s', $input, $match)) {
                break;
            }

            if (strpos($match[0], '/*') === 0) {
                $this->push(TokenizerInterface::TOKEN_BLOCK_COMMENT, $match[0]);
                continue;
            } else if (strpos($match[0], '//') === 0) {
                $this->push(TokenizerInterface::TOKEN_LINE_COMMENT, $match[0]);
                continue;
            }
            break;
        }

        if ($input == '') {
            return $this->push(TokenizerInterface::TOKEN_END, '');
        }

        switch ($input[0]) {
        /** @noinspection PhpMissingBreakStatementInspection */
        case '0':
            // hexadecimal
            if (($input[1] == 'x' || $input[1] == 'X') && preg_match('/^0x[0-9a-f]+/i', $input, $match)) {
                return $this->push(TokenizerInterface::TOKEN_NUMBER_INTEGER, $match[0]);
            }

        case '1':
        case '2':
        case '3':
        case '4':
        case '5':
        case '6':
        case '7':
        case '8':
        case '9':
            if (preg_match('/^\d+(?:\.\d*)?([eE][-+]?\d+)?/', $input, $match)) {
                return $this->push(empty($match[1]) ? TokenizerInterface::TOKEN_NUMBER_INTEGER : TokenizerInterface::TOKEN_NUMBER_FLOATING_POINT, $match[0]);
            }

            throw new Exception\SyntaxError('Invalid number literal',
                $this->source->getPath(), $this->line
            );

        case "'":
            if (preg_match('/^\'(?:[^\\\\\'\r\n]++|\\\\(?:.|\r?\n))*\'/', $input, $match)) {
                return $this->push(TokenizerInterface::TOKEN_STRING, $match[0]);
            }

            if ($chunkSize) {
                return $this->get(null); // retry with a full chunk fetch
            }

            throw new Exception\SyntaxError('Unterminated string literal',
                $this->source->getPath(), $this->line
            );

        case '"':
            if (preg_match('/^"(?:[^\\\\"\r\n]++|\\\\(?:.|\r?\n))*"/', $input, $match)) {
                return $this->push(TokenizerInterface::TOKEN_STRING, $match[0]);
            }

            if ($chunkSize) {
                return $this->get(null); // retry with a full chunk fetch
            }

            throw new Exception\SyntaxError('Unterminated string literal',
                $this->source->getPath(), $this->line
            );

        /** @noinspection PhpMissingBreakStatementInspection */
        case '/':
            if ($this->scanOperand && preg_match('/^\/((?:\\\\.|\[(?:\\\\.|[^\]])*\]|[^\/])+)\/([gimy]*)/', $input, $match)) {
                return $this->push(TokenizerInterface::TOKEN_REGEXP, $match[0]);
            }

        case '|':
        case '^':
        case '&':
        case '<':
        case '>':
        case '+':
        case '-':
        case '*':
        case '%':
        case '=':
        case '!':
            // should always match
            preg_match($this->opRegExp, $input, $match);
            if (in_array($match[0], $this->assignOps) && $input[strlen($match[0])] == '=') {
                return $this->push(TokenizerInterface::OP_ASSIGN, $match[0] . '=', $match[0]);
            }

            if ($this->scanOperand) {
                if ($match[0] == TokenizerInterface::OP_PLUS) {
                    return $this->push(TokenizerInterface::OP_UNARY_PLUS, $match[0], TokenizerInterface::OP_PLUS);
                } else if ($match[0] == TokenizerInterface::OP_MINUS) {
                    return $this->push(TokenizerInterface::OP_UNARY_MINUS, $match[0], TokenizerInterface::OP_MINUS);
                }
            }
            return $this->push(TokenizerInterface::OP_UNARY_PLUS, $match[0]);

        /** @noinspection PhpMissingBreakStatementInspection */
        case '.':
            if (preg_match('/^\.\d+(?:[eE][-+]?\d+)?/', $input, $match)) {
                return $this->push(TokenizerInterface::TOKEN_NUMBER_FLOATING_POINT, $match[0]);
            }

        case ';':
        case ',':
        case '?':
        case ':':
        case '~':
        case '[':
        case ']':
        case '{':
        case '}':
        case '(':
        case ')':
            // these are all single
            return $this->push($input[0], $input[0]);

        case "\n":
            if ($this->scanNewLines) {
                return $this->push(TokenizerInterface::TOKEN_NEWLINE, "\n");
            }

            throw new Exception\SyntaxError('Illegal token',
                $this->source->getPath(), $this->line
            );

        default:
            // FIXME: add support for unicode and unicode escape sequence \uHHHH
            if (preg_match('/^[$\w]+/', $input, $match)) {
                return $this->push(in_array($match[0], $this->keywords) ? $match[0] : TokenizerInterface::TOKEN_IDENTIFIER, $match[0]);
            }

            throw new Exception\SyntaxError('Illegal token',
                $this->source->getPath(), $this->line
            );
        }
    }

    protected function push($type, $value, $assignOp = null)
    {
        $token = new Token($type, $value, $this->cursor, $this->cursor, $this->line, $assignOp);

        $this->tokens[] = $token;
        $this->tokenCount++;
        $this->cursor += strlen($value);
        return $token;
    }
}
