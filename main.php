<?php
/** 
 * Script principal
 * 
 * Este sencillo programa puede realizar un web scraping básico mediante 
 * el uso de ficheros JSON con indicaciones de los elementos a recuperar,
 * denominados "recetas". Todo lo obtenido, será almacenado en un CSV.
 * 
 * @category Main file
 * @package  Eliscraper
 * @author   José González <josegs84@gmail.com>
 * @license  GNU General Public License version 3
 * @version  1.0.1
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

// Fichero que carga la configuración de la web a capturar
$scrapSettings  = json_decode(file_get_contents("recetas/powerplanetmoviles.json"));
// Elementos de configuración
$localMode      = $scrapSettings->localmode ?? false;
$urlToScrap     = $scrapSettings->url;
$outputScrap    = $scrapSettings->filehandle . ".txt";
$inputCSV       = $scrapSettings->filehandle . ".csv";
// Solo obtenemos la información de internet si no estamos en modo local.
if ($localMode !== true) {
    $dataOrigin = curl_init($urlToScrap);
    $fileScraped = fopen($outputScrap, "w");
    
    curl_setopt($dataOrigin, CURLOPT_FILE, $fileScraped);
    curl_setopt($dataOrigin, CURLOPT_HEADER, 0);
    
    curl_exec($dataOrigin);
    curl_close($dataOrigin);
    
    fclose($fileScraped);
}

// Abrir el fichero y empezar a sacar elementos
$fileScraped = fopen($outputScrap, 'r') or die("No se ha podido abrir el fichero");
// Ficha de producto y sus elementos principales
$productContent = $scrapSettings->product->tab;
$subContents = $productContent->tagCapture;
//
$principalTag = sprintf('<%s',$productContent->tagType);
$endPrincipalTag = sprintf('</%s>',$productContent->tagType);
$principalClass = sprintf('class="%s"', $productContent->tagClass);
$gotchaElements = [];
$cnt = 0;
// Comienza la fiesta.
while (!feof($fileScraped)) {
    // Para la iteracion si se acaba el fichero (creo que ya sobra).
    if (feof($fileScraped)) break;
    // Captura la linea que toque, limpiando espacios en blanco.
    $lineContent = trim(fgets($fileScraped));

    if (preg_match("/$principalTag/", $lineContent) !== false && preg_match("/$principalClass/", $lineContent) !== false) {
        // Tenemos un nuevo item!
        $cnt++;
        $gotchaElements[$cnt] = [];
        while(!feof($fileScraped)) {
            // Seguimos capturando líneas.
            $subLineContent = trim(fgets($fileScraped));
            // Si llegamos a la etiqueta de cierre del elemento que estemos viendo, se termina el item.
            if (strpos($subLineContent, $endPrincipalTag) !== false) break;
            // Comprobamos si las lineas que van saliendo coinciden con alguno de los elementos que queremos capturar.
            foreach ($subContents as $classForEvaluate => $data) {
                $dataForEval = sprintf('class="%s"', $classForEvaluate);
                if (strpos($subLineContent, $dataForEval) !== false) {
                    if (empty($data->childCapture) === true) {
                        // No se ha definido ningun punto de captura, por lo que obtiene el contenido del tag.
                        $gotchaElements[$cnt][$data->childType] = strip_tags($subLineContent);
                    } else {
                        // Se busca el atributo definido y se obtiene el valor del mismo.
                        $tmpContentToSave = strstr($subLineContent, $data->childCapture);
                        $tmpContentToSave = strstr($tmpContentToSave, "\"");
                        $gotchaElements[$cnt][$data->childType] = strstr(substr($tmpContentToSave, 1), "\" ", true);
                    }
                }
            }
        }
    }
}
fclose($fileScraped);
// Exportar los datos a un fichero CSV.
$fileCSV = fopen($inputCSV, 'a') or die('Error al crear el fichero CSV de salida');
foreach ($gotchaElements as $values) {
    fputcsv($fileCSV, $values);
}
fclose($fileCSV);
?>