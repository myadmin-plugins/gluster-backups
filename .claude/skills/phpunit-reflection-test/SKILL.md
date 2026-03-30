---
name: phpunit-reflection-test
description: Writes structural PHPUnit tests in `tests/PluginTest.php` using `ReflectionClass` to assert class shape, static properties, method signatures, and parameter types. Also generates source-content assertions via `file_get_contents()`. Use when user says 'add test', 'test the plugin', 'write PHPUnit for Plugin', or adds a method to `src/Plugin.php`. Do NOT use for behavioral/integration tests or mocking event dispatch.
---
# phpunit-reflection-test

## Critical

- **Never mock `GenericEvent`** — these are structural/static tests only; no event dispatch occurs.
- **Namespace for test file:** `Detain\MyAdminGluster\Tests` — must match `composer.json` autoload-dev.
- **Indentation:** tabs only (enforced by `.scrutinizer.yml`).
- All new test methods must be `public function testXxx(): void` with a docblock.
- Run `phpunit` to verify before considering done.

## Instructions

1. **Read `src/Plugin.php`** to identify all public static methods and static property names/values. Verify the class namespace is `Detain\MyAdminGluster` before proceeding.

2. **Open `tests/PluginTest.php`**. Confirm it has these imports and the shared `$reflector` property:
   ```php
   use Detain\MyAdminGluster\Plugin;
   use PHPUnit\Framework\TestCase;
   use ReflectionClass;
   use ReflectionMethod;

   private ReflectionClass $reflector;

   protected function setUp(): void {
       parent::setUp();
       $this->reflector = new ReflectionClass(Plugin::class);
   }
   ```

3. **For each new `public static` method added to `Plugin.php`**, add two test blocks:

   **3a. Method signature test** — verifies the method is public, static, has the right parameter name and type:
   ```php
   public function testGetFooMethodSignature(): void
   {
       $this->assertTrue($this->reflector->hasMethod('getFoo'));
       $method = $this->reflector->getMethod('getFoo');
       $this->assertTrue($method->isStatic());
       $this->assertTrue($method->isPublic());
       $this->assertCount(1, $method->getParameters());

       $param = $method->getParameters()[0];
       $this->assertSame('event', $param->getName());
       $this->assertNotNull($param->getType());
       $this->assertSame(
           'Symfony\\Component\\EventDispatcher\\GenericEvent',
           $param->getType()->getName()
       );
   }
   ```

   **3b. Source-content test** — verifies key strings appear in the source file:
   ```php
   public function testSourceReferencesFoo(): void
   {
       $source = file_get_contents((new ReflectionClass(Plugin::class))->getFileName());
       $this->assertStringContainsString('someExpectedString', $source);
   }
   ```
   Use this for: requirement keys (`class.Gluster`), file paths (`backup.inc.php`), ACL strings (`has_acl`, `client_billing`), method calls (`$event->getSubject()`).

4. **Update `testExpectedPublicMethods()`** — add the new method name to `$expected`:
   ```php
   $expected = ['__construct', 'getHooks', 'getFoo', 'getMenu', 'getRequirements', 'getSettings'];
   ```
   Verify the sorted assertion still passes.

5. **For new static properties**, add a property test following this exact pattern:
   ```php
   public function testFooProperty(): void
   {
       $this->assertTrue($this->reflector->hasProperty('foo'));
       $prop = $this->reflector->getProperty('foo');
       $this->assertTrue($prop->isStatic());
       $this->assertTrue($prop->isPublic());
       $this->assertSame('expected value', Plugin::$foo);
   }
   ```
   Then increment the count in `testStaticPropertyCount()`.

6. **Run tests:** `phpunit` — all tests must be green before finishing.

## Examples

**User says:** "Add a `getSettings` method to `Plugin.php` that calls `$event->getSubject()`"

**Actions taken:**
1. Read `src/Plugin.php` — confirms `getSettings(GenericEvent $event)` is `public static`.
2. Add to `tests/PluginTest.php`:
   - `testGetSettingsMethodSignature()` — asserts static, public, 1 param named `event`, typed `GenericEvent`.
   - `testGetSettingsCallsGetSubject()` — asserts `$event->getSubject()` appears in source.
3. Add `'getSettings'` to `$expected` in `testExpectedPublicMethods()`.
4. Run `phpunit` — green.

**Result:** New method covered structurally without any live object instantiation or event dispatch.

## Common Issues

- **`ReflectionException: Class ... does not exist`** — autoload not resolving. Run `composer dump-autoload` and confirm `composer.json` has `"Detain\\MyAdminGluster\\Tests\\" : "tests/"` under `autoload-dev`.
- **`getType()->getName()` returns null / fatal**  — PHP < 7.4 or untyped param. Confirm `src/Plugin.php` has `GenericEvent $event` as the type hint, not a bare `$event`.
- **`testExpectedPublicMethods` fails after adding a method** — you forgot to add the new method name to the `$expected` array. Also check for inherited methods: `getMethods(ReflectionMethod::IS_PUBLIC)` includes inherited ones; the test uses `$this->reflector->getMethods()` scoped to declared methods only.
- **`assertStringContainsString` fails on backslashes** — PHP string escaping. Use `'Symfony\\Component\\EventDispatcher\\GenericEvent'` (four backslashes) in test source to match two literal backslashes in the file.
- **`testStaticPropertyCount` fails** — you added a property but forgot to update `assertCount(N, $staticProps)`. Count all `public static $` declarations in `src/Plugin.php` and set N accordingly.
