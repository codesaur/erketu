<?php namespace codesaur\HTML;

interface HTML5Interface
{
    public static function a(array $attr = [], $inner = '', bool $close = true) : string;
    public static function u($inner = '', array $attr = [], bool $close = true) : string;
    public static function br(array $attr = [], bool $xhtml = true) : string;
    public static function hr(array $attr = [], bool $xhtml = true) : string;
    public static function form(array $attr = [], $inner = '', bool $close = false, bool $xhtml = true) : string;
    public static function div(array $attr = [], $inner = '', bool $close = false) : string;
    public static function divc($content, $class = 'row') : string;
    public static function img(array $attr = [], bool $xhtml = true) : string;
    public static function input(array $attr = [], bool $xhtml = true) : string;
    public static function button(array $attr = [], $inner = '', bool $close = true, bool $xhtml = true) : string;
    public static function label(array $attr = [], $inner = '', bool $close = true) : string;
    public static function p(array $attr = [], $inner = '', bool $close = true) : string;
    public static function h(int $h = 1, array $attr = [], $inner = '', bool $close = true) : string;
    public static function nbsp(int $multiplier = 1) : string;
    public static function ul(array $attr = [], $inner = '', bool $close = false) : string;
    public static function ol(array $attr = [], $inner = '', bool $close = false, bool $xhtml = true) : string;
    public static function li(array $attr = [], $inner = '', bool $close = true) : string;
    public static function select(array $attr = [], $inner = '', bool $close = false, bool $xhtml = true) : string;
    public static function option(array $attr = [], $inner = '', bool $close = true, bool $xhtml = true) : string;
    public static function script(array $attr = [], $inner = '', bool $close = true, bool $xhtml = true) : string;
    public static function pre(array $attr = [], $inner = '', bool $close = true) : string;
    public static function span(array $attr = [], $inner = '', bool $close = true) : string;
    public static function textarea(array $attr = [], $inner = '', bool $close = true, bool $xhtml = true) : string;
    public static function table(array $attr = [], $inner = '', bool $close = false, bool $xhtml = true) : string;
    public static function tr(array $attr = [], $inner = '', bool $close = false) : string;
    public static function table_cell(string $cell, array $attr = [], $inner = '', bool $close = true) : string;
    public static function th(array $attr = [], $inner = '', bool $close = true) : string;
    public static function td(array $attr = [], $inner = '', bool $close = true) : string;
    public static function global_attributes(array $attr = []) : string;
    public static function event_attributes(array $attr = []) : string;
    public static function inline($content) : string;
    public static function element(string $name, array $attr = [], string $custom='', $inner = '', bool $close = false) : string;
    public static function open(string $name = '') : string;
    public static function close(string $name = '', string $closure = '>') : string;
}
