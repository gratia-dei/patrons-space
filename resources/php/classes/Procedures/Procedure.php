<?php

class Procedure extends Base
{
    protected function print($data): void
    {
        echo $this->getDate()->getCurrentDateTime() . ' [' . get_called_class() . '] ' . print_r($data, true) . "\n";
    }
}
