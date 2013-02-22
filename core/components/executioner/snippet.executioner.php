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
    switch ($tElementClass) {
        case 'modChunk':
            $output = $modx->getChunk($tElement, $scriptProperties);
            break;
        case 'modSnippet':
            $output = $modx->runSnippet($tElement, $scriptProperties);
            break;
        default:
            $tElementObj = $modx->getObject($tElementClass, array('name' => $tElement));
            if ($tElementObj && $tElementObj instanceof modElement) {
                $tElementObj->setCacheable(false);
                $output = $tElementObj->process($scriptProperties);
            } else {
                $modx->log(modX::LOG_LEVEL_ERROR, "{$tElementClass}: {$tElement} is not a valid MODx Element");
            }
            break;
    }
}
$tEnd = microtime(true);

$modx->log(modX::LOG_LEVEL_ERROR, "{$tElementClass}: {$tElement} executed in " . sprintf("%2.4f s", $tEnd - $tStart));

return $output;
