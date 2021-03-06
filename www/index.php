<?php
use APF\core\benchmark\BenchmarkTimer;
use APF\core\configuration\ConfigurationManager;
use APF\core\configuration\provider\ini\IniConfigurationProvider;
use APF\core\filter\ChainedUrlRewritingInputFilter;
use APF\core\filter\ChainedUrlRewritingOutputFilter;
use APF\core\filter\InputFilterChain;
use APF\core\filter\OutputFilterChain;
use APF\core\frontcontroller\Frontcontroller;
use APF\core\loader\RootClassLoader;
use APF\core\loader\StandardClassLoader;
use APF\core\logging\Logger;
use APF\core\registry\Registry;
use APF\core\singleton\Singleton;
use APF\tools\link\LinkGenerator;
use APF\tools\link\RewriteLinkScheme;
use DOCS\pres\filter\output\ScriptletOutputFilter;

date_default_timezone_set('Europe/Berlin');
ob_start();

// pre-define the root path of the root class loader (if necessary)
$dir = dirname(dirname($_SERVER['SCRIPT_FILENAME']));
$apfClassLoaderRootPath = $dir . '/APF';
$apfClassLoaderConfigurationRootPath = $dir . '/config/APF';
include('../APF/core/bootstrap.php');

// Define class loader for documentation page resources
RootClassLoader::addLoader(new StandardClassLoader('DOCS', $dir . '/DOCS', $dir . '/config/DOCS'));

/* @var $iniProvider IniConfigurationProvider */
$iniProvider = ConfigurationManager::retrieveProvider('ini');
$iniProvider->setOmitConfigSubFolder(true);
$iniProvider->setOmitContext(true);

/* @var $l Logger */
$l = & Singleton::getInstance('APF\core\logging\Logger');
$l->setLogThreshold(Logger::$LOGGER_THRESHOLD_ALL);

// configure logger for database debug messages
$defaultWriter = $l->getLogWriter(
      Registry::retrieve('APF\core', 'InternalLogTarget')
);
$l->addLogWriter('mysqlx', clone $defaultWriter);
$l->addLogWriter('mysqli', clone $defaultWriter);
$l->addLogWriter('searchlog', clone $defaultWriter);

// configure url rewriting feature
// 1. input and output filter

InputFilterChain::getInstance()->prependFilter(new ChainedUrlRewritingInputFilter());

OutputFilterChain::getInstance()->appendFilter(new ChainedUrlRewritingOutputFilter());

// 2. link scheme

LinkGenerator::setLinkScheme(new RewriteLinkScheme());

// configure page values
Registry::register('DOCS', 'Releases.LocalDir', 'C:\Users\Christian\Entwicklung\Build\RELEASES');
Registry::register('DOCS', 'Releases.BaseURL', 'http://files.adventure-php-framework.org');
Registry::register('DOCS', 'ForumBaseURL', 'http://forum.adventure-php-framework.org');
Registry::register('DOCS', 'WikiBaseURL', 'http://wiki.adventure-php-framework.org');
Registry::register('DOCS', 'TrackerBaseURL', 'http://tracker.adventure-php-framework.org');

// special script kiddie error handler ;)

//GlobalErrorHandler::registerErrorHandler(new LiveErrorHandler());

//GlobalExceptionHandler::registerExceptionHandler(new LiveExceptionHandler());

// special output filter
OutputFilterChain::getInstance()->appendFilter(new ScriptletOutputFilter());

// send HTTP caching headers
//HttpCacheManager::sendHtmlCacheHeaders();

$fC = Singleton::getInstance('APF\core\frontcontroller\Frontcontroller');
/* @var $fC Frontcontroller */
$fC->setContext(null);
$fC->setLanguage('de');

$fC->registerAction('DOCS\biz', 'setModel');

echo $fC->start('DOCS\pres\templates', 'main');

// display benchmark report on demand
/* @var $t BenchmarkTimer */
$t = Singleton::getInstance('APF\core\benchmark\BenchmarkTimer');
if (isset($_REQUEST['benchmarkreport']) && $_REQUEST['benchmarkreport'] == 'true') {
   echo $t->createReport();
}
echo '<!-- rendering time: ' . $t->getTotalTime() . 's -->';

ob_end_flush();