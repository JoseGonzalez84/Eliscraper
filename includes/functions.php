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

    global $appVersion;
    
    $output = "================================================================================\n";
    $output .= "_____ ____   ___    _______________     ___   ____ _____  ____   \n";
    $output .= "| ___\\\\  |   | |   / ___|  ___/  _ \   / _ \ | __ \| ___\/  _ \  \n";
    $output .= "| |___ | |   | |  / /__ | |   | |_\ \ / / \ \| |/ /| |_  | |_\ \ \n";
    $output .= "| ___| | |   | |  \___ \| |   |  _  / | |_| ||  _/ | __| |  _  / \n";
    $output .= "| |___ | |__ | |  ___/ /| |___| | \ \ |  _  || |   | |___| | \ \ \n";
    $output .= "|____| |____\\\\_| |____/ |_____|_|  \_\|_| |_||_|   |_____|_|  \_\\\n";
    $output .= "================================================================================\n";
    $output .= "v{$appVersion}";
    $output .= "\n";
    $output .= "Usage:\n";
    $output .= "--------------------------------------------------------------------------------\n";
    $output .= " $ php main.php <param:value>\n";
    $output .= "--------------------------------------------------------------------------------\n";
    $output .= " · -h        : Show this help screen.\n";
    $output .= " · -o <type> : REQUIRED. Output type. Type can be `json`, `csv` or `db`.\n";
    $output .= " · -r <path> : REQUIRED. Recipe file. Name of recipe file in `recetas` folder.\n";
    $output .= "\n";
    $output .= "Example\n";
    $output .= "--------------------------------------------------------------------------------\n";
    $output .= " $ php main.php -o csv -r powerplanetmoviles\n";
    $output .= "--------------------------------------------------------------------------------\n";
    $output .= "\n";

    echo $output;
}

function buildOutput(string $type, string $file, array $content) {

    switch ($type) {
        case 'json':
        
        break;
        case 'db':
        
        break;
        default:
        case 'csv':
            $output = fopen(sprintf("results/%s_%d.csv",$file,time()), 'a') or die(printLine('Error al crear el fichero CSV de salida', 'error'));
            foreach ($content as $values) {
                fputcsv($output, $values);
            }

            if (fclose($output) === true) {
                printLine('Grabación de fichero exitosa.', 'success');
            }

        break;
    }
}


/**
 * Manages the arguments received
 * 
 * @param array   $arguments  ARGV received.
 * 
 * @return object $parameters Object with the parameters handled.
 */
function handleArguments(array $arguments) {
    $parameters = new stdClass;
    for ($i=1; $i < count($arguments) ; $i++) { 
        if ($i % 2 !== 0) {
            switch (strtolower($arguments[$i])) {
                case "-o":
                    // -o: Output type
                    $i++;
                    if (empty($arguments[$i]) === false) {
                        $parameters->outputType = $arguments[$i];
                        printLine(sprintf('Se ha elegido salida %s', strtoupper($parameters->outputType)), 'info', 5);
                    } else {
                        printLine('No se ha definido una salida. Revise el parámetro.', 'error', 1);
                    }
                break;
                case "-r":
                    // -r: Recipe file
                    $i++;
                    if (empty($arguments[$i]) === false) {
                        $parameters->recipe = $arguments[$i];
                        printLine(sprintf('Se va a cargar la receta %s', $parameters->recipe), 'info', 5);    
                    } else {
                        printLine('No se ha indicado una receta correcta. Revise el parámetro.', 'error', 1);
                        exit(20);
                    }
                break;
                case "-h":
                    // -h: Show help
                    showHelp();
                    exit(0);
                break;
                default:
                    // The parameter does not exists.
                    if (preg_match('/(\-{1}[A-z])/', $arguments[$i]) !== false) {
                        $errorOutput = 'Alguno de los parámetros no fueron correctamente informados.';
                    } else {
                        $errorOutput = sprintf('Parámetro incorrecto [%s]', $arguments[$i]);
                    }
                    printLine($errorOutput, 'error', 1);
                    showHelp();
                    exit(10);         
                break;
            }
        }
    }

    return $parameters;
}


/**
 * Checks if defined type by user is allowed
 * 
 * @param string $type Output type.
 * 
 * @return boolean True if is allowed.
 */
function checkAllowedOutputTypes(string $type){
    $allowedTypes = [
        'json', 'db', 'csv'
    ];
    
    // Not allowed by default.
    $allowed = false;

    if (empty($type) === true) {
        printLine('No se ha indicado un tipo de fichero.', 'warning');
    } else if (in_array($type, $allowedTypes) === false) {
        printLine('No se ha indicado un tipo de fichero válido.', 'warning');
    } else {
        $allowed = true;
    }

    return $allowed;
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

    return $out;
}

/**
 * Prints a formatted line
 * 
 * @param string  $string String to show.
 * @param string  $type   Type of message.
 * @param integer $level  Level of verbosity. Higher is more verbosity. 1 lowest 5 higher.
 * @param string  $return If true, the string is returned.
 * 
 * @return string
 */
function printLine(string $string, string $type='neutral', int $level=3, bool $return=false) {

    global $appConfiguration;

    // If the configuration file not load properly, the messages will be displayed anyway.
    if (empty($appConfiguration) === true) {
        $appConfiguration->main->verbosity = 5;
    }

    // If the level of this message is above configured verbosity, do not display it.
    if ($appConfiguration->main->verbosity < $level) {
        $output = '';
    } else {
        switch ($type) {
            case 'error':
                $header = strColor(' [FAIL] ', 'white', 'red');
            break;
    
            case 'warning':
                $header = strColor(' [WARN] ', 'black', 'yellow');
            break;
    
            case 'success':
                $header = strColor(' [ OK ] ', 'black', 'green');
            break;
            
            case 'neutral':
            default:
                $header = '';
            break;
        }
    
        $output = sprintf("%s %s\n", $header, $string);
    }

    if ($return === true) {
        return $output;
    } else {
        echo $output;
    }
}
