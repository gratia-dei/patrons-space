<?php

abstract class ContentBlock extends Content
{
    protected const UNKNOWN_PAGE_NUMBER = self::VARIABLE_NAME_SIGN . 'unknown' . self::VARIABLE_NAME_SIGN;
    protected const UNKNOWN_SIGN = '?';
}
