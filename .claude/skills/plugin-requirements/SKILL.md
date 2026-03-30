---
name: plugin-requirements
description: Registers new file or class requirements in getRequirements() inside src/Plugin.php using $loader->add_requirement(). Use when user says 'add requirement', 'register class', 'load file', or adds a new .php or .inc.php to src/. Generates the correct /../vendor/detain/myadmin-gluster-backups/src/ path prefix. Do NOT use for getHooks, getMenu, or getSettings changes.
---
# plugin-requirements

## Critical

- **Path prefix is always** `'/../vendor/detain/myadmin-gluster-backups/src/'` — never omit the leading `/../` or change the vendor path.
- Each exported symbol from a file gets its **own** `add_requirement()` line, even when multiple keys map to the same file.
- Keys for class files use the `class.ClassName` convention. Keys for function files use the function name directly.
- Indentation in `src/Plugin.php` uses **tabs**, not spaces.

## Instructions

1. **Identify the new file** being added under `src/`.  
   Verify the file exists before editing the plugin source.

2. **Determine the requirement key(s)**:  
   - For a class file containing a class named `Foo`: key is `'class.Foo'`  
   - For a procedural include with functions `bar()` and `baz()`: add one line per function — keys are `'bar'` and `'baz'`

3. **Open `src/Plugin.php`** and locate `getRequirements(GenericEvent $event)`. The method body starts with:
   ```php
   $loader = $event->getSubject();
   ```
   Add new line(s) immediately after the existing `add_requirement` calls, before the closing `}`.

4. **Add the line(s)** using this exact format (tab-indented):
   ```php
   		$loader->add_requirement('key', '/../vendor/detain/myadmin-gluster-backups/src/Filename.php');
   ```
   Verify the key and path are correct before saving.

5. **Add a corresponding test assertion** in `tests/PluginTest.php`. Add a new `assertStringContainsString` call inside an existing or new `testSourceReferences*` test method:
   ```php
   $this->assertStringContainsString('class.NewClass', $source);
   $this->assertStringContainsString('myadmin-gluster-backups/src/NewClass.php', $source);
   ```

6. **Run tests** to confirm nothing broke:
   ```bash
   vendor/bin/phpunit
   ```
   All tests must pass before the change is complete.

## Examples

**User says:** "Add a requirement for a new class file in `src/`"

**Actions taken:**
1. Confirm the new class file exists in `src/`.
2. Determine key using `class.ClassName` convention and the appropriate path.
3. Add to `getRequirements()` in `src/Plugin.php`:
```php
public static function getRequirements(GenericEvent $event)
{
	/**
	 * @var \MyAdmin\Plugins\Loader $this->loader
	 */
	$loader = $event->getSubject();
	$loader->add_requirement('class.Gluster', '/../vendor/detain/myadmin-gluster-backups/src/Gluster.php');
	$loader->add_requirement('class.NewClass', '/../vendor/detain/myadmin-gluster-backups/src/NewClass.php');
}
```
4. Add to `tests/PluginTest.php` inside a source-reference test:
```php
$this->assertStringContainsString('class.NewClass', $source);
$this->assertStringContainsString('myadmin-gluster-backups/src/NewClass.php', $source);
```
5. Run `vendor/bin/phpunit` — all tests pass.

**User says:** "Register `restore_backup()` and `list_backups()` from `src/backup.inc.php`"

Add two lines:
```php
	$loader->add_requirement('restore_backup', '/../vendor/detain/myadmin-gluster-backups/src/backup.inc.php');
	$loader->add_requirement('list_backups', '/../vendor/detain/myadmin-gluster-backups/src/backup.inc.php');
```

## Common Issues

- **Test fails with `assertStringContainsString('class.NewClass', $source)` → false**: The key in `add_requirement()` does not match. Check that you used `'class.ClassName'` (dot-separated, matching the class name exactly), not the filename.
- **`vendor/bin/phpunit` reports "No such file or directory" for the added path**: The path prefix `/../vendor/detain/myadmin-gluster-backups/src/` is resolved relative to the host app root, not this package root. Double-check you did not accidentally use a relative path (without the `/../vendor/...` prefix).
- **Spaces instead of tabs cause `.scrutinizer.yml` style failure**: Open `src/Plugin.php` in an editor confirming tab characters, or run `cat -A src/Plugin.php | grep add_requirement` and verify `^I` (tab) appears before `$loader`.
- **Added a class file but used a bare name key (e.g., `'Gluster'` instead of `'class.Gluster'`)**: The loader resolves class-type keys differently. Always prefix with `class.` for files containing a class definition.
