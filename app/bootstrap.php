<?php

require __DIR__ . '/../vendor/autoload.php';

$configurator = new Nette\Configurator;

//$configurator->setDebugMode('23.75.345.200'); // enable for your remote IP
$configurator->enableTracy(__DIR__ . '/../log');

$configurator->setTimeZone('Europe/Prague');
$configurator->setTempDirectory(__DIR__ . '/../temp');

$configurator->createRobotLoader()
	->addDirectory(__DIR__)
	->register();

define('__MAIL_DIR__', __DIR__ . '/templates/');
define('__CACHE_DIR__', __DIR__ . '/../temp/cache/');
define('__FONTS_DIR__', __DIR__ . '/../files/fonts/');
define('__QRCODES_DIR__', __DIR__ . '/../files/QRCodes/');
define('__INVITATIONS_DIR__', __DIR__ . '/../files/invitations/');
define('__INVITATION_BACKGROUNDS_DIR__', __DIR__ . '/../files/invitationBackgrounds/');
define('__TICKETS_DIR__', __DIR__ . '/../files/tickets/');
define('__TICKET_BACKGROUNDS_DIR__', __DIR__ . '/../files/ticketBackgrounds/');

$configurator->addConfig(__DIR__ . '/config/config.neon');
$configurator->addConfig(__DIR__ . '/config/config.local.neon');

$container = $configurator->createContainer();

return $container;
