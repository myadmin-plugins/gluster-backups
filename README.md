# MyAdmin Gluster Backups Plugin

[![Build Status](https://github.com/detain/myadmin-gluster-backups/actions/workflows/tests.yml/badge.svg)](https://github.com/detain/myadmin-gluster-backups/actions/workflows/tests.yml)
[![Latest Stable Version](https://poser.pugx.org/detain/myadmin-gluster-backups/version)](https://packagist.org/packages/detain/myadmin-gluster-backups)
[![Total Downloads](https://poser.pugx.org/detain/myadmin-gluster-backups/downloads)](https://packagist.org/packages/detain/myadmin-gluster-backups)
[![License](https://poser.pugx.org/detain/myadmin-gluster-backups/license)](https://packagist.org/packages/detain/myadmin-gluster-backups)

A MyAdmin plugin for managing GlusterFS-based backup services. This plugin integrates with the MyAdmin control panel to provide Gluster storage provisioning, monitoring, and lifecycle management through the Symfony EventDispatcher hook system.

## Features

- Registers Gluster backup service requirements with the MyAdmin plugin loader
- Provides event-driven menu integration for admin panels
- Supports ACL-based access control for billing management
- Pluggable settings interface via Symfony GenericEvent hooks

## Installation

```sh
composer require detain/myadmin-gluster-backups
```

## Usage

The plugin auto-registers with MyAdmin through the plugin installer. Event hooks are defined in `Plugin::getHooks()` and are wired into the Symfony EventDispatcher by the MyAdmin core.

## Testing

```sh
composer install
vendor/bin/phpunit
```

## License

Licensed under the LGPL-2.1. See [LICENSE](https://www.gnu.org/licenses/old-licenses/lgpl-2.1.html) for details.
