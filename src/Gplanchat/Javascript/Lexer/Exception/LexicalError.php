<?php
/**
 * Created by PhpStorm.
 * User: Greg
 * Date: 17/03/14
 * Time: 16:58
 */

namespace Gplanchat\Javascript\Lexer\Exception;


class LexicalError
    extends \RuntimeException
    implements Exception
{
    /** @var string */
    private $sourceFile = null;

    /** @var int */
    private $sourceLine = null;

    /** @var int */
    private $sourceOffset = null;

    /**
     * @param string $message
     * @param int $file
     * @param int $line
     * @param int $offset
     */
    public function __construct($message, $file = null, $line = null, $offset = null)
    {
        parent::__construct($message);

        $this->sourceFile = $file;
        $this->sourceLine = $line;
        $this->sourceOffset = $offset;
    }

    /**
     * @return string
     */
    public function getSourceFile()
    {
        return $this->file;
    }

    /**
     * @return int
     */
    public function getSourceLine()
    {
        return $this->line;
    }

    /**
     * @return int
     */
    public function getSourceOffset()
    {
        return $this->offset;
    }

    public function __toString()
    {
        return sprintf('Parse error: %s in file "%s" on line %d', $this->getMessage(), $this->getSourceFile(), $this->getSourceLine())
            . PHP_EOL . $this->getTraceAsString();
    }
}
