<?php
/** 
 * Funciones
 * 
 * @category Functions
 * @package  Eliscraper
 * @author   José González <josegs84@gmail.com>
 * @license  GNU General Public License version 3
 * @version  1.0.2
 * @link     Link
 * _____ ____   ___    _______________     ___   ____ _____  ____   
 * | ___\\  |   | |   / ___|  ___/  _ \   / _ \ | __ \| ___\/  _ \  
 * | |___ | |   | |  / /__ | |   | |_\ \ / / \ \| |/ /| |_  | |_\ \ 
 * | ___| | |   | |  \___ \| |   |  _  / | |_| ||  _/ | __| |  _  / 
 * | |___ | |__ | |  ___/ /| |___| | \ \ |  _  || |   | |___| | \ \ 
 * |____| |____\\_| |____/ |_____|_|  \_\|_| |_||_|   |_____|_|  \_\
 * 
 * ============================================================================
 *   Copyright (c) 2018-2021 Jose Gonzalez Silva
 *   This program is free software: you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation, either version 3 of the License, or
 *   (at your option) any later version.
 *
 *   This program is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with this program.  If not, see <https://www.gnu.org/licenses/>.
 * ============================================================================
 */

function showHelp(bool $return = true) {
    echo "Soy la ayuda";
}

function buildOutput(string $type, object $content) {

}


/**
 * Set color for the output. Only for bash terminals.
 * This will be possible by https://www.if-not-true-then-false.com/2010/php-class-for-coloring-php-command-line-cli-scripts-output-php-output-colorizing-using-bash-shell-colors/
 * 
 * @param string $str     String for colour.
 * @param string $fgcolor Foreground color.
 * @param string $bgcolor Background color. No color by default.
 * 
 * @return string
 */
function strColor(string $str,string $fgcolor='white', string $bgcolor=null)
{

    $out="";

    //ncurses_init();

    //if (ncurses_has_colors() === true) {
    if (true === true) {
        
    static $fgcolors = array(
        'black' => '0;30',
        'dark gray' => '1;30',
        'blue'=> '0;34',
        'light blue' => '1;34',
        'green'=> '0;32',
        'light green' => '1;32',
        'cyan'=> '0;36',
        'light cyan' => '1;36',
        'red'=> '0;31',
        'light red' => '1;31',
        'purple'=> '0;35',
        'light purple' => '1;35',
        'brown'=> '0;33',
        'yellow'=> '1;33',
        'light gray' => '0;37',
        'white'=> '1;37'
    );

    static $bgcolors = array(
        'black' => '40',
        'red' => '41',
        'green' => '42',
        'yellow' => '43',
        'blue' => '44',
        'magenta' => '45',
        'cyan' => '46',
        'light gray' => '47'
    );

    if (!isset($fgcolors[$fgcolor]))
        $fgcolor='white';
    if (!isset($bgcolors[$bgcolor]))
        $bgcolor=null;

    if ($fgcolor)
        $out .= "\033[{$fgcolors[$fgcolor]}m";
    if ($bgcolor)
        $out .= "\033[{$bgcolors[$bgcolor]}m";

        $out .= $str . "\033[0m";
    } else {
        $out = $str;
    }

    //ncurses_end();

    return $out;
}


function printLine(string $string, string $type='neutral', bool $return=false) {
    switch ($type) {
        default:
        case 'neutral':
            $header = '';
        break;

        case 'error':
            $header = strColor(' [FAIL] ', 'white', 'red');
        break;

        case 'warning':
            $header = strColor(' [WARN] ', 'black', 'yellow');
        break;

        case 'success':
            $header = strColor(' [ OK ] ', 'black', 'green');
        break;
    }

    $output = sprintf("%s %s\n", $header, $string);

    if ($return === true) {
        return $output;
    } else {
        echo $output;
    }
}
