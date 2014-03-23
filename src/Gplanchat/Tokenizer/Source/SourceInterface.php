<?php
/**
 * Created by PhpStorm.
 * User: Greg
 * Date: 17/03/14
 * Time: 12:06
 */

namespace Gplanchat\Tokenizer\Source;


interface SourceInterface
{
    /**
     * @return string
     */
    public function __toString();

    /**
     * @param int $length
     * @param int $offset
     * @return string
     */
    public function __call($length = null, $offset = null);

    /**
     * @param int $length
     * @param int $offset
     * @return string
     */
    public function get($length = null, $offset = null);

    /**
     * @return string
     */
    public function getPath();
}
