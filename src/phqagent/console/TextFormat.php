<?php
namespace phqagent\console;

abstract class TextFormat
{

    const WHITE        = "\x1b[38;5;231m";
    const BLACK        = "\x1b[38;5;16m";
    const GREEN        = "\x1b[38;5;83m";
    const DARK_BLUE    = "\x1b[38;5;19m";
    const YELLOW       = "\x1b[38;5;227m";
    const RED          = "\x1b[38;5;203m";
    const GOLD         = "\x1b[38;5;214m";
    const AQUA         = "\x1b[38;5;87m";
    const PURPLE       = "\x1b[38;5;127m";
    const LIGHT_PURPLE = "\x1b[38;5;207m";
    const RESET        = "\x1b[0m";
    const BOLD         = "\x1b[1m";
    const ITALIC       = "\x1b[3m";
    const UNDERLINE    = "\x1b[4m";

    public static function clean($string)
    {
        return preg_replace("/\x1b[\\(\\][[0-9;\\[\\(]+[Bm]/", "", $string);
    }
}
