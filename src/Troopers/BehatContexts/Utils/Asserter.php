<?php

namespace Troopers\BehatContexts\Utils;

use Knp\FriendlyContexts\Table\Node;
use Knp\FriendlyContexts\Table\NodesBuilder;
use Knp\FriendlyContexts\Utils\Asserter AS BaseAsserter;

class Asserter extends BaseAsserter {

    /**
     * @param $value
     * @return string
     */
    private function explode($value)
    {
        if (!is_array($value)) {
            return (string) $value;
        } else {
            return $this->formater->tableToString($value);
        }
    }

    /**
     * @param array $expected
     * @param array $real
     * @param null $message
     * @return bool
     */
    public function assertArrayContains(array $expected, array $real, $message = null)
    {
        $message = $message ?: sprintf("The given array\r\n\r\n%s\r\ndoes not contain the following rows\r\n\r\n%s", $this->explode($real), $this->explode($expected));
        foreach ($expected as $row) {
            $this->assert(is_array($row), $message);
        }
        foreach ($real as $row) {
            $this->assert(is_array($row), $message);
        }
        $nodes = (new NodesBuilder)->build($real);
        $nodes = $nodes->search(current(current($expected)));
        foreach ($nodes as $initial) {
            $result    = true;
            $cells     = $expected;
            /** @var Node $lineStart */
            $lineStart = $initial;
            do {
                $columns       = array_shift($cells);
                /** @var Node $columnElement */
                $columnElement = $lineStart;
                do {
                    $content = array_shift($columns);
                    $result = $columnElement
                        ? $content === trim($columnElement->getContent())
                        : false
                    ;
                    $columnElement = $columnElement ? $columnElement->getRight() : null;
                } while (!empty($columns) && $result);
                $lineStart = $lineStart ? $lineStart->getBottom() : null;
            } while (!empty($cells) && $result);
            if ($result) {
                return true;
            }
        }
        $this->assert(false, $message);
    }

}