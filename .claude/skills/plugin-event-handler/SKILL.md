---
name: plugin-event-handler
description: Adds a new static event handler method to src/Plugin.php following the GenericEvent pattern. Generates method signature, PHPDoc, and $event->getSubject() boilerplate. Use when user says 'add hook', 'new event handler', 'add getMenu/getSettings method', or extends plugin behavior. Do NOT use for modifying existing handler logic or editing getHooks() return values.
---
# plugin-event-handler

## Critical

- All handler methods MUST be `public static` — never instance methods.
- Parameter name MUST be `$event`, typed as `GenericEvent` (not `Event` or untyped).
- `use Symfony\Component\EventDispatcher\GenericEvent;` MUST already exist at the top of `src/Plugin.php` — do not duplicate it.
- Every handler MUST have a PHPDoc block with `@param \Symfony\Component\EventDispatcher\GenericEvent $event`.
- Indentation: **tabs only** (per `.scrutinizer.yml`) — never spaces.
- After adding the method, register it in `getHooks()` or the caller is responsible — do not leave the mapping silently missing.
- The test suite asserts an exact list of public methods (`testExpectedPublicMethods`). Adding a new handler will break that test — **update `tests/PluginTest.php` in the same change**.

## Instructions

1. **Read `src/Plugin.php`** to confirm the file structure and that `use Symfony\Component\EventDispatcher\GenericEvent;` is present. Verify before proceeding.

2. **Choose the subject variable name** based on handler type:
   - `getMenu` → `$menu = $event->getSubject();`
   - `getSettings` → `$settings = $event->getSubject();` (add `@var \MyAdmin\Settings $settings` inner docblock)
   - `getRequirements` → `$loader = $event->getSubject();` (add `@var \MyAdmin\Plugins\Loader $this->loader` inner docblock)
   - Custom handler → `$subject = $event->getSubject();`

3. **Add the method** inside the `Plugin` class in `src/Plugin.php`, following this exact template (tabs for indentation):

```php
	/**
	 * @param \Symfony\Component\EventDispatcher\GenericEvent $event
	 */
	public static function myHandlerName(GenericEvent $event)
	{
		$subject = $event->getSubject();
	}
```

   For `getMenu`, wrap the body with the ACL guard:
```php
	public static function getMenu(GenericEvent $event)
	{
		$menu = $event->getSubject();
		if ($GLOBALS['tf']->ima == 'admin') {
			function_requirements('has_acl');
			if (has_acl('client_billing')) {
			}
		}
	}
```

4. **Register the handler** in `getHooks()`. Uncomment the relevant line or add a new entry using `[__CLASS__, 'myHandlerName']`:
```php
public static function getHooks()
{
	return [
		'system.settings' => [__CLASS__, 'getSettings'],
		'ui.menu'         => [__CLASS__, 'getMenu'],
	];
}
```
   Verify the event name string matches the host app's dispatched event before saving.

5. **Update `tests/PluginTest.php`** — add the method name to the `$expected` array in `testExpectedPublicMethods()` and add a signature test:
```php
public function testMyHandlerNameMethodSignature(): void
{
	$this->assertTrue($this->reflector->hasMethod('myHandlerName'));
	$method = $this->reflector->getMethod('myHandlerName');
	$this->assertTrue($method->isStatic());
	$this->assertTrue($method->isPublic());
	$this->assertCount(1, $method->getParameters());
	$param = $method->getParameters()[0];
	$this->assertSame('event', $param->getName());
	$this->assertSame(
		'Symfony\\Component\\EventDispatcher\\GenericEvent',
		$param->getType()->getName()
	);
}
```

6. **Run tests** to confirm nothing is broken:
```bash
vendor/bin/phpunit tests/ -v
```

## Examples

**User says:** "Add a getSettings handler to the plugin."

**Actions taken:**
1. Read `src/Plugin.php` — confirmed `use Symfony\Component\EventDispatcher\GenericEvent;` present.
2. Added method with `@var \MyAdmin\Settings $settings` inner docblock and `$settings = $event->getSubject();`.
3. Uncommented `'system.settings' => [__CLASS__, 'getSettings']` in `getHooks()`.
4. Added `testGetSettingsMethodSignature()` to `tests/PluginTest.php` and added `'getSettings'` to `$expected` in `testExpectedPublicMethods()`.
5. Ran tests — all tests green.

**Result** — `src/Plugin.php` now contains:
```php
	/**
	 * @param \Symfony\Component\EventDispatcher\GenericEvent $event
	 */
	public static function getSettings(GenericEvent $event)
	{
		/**
		 * @var \MyAdmin\Settings $settings
		 **/
		$settings = $event->getSubject();
	}
```

## Common Issues

- **`testExpectedPublicMethods` fails with "arrays differ"**: You added the handler to `src/Plugin.php` but forgot to add its name to `$expected` in `tests/PluginTest.php:315`. Add the method name to both the `$expected` array and add a dedicated signature test.

- **`testAllEventHandlersHaveDocblock` fails**: The new method is missing its PHPDoc block. Every handler method requires at minimum `/** @param \Symfony\Component\EventDispatcher\GenericEvent $event */` immediately above the method signature.

- **Parse error / indent mismatch** from `.scrutinizer.yml` lint: File uses **tabs**. If your editor auto-converted to spaces, run `unexpand --first-only src/Plugin.php` or manually fix indentation.

- **Handler never fires at runtime**: The method was added but `getHooks()` still returns `[]`. Uncomment or add `'event.name' => [__CLASS__, 'myHandlerName']` in `getHooks()`. Confirm the event name string against the host app's `run_event()` call.

- **`Class 'GenericEvent' not found`**: `use Symfony\Component\EventDispatcher\GenericEvent;` is missing or was accidentally removed. It must appear after the `namespace` declaration and before the class definition.
