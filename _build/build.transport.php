<?php
$tstart = microtime(true);
set_time_limit(0);

/* define sources */
$root = dirname(dirname(__FILE__)) . '/';
$sources= array (
    'root' => $root,
    'build' => $root . '_build/',
    'lexicon' => $root . '_build/lexicon/',
    'source_core' => $root . 'core/components/executioner',
);
unset($root);

/* instantiate MODx */
require_once $sources['build'].'build.config.php';
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';
$modx= new modX();
$modx->initialize('mgr');
$modx->setLogLevel(xPDO::LOG_LEVEL_INFO);
$modx->setLogTarget(XPDO_CLI_MODE ? 'ECHO' : 'HTML');

/* set package info */
define('PKG_NAME', 'executioner');
define('PKG_VERSION', '1.0.0');
define('PKG_RELEASE', 'pl');

/* load builder */
$modx->loadClass('transport.modPackageBuilder','',false, true);
$builder = new modPackageBuilder($modx);
$builder->createPackage(PKG_NAME, PKG_VERSION, PKG_RELEASE);

/* create snippet object */
$modx->log(xPDO::LOG_LEVEL_INFO,'Adding in snippet.');
$snippet= $modx->newObject('modSnippet');
$snippet->set('name', 'Executioner');
$snippet->set('description', 'The Executioner provides data about the processing of a particular Element');
$snippet->set('category', 0);
$snippet->set('snippet', file_get_contents($sources['source_core'] . '/snippet.executioner.php'));
$properties = include $sources['build'].'properties.inc.php';
$snippet->setProperties($properties);
unset($properties);


/* create a transport vehicle for the data object */
$vehicle = $builder->createVehicle($snippet,array(
    xPDOTransport::PRESERVE_KEYS => false,
    xPDOTransport::UPDATE_OBJECT => true,
    xPDOTransport::UNIQUE_KEY => 'name',
));
$vehicle->resolve('file',array(
    'source' => $sources['source_core'],
    'target' => "return MODX_CORE_PATH . 'components/';",
));
$builder->putVehicle($vehicle);

/* now pack in the license file, readme and setup options */
$builder->setPackageAttributes(array(
    'license' => file_get_contents($sources['source_core'] . '/docs/UNLICENSE'),
    'readme' => file_get_contents($sources['source_core'] . '/docs/README'),
    'changelog' => file_get_contents($sources['source_core'] . '/docs/CHANGELOG'),
));

/* zip up the package */
$builder->pack();

$tend= microtime(true);
$totalTime= ($tend - $tstart);
$totalTime= sprintf("%2.4f s", $totalTime);

$modx->log(xPDO::LOG_LEVEL_INFO, "Package Built.");
$modx->log(xPDO::LOG_LEVEL_INFO, "Execution time: {$totalTime}");
exit();
