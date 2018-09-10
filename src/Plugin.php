<?php

namespace Detain\MyAdminGluster;

use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Class Plugin
 *
 * @package Detain\MyAdminGluster
 */
class Plugin
{
	public static $name = 'Gluster Plugin';
	public static $description = 'Allows handling of Gluster based Backups';
	public static $help = '';
	public static $type = 'plugin';

	/**
	 * Plugin constructor.
	 */
	public function __construct()
	{
	}

	/**
	 * @return array
	 */
	public static function getHooks()
	{
		return [
			//'system.settings' => [__CLASS__, 'getSettings'],
			//'ui.menu' => [__CLASS__, 'getMenu'],
		];
	}

	/**
	 * @param \Symfony\Component\EventDispatcher\GenericEvent $event
	 */
	public static function getMenu(GenericEvent $event)
	{
		$menu = $event->getSubject();
		if ($GLOBALS['tf']->ima == 'admin') {
			function_requirements('has_acl');
			if (has_acl('client_billing')) {
				$menu->add_link('admin', 'choice=none.abuse_admin', '/lib/webhostinghub-glyphs-icons/icons/development-16/Black/icon-spam.png', 'Gluster');
			}
		}
	}

	/**
	 * @param \Symfony\Component\EventDispatcher\GenericEvent $event
	 */
	public static function getRequirements(GenericEvent $event)
	{
		$loader = $event->getSubject();
		$loader->add_requirement('class.Gluster', '/../vendor/detain/myadmin-gluster-backups/src/Gluster.php');
		$loader->add_requirement('deactivate_kcare', '/../vendor/detain/myadmin-gluster-backups/src/abuse.inc.php');
		$loader->add_requirement('deactivate_abuse', '/../vendor/detain/myadmin-gluster-backups/src/abuse.inc.php');
		$loader->add_requirement('get_abuse_licenses', '/../vendor/detain/myadmin-gluster-backups/src/abuse.inc.php');
	}

	/**
	 * @param \Symfony\Component\EventDispatcher\GenericEvent $event
	 */
	public static function getSettings(GenericEvent $event)
	{
		$settings = $event->getSubject();
		$settings->add_text_setting('General', 'Gluster', 'abuse_imap_user', 'Gluster IMAP User:', 'Gluster IMAP Username', ABUSE_IMAP_USER);
		$settings->add_text_setting('General', 'Gluster', 'abuse_imap_pass', 'Gluster IMAP Pass:', 'Gluster IMAP Password', ABUSE_IMAP_PASS);
	}
}
