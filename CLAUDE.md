# MyAdmin Gluster Backups Plugin

## Overview
PHP plugin (type `myadmin-plugin`) for GlusterFS backup management. Integrates with MyAdmin via Symfony EventDispatcher hooks.

## Commands
```bash
composer install
vendor/bin/phpunit                                                    # all tests
vendor/bin/phpunit tests/ -v                                          # verbose
```

## Architecture
- **Plugin class**: `src/Plugin.php` · namespace `Detain\MyAdminGluster` · all methods `public static`
- **Tests**: `tests/PluginTest.php` · namespace `Detain\MyAdminGluster\Tests`
- **Autoload**: `composer.json` PSR-4 `Detain\MyAdminGluster\` → `src/`; dev `Detain\MyAdminGluster\Tests\` → `tests/`
- **CI/CD**: `.github/` contains workflows for automated testing and deployment pipelines · `.scrutinizer.yml` · `.travis.yml` · `.codeclimate.yml` · `.bettercodehub.yml`
- **IDE config**: `.idea/` contains inspectionProfiles, deployment.xml, and encodings.xml for JetBrains IDE settings
- **PHPUnit config**: `phpunit.xml.dist`

## Plugin Class Pattern (`src/Plugin.php`)
Static properties: `$name`, `$description`, `$help`, `$type = 'plugin'`

All event handlers are `public static` and accept `GenericEvent $event`:
- `getHooks()` → returns `[]` array of event-name → handler mappings
- `getMenu(GenericEvent $event)` → injects admin menu items; guard with `$GLOBALS['tf']->ima == 'admin'` + `has_acl('client_billing')`
- `getRequirements(GenericEvent $event)` → calls `$loader->add_requirement('key', '/path/to/src/File.php')`
- `getSettings(GenericEvent $event)` → configures settings via `$event->getSubject()`

Required import: `use Symfony\Component\EventDispatcher\GenericEvent;`

## Conventions
- Indentation: **tabs** (per `.scrutinizer.yml`)
- camelCase properties and parameters
- ACL guard pattern:
```php
if ($GLOBALS['tf']->ima == 'admin') {
    function_requirements('has_acl');
    if (has_acl('client_billing')) {
        // menu/action
    }
}
```

## Testing Conventions (`tests/PluginTest.php`)
- Use `ReflectionClass` for structural assertions (namespace, static props, method signatures)
- Verify parameter types via `$param->getType()->getName()` against `Symfony\Component\EventDispatcher\GenericEvent`
- Test source file content: `file_get_contents((new ReflectionClass(Plugin::class))->getFileName())`
- Assert `assertStringContainsString()` for key strings like `class.Gluster`, `abuse.inc.php`, `has_acl`

Run the full test suite:
```bash
vendor/bin/phpunit --configuration phpunit.xml.dist
```

<!-- caliber:managed:pre-commit -->
## Before Committing

**IMPORTANT:** Before every git commit, you MUST ensure Caliber syncs agent configs with the latest code changes.

First, check if the pre-commit hook is already installed:
```bash
grep -q "caliber" .git/hooks/pre-commit 2>/dev/null && echo "hook-active" || echo "no-hook"
```

- If **hook-active**: the hook handles sync automatically — just commit normally. Tell the user: "Caliber will sync your agent configs automatically via the pre-commit hook."
- If **no-hook**: run Caliber manually before committing:
  1. Tell the user: "Caliber: Syncing agent configs with your latest changes..."
  2. Run: `caliber refresh && git add CLAUDE.md .claude/ .cursor/ .cursorrules .github/copilot-instructions.md .github/instructions/ AGENTS.md CALIBER_LEARNINGS.md 2>/dev/null`
  3. After it completes, briefly tell the user what Caliber updated. Then proceed with the commit.

If `caliber` is not found, tell the user: "This project uses Caliber for agent config sync. Run /setup-caliber to get set up."
<!-- /caliber:managed:pre-commit -->

<!-- caliber:managed:learnings -->
## Session Learnings

Read `CALIBER_LEARNINGS.md` for patterns and anti-patterns learned from previous sessions.
These are auto-extracted from real tool usage — treat them as project-specific rules.
<!-- /caliber:managed:learnings -->
