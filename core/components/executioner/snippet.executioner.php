<?php
/**
 * The Executioner
 *
 * @author Jason Coward <jason@modx.com>
 *
 * @var modX $modx
 * @var array $scriptProperties
 */
$output = '';
$tElementClass = !empty($tElementClass) ? $tElementClass : 'modSnippet';

$tStart = microtime(true);
if (!empty($tElement)) {
    $tElementObj = $modx->parser->getElement($tElementClass, $tElement);
    if ($tElementObj && ($tElementObj instanceof modElement || $tElementObj instanceof \MODX\Revolution\modElement)) {
        $tElementObj->setCacheable(false);
        $output = $tElementObj->process($scriptProperties);
    } else {
        $modx->log(modX::LOG_LEVEL_ERROR, "{$tElementClass}: {$tElement} is not a valid MODX Element");
    }
}
$tEnd = microtime(true);

$modx->log(modX::LOG_LEVEL_ERROR, "{$tElementClass}: {$tElement} executed in " . sprintf("%2.4f s", $tEnd - $tStart));
$modx->log(modX::LOG_LEVEL_INFO, "{$tElementClass}: {$tElement} executed with properties: " . print_r($scriptProperties, true));

return $output;
