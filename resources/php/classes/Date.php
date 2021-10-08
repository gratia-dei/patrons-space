<?php

class Date
{
    public function getCurrentDateTime(): string
    {
        return date('Y-m-d H:i:s');
    }

    public function getCurrentYear(): string
    {
        return date('Y');
    }

    public function getCurrentMonth(): string
    {
        return date('m');
    }

    public function getCurrentDay(): string
    {
        return date('d');
    }
}
