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
 * @package Gplanchat\EcmaScript\Tokenizer
 */

namespace Gplanchat\EcmaScript\Tokenizer;

use Gplanchat\EcmaScript\Lexer\Rule\RuleInterface;
use Gplanchat\Tokenizer\DataSource\DataSourceInterface;
use Gplanchat\Tokenizer\Token;

/**
 * EcmaScript tokenizer implementation
 *
 * @package Gplanchat\EcmaScript\Tokenizer
 */
class Tokenizer
    implements TokenizerInterface
{
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
     * @var array
     */
    private $precedingRegexTokens = [
        TokenizerInterface::OP_NOT,
        TokenizerInterface::OP_NE,
        TokenizerInterface::OP_STRICT_NE,
        TokenizerInterface::OP_MOD,
        TokenizerInterface::OP_ASSIGN,
        TokenizerInterface::OP_AND,
        TokenizerInterface::OP_BITWISE_AND,
        TokenizerInterface::OP_LEFT_BRACKET,
        TokenizerInterface::OP_MUL,
        TokenizerInterface::OP_PLUS,
        TokenizerInterface::OP_COMMA,
        TokenizerInterface::OP_MINUS,
        TokenizerInterface::OP_DOT,
        TokenizerInterface::OP_DIV,
        TokenizerInterface::OP_COLON,
        TokenizerInterface::OP_SEMICOLON,
        TokenizerInterface::OP_LT,
        TokenizerInterface::OP_LE,
        TokenizerInterface::OP_GT,
        TokenizerInterface::OP_GE,
        TokenizerInterface::OP_RSH,
        TokenizerInterface::OP_LSH,
        TokenizerInterface::OP_URSH,
        TokenizerInterface::OP_HOOK,
        TokenizerInterface::OP_LEFT_SQUARE_BRACKET,
        TokenizerInterface::OP_BITWISE_XOR,
        TokenizerInterface::OP_LEFT_CURLY,
        TokenizerInterface::OP_BITWISE_OR,
        TokenizerInterface::OP_OR,
        TokenizerInterface::OP_BITWISE_NOT,
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
        TokenizerInterface::KEYWORD_FINALLY,
        TokenizerInterface::KEYWORD_FOR,
        TokenizerInterface::KEYWORD_FUNCTION,
        TokenizerInterface::KEYWORD_IF,
        TokenizerInterface::KEYWORD_IN,
        TokenizerInterface::KEYWORD_INSTANCEOF,
        TokenizerInterface::KEYWORD_NEW,
        TokenizerInterface::KEYWORD_RETURN,
        TokenizerInterface::KEYWORD_SWITCH,
        TokenizerInterface::KEYWORD_THROW,
        TokenizerInterface::KEYWORD_TRY,
        TokenizerInterface::KEYWORD_TYPEOF,
        TokenizerInterface::KEYWORD_VAR,
        TokenizerInterface::KEYWORD_WHILE,
        TokenizerInterface::KEYWORD_WITH
    ];

    /**
     * @var string
     */
    private $opRegExp = null;

    /**
     * @var Token[]
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
    private $line = 1;

    /**
     * @var int
     */
    private $lineOffset = 0;

    /**
     * @var int
     */
    private $cursor = 0;

    /**
     * @var DataSourceInterface
     */
    private $source = null;

    /**
     * @var bool
     */
    private $isSourceConsumed = false;

    /**
     * @param DataSourceInterface $source
     */
    public function __construct(DataSourceInterface $source)
    {
        $this->source = $source;
        $this->opRegExp = '#^' . implode('|', array_map('preg_quote', $this->opTypeNames)) . '#';
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     */
    public function current()
    {
        /** @var Token $token */
        return $this->get();
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
     * @return bool The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid()
    {
        /** @var Token $token */
        $token = $this->get();
        if ($token !== null) {
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

    /**
     * @param int $tokenIndex
     * @return Token|null
     * @throws Exception\SyntaxError
     */
    public function get($tokenIndex = null)
    {
        if ($tokenIndex === null) {
            $tokenIndex = $this->tokenIndex;
        }

        if ($tokenIndex < $this->tokenCount) {
            return $this->tokens[$tokenIndex];
        }

        $input = '';
        $chunkSize = 0;
        while (true) {
            $input = $this->source->get(null, $this->cursor);
            $chunkSize = strlen($input);

            if (preg_match('/^\s+/', $input, $match) > 0) {
                $spaces = $match[0];
                while (($pos = strpos($spaces, "\n")) !== false) {
                    $this->cursor += $pos;
                    $this->line++;
                    $this->lineOffset = 0;

                    $spaces = substr($spaces, $pos + 1);
                    $this->push(TokenizerInterface::TOKEN_NEWLINE, "\n", null, true);
                }
                $spacesLength = strlen($spaces);
                $this->cursor += $spacesLength;
                $this->lineOffset += $spacesLength + 1;
                continue;
            }

            if (!preg_match('/^\/(?:\*.*?\*\/|\/[^\n]*)/s', $input, $match)) {
                break;
            }

            if (strpos($match[0], '/*') === 0) {
                $type = TokenizerInterface::TOKEN_BLOCK_COMMENT;
                if (strpos($match[0], '/**') === 0) {
                    $type = TokenizerInterface::TOKEN_DOC_COMMENT;
                }

                $commentLength = strlen($match[0]);
                $this->line += substr_count($match[0], "\n");
                if (($pos = strrpos($match[0], "\n")) !== false) {
                    $this->lineOffset = $commentLength - $pos;
                } else {
                    $this->lineOffset += $commentLength;
                }
                $this->push($type, $match[0], null, true);
                continue;
            } else if (strpos($match[0], '//') === 0) {
                $this->push(TokenizerInterface::TOKEN_LINE_COMMENT, $match[0]);
                continue;
            }
            break;
        }

        if ($input == '') {
            if ($this->isSourceConsumed === true) {
                return null;
            }

            $this->isSourceConsumed = true;
            return $this->push(TokenizerInterface::TOKEN_END, null);
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
                $this->source->getPath(), $this->line, $this->lineOffset, $this->cursor
            );

        case "'":
            if (preg_match('/^\'(?:[^\\\\\'\r\n]++|\\\\(?:.|\r?\n))*\'/', $input, $match)) {
                return $this->push(TokenizerInterface::TOKEN_STRING, $match[0]);
            }

            if ($chunkSize) {
                return $this->get(null); // retry with a full chunk fetch
            }

            throw new Exception\SyntaxError('Unterminated string literal',
                $this->source->getPath(), $this->line, $this->lineOffset, $this->cursor
            );

        case '"':
            if (preg_match('/^"(?:[^\\\\"\r\n]++|\\\\(?:.|\r?\n))*"/', $input, $match)) {
                return $this->push(TokenizerInterface::TOKEN_STRING, $match[0]);
            }

            if ($chunkSize) {
                return $this->get(null); // retry with a full chunk fetch
            }

            throw new Exception\SyntaxError('Unterminated string literal',
                $this->source->getPath(), $this->line, $this->lineOffset, $this->cursor
            );

        /** @noinspection PhpMissingBreakStatementInspection */
        case '/':
            if ($this->checkPreviousTokenType($this->precedingRegexTokens)) {
                preg_match('/^\/((?:\\\\.|\[(?:\\\\.|[^\]])*\]|[^\/])+)\/([gimy]*)/', $input, $match);
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
            if ($match[0][strlen($match[0]) - 1] === '=') {
                $operator = substr($match[0], 0, -1);
                if (in_array($operator, $this->assignOps)) {
                    return $this->push(TokenizerInterface::OP_ASSIGN, $match[0], $operator);
                }
            }

            return $this->push($match[0], $match[0]);

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
            throw new Exception\SyntaxError('Illegal token',
                $this->source->getPath(), $this->line, $this->lineOffset, $this->cursor
            );

        default:
            if (preg_match('/^(get|set)\s+[$\w]+/', $input, $match)) {
                return $this->push($match[1], $match[1]);
            }
            // FIXME: add support for unicode and unicode escape sequence \uHHHH
            if (preg_match('/^[$\w]+/', $input, $match)) {
                return $this->push(in_array($match[0], $this->keywords) ? $match[0] : TokenizerInterface::TOKEN_IDENTIFIER, $match[0]);
            }

            throw new Exception\SyntaxError('Illegal token',
                $this->source->getPath(), $this->line, $this->lineOffset, $this->cursor
            );
        }
    }

    protected function push($type, $value, $assignOp = null, $ignoreOffsetUpdate = false)
    {
        $tokenLength = strlen($value);

        $token = new Token(
            $this->tokenCount++,
            $type,
            $value,
            $this->cursor,
            $this->cursor + $tokenLength,
            $this->source->getPath(),
            $this->line,
            $this->lineOffset,
            $assignOp
        );

        $this->tokens[] = $token;
        $this->cursor += $tokenLength;
        if ($ignoreOffsetUpdate !== true) {
            $this->lineOffset += $tokenLength;
        }
        $this->printInfo($token);
        return $token;
    }

    private function printInfo(Token $token)
    {
        echo $token;
        foreach (debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS) as $call) {
            if (!isset($call['class'])) {
                continue;
            }
            $re = new \ReflectionClass($call['class']);
            if (!$re->isSubclassOf(RuleInterface::class)) {
                continue;
            }
            if (isset($call['file']) && isset($call['line'])) {
                echo sprintf('%s%s%s %s:%d', $call['class'], $call['type'], $call['function'], $call['file'], $call['line']) . PHP_EOL;
            } else {
                echo sprintf('%s%s%s', $call['class'], $call['type'], $call['function']) . PHP_EOL;
            }
        }
    }

    /**
     * @param Token $token
     * @param array $typeList
     * @return bool|null
     */
    public function isPreviousTokenType(Token $token, array $typeList)
    {
        $token = $this->get($token->getIndex() - 1);
        if ($token !== null) {
            return in_array($token->getType(), $typeList);
        }

        return false;
    }

    /**
     * @param Token $token
     * @param array $typeList
     * @return bool|null
     */
    public function isNextTokenType(Token $token, array $typeList)
    {
        $token = $this->get($token->getIndex() + 1);
        if ($token !== null) {
            return in_array($token->getType(), $typeList);
        }

        return false;
    }

    /**
     * @param array $typeList
     * @return bool|null
     */
    protected function checkPreviousTokenType(array $typeList)
    {
        if (isset($this->tokens[$this->tokenIndex - 1])) {
            return in_array($this->tokens[$this->tokenIndex - 1]->getType(), $typeList);
        }

        return false;
    }

    public function getCodeContext(Token $token)
    {
        $lineOffset = $token->getStart() - $token->getLineOffset() - 10;
        $context = $this->source->get(500, $lineOffset);
        $lineStart = strpos($context, "\n");
        $eol = strpos($context, "\n", $lineStart);
        return substr($context, $lineStart, $eol) . PHP_EOL
            . str_pad('^', $token->getLineOffset(), ' ', STR_PAD_LEFT) . PHP_EOL;
    }
}
