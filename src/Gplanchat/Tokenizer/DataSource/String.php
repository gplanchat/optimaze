<?php
/**
 * Created by PhpStorm.
 * User: Greg
 * Date: 17/03/14
 * Time: 12:06
 */

namespace Gplanchat\Tokenizer\DataSource;

class String
    implements DataSourceInterface
{
    /**
     * @var string
     */
    private $contents = null;

    /**
     * @param string $contents
     */
    public function __construct($contents)
    {
        $this->contents = $contents;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->contents;
    }

    /**
     * @param int $length
     * @param int $offset
     * @return string
     */
    public function __call($length = null, $offset = null)
    {
        return $this->get($length, $offset);
    }

    /**
     * @param int $length
     * @param int $offset
     * @return string
     */
    public function get($length = null, $offset = null)
    {
        if ($offset === null) {
            $offset = 0;
        }

        if ($length === null) {
            return substr($this->contents, $offset);
        } else {
            return substr($this->contents, $offset, $length);
        }
    }

    public function getPath()
    {
        return '[string]';
    }
}
