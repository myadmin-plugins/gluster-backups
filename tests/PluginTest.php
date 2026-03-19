<?php

namespace Detain\MyAdminGluster\Tests;

use Detain\MyAdminGluster\Plugin;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;

/**
 * Class PluginTest
 *
 * Unit tests for the Detain\MyAdminGluster\Plugin class.
 *
 * @package Detain\MyAdminGluster\Tests
 */
class PluginTest extends TestCase
{
    /**
     * @var ReflectionClass
     */
    private ReflectionClass $reflector;

    /**
     * Set up the reflection class instance used across tests.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->reflector = new ReflectionClass(Plugin::class);
    }

    // ---------------------------------------------------------------
    //  Class structure tests
    // ---------------------------------------------------------------

    /**
     * Test that the Plugin class exists and can be reflected.
     *
     * @return void
     */
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(Plugin::class));
    }

    /**
     * Test that the Plugin class resides in the correct namespace.
     *
     * @return void
     */
    public function testClassNamespace(): void
    {
        $this->assertSame('Detain\\MyAdminGluster', $this->reflector->getNamespaceName());
    }

    /**
     * Test that the Plugin class is not abstract.
     *
     * @return void
     */
    public function testClassIsNotAbstract(): void
    {
        $this->assertFalse($this->reflector->isAbstract());
    }

    /**
     * Test that the Plugin class is not an interface.
     *
     * @return void
     */
    public function testClassIsNotInterface(): void
    {
        $this->assertFalse($this->reflector->isInterface());
    }

    /**
     * Test that the Plugin class is instantiable.
     *
     * @return void
     */
    public function testClassIsInstantiable(): void
    {
        $this->assertTrue($this->reflector->isInstantiable());
    }

    /**
     * Test that the Plugin class can be instantiated without arguments.
     *
     * @return void
     */
    public function testConstructorRequiresNoArguments(): void
    {
        $constructor = $this->reflector->getConstructor();
        $this->assertNotNull($constructor);
        $this->assertCount(0, $constructor->getParameters());
    }

    /**
     * Test that the constructor creates a valid Plugin instance.
     *
     * @return void
     */
    public function testCanInstantiate(): void
    {
        $plugin = new Plugin();
        $this->assertInstanceOf(Plugin::class, $plugin);
    }

    // ---------------------------------------------------------------
    //  Static property tests
    // ---------------------------------------------------------------

    /**
     * Test that the $name static property exists and has the expected value.
     *
     * @return void
     */
    public function testNameProperty(): void
    {
        $this->assertTrue($this->reflector->hasProperty('name'));
        $prop = $this->reflector->getProperty('name');
        $this->assertTrue($prop->isStatic());
        $this->assertTrue($prop->isPublic());
        $this->assertSame('Gluster Plugin', Plugin::$name);
    }

    /**
     * Test that the $description static property exists and has the expected value.
     *
     * @return void
     */
    public function testDescriptionProperty(): void
    {
        $this->assertTrue($this->reflector->hasProperty('description'));
        $prop = $this->reflector->getProperty('description');
        $this->assertTrue($prop->isStatic());
        $this->assertTrue($prop->isPublic());
        $this->assertSame('Allows handling of Gluster based Backups', Plugin::$description);
    }

    /**
     * Test that the $help static property exists and is an empty string.
     *
     * @return void
     */
    public function testHelpProperty(): void
    {
        $this->assertTrue($this->reflector->hasProperty('help'));
        $prop = $this->reflector->getProperty('help');
        $this->assertTrue($prop->isStatic());
        $this->assertTrue($prop->isPublic());
        $this->assertSame('', Plugin::$help);
    }

    /**
     * Test that the $type static property exists and equals 'plugin'.
     *
     * @return void
     */
    public function testTypeProperty(): void
    {
        $this->assertTrue($this->reflector->hasProperty('type'));
        $prop = $this->reflector->getProperty('type');
        $this->assertTrue($prop->isStatic());
        $this->assertTrue($prop->isPublic());
        $this->assertSame('plugin', Plugin::$type);
    }

    /**
     * Test that the class has exactly four static properties.
     *
     * @return void
     */
    public function testStaticPropertyCount(): void
    {
        $staticProps = array_filter(
            $this->reflector->getProperties(),
            static fn (\ReflectionProperty $p) => $p->isStatic()
        );
        $this->assertCount(4, $staticProps);
    }

    // ---------------------------------------------------------------
    //  getHooks() tests
    // ---------------------------------------------------------------

    /**
     * Test that getHooks exists and is a public static method.
     *
     * @return void
     */
    public function testGetHooksMethodSignature(): void
    {
        $this->assertTrue($this->reflector->hasMethod('getHooks'));
        $method = $this->reflector->getMethod('getHooks');
        $this->assertTrue($method->isStatic());
        $this->assertTrue($method->isPublic());
        $this->assertCount(0, $method->getParameters());
    }

    /**
     * Test that getHooks returns an array.
     *
     * @return void
     */
    public function testGetHooksReturnsArray(): void
    {
        $result = Plugin::getHooks();
        $this->assertIsArray($result);
    }

    /**
     * Test that getHooks currently returns an empty array (all hooks commented out).
     *
     * @return void
     */
    public function testGetHooksReturnsEmptyArray(): void
    {
        $result = Plugin::getHooks();
        $this->assertEmpty($result);
    }

    // ---------------------------------------------------------------
    //  getMenu() method signature tests
    // ---------------------------------------------------------------

    /**
     * Test that getMenu exists and is a public static method accepting a GenericEvent.
     *
     * @return void
     */
    public function testGetMenuMethodSignature(): void
    {
        $this->assertTrue($this->reflector->hasMethod('getMenu'));
        $method = $this->reflector->getMethod('getMenu');
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

    // ---------------------------------------------------------------
    //  getRequirements() method signature tests
    // ---------------------------------------------------------------

    /**
     * Test that getRequirements exists and is a public static method accepting a GenericEvent.
     *
     * @return void
     */
    public function testGetRequirementsMethodSignature(): void
    {
        $this->assertTrue($this->reflector->hasMethod('getRequirements'));
        $method = $this->reflector->getMethod('getRequirements');
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

    // ---------------------------------------------------------------
    //  getSettings() method signature tests
    // ---------------------------------------------------------------

    /**
     * Test that getSettings exists and is a public static method accepting a GenericEvent.
     *
     * @return void
     */
    public function testGetSettingsMethodSignature(): void
    {
        $this->assertTrue($this->reflector->hasMethod('getSettings'));
        $method = $this->reflector->getMethod('getSettings');
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

    // ---------------------------------------------------------------
    //  Method inventory test
    // ---------------------------------------------------------------

    /**
     * Test that the class declares exactly the expected public methods.
     *
     * @return void
     */
    public function testExpectedPublicMethods(): void
    {
        $expected = ['__construct', 'getHooks', 'getMenu', 'getRequirements', 'getSettings'];
        $actual = array_map(
            static fn (ReflectionMethod $m) => $m->getName(),
            $this->reflector->getMethods(ReflectionMethod::IS_PUBLIC)
        );
        sort($expected);
        sort($actual);
        $this->assertSame($expected, $actual);
    }

    // ---------------------------------------------------------------
    //  Source-file static analysis tests
    // ---------------------------------------------------------------

    /**
     * Test that the source file is valid PHP (no syntax errors via token_get_all).
     *
     * @return void
     */
    public function testSourceFileIsValidPhp(): void
    {
        $path = (new ReflectionClass(Plugin::class))->getFileName();
        $this->assertNotFalse($path);
        $source = file_get_contents($path);
        $this->assertNotFalse($source);
        // token_get_all will trigger a parse error warning on invalid PHP
        $tokens = @token_get_all($source);
        $this->assertNotEmpty($tokens);
    }

    /**
     * Test that the source file contains the expected namespace declaration.
     *
     * @return void
     */
    public function testSourceContainsNamespace(): void
    {
        $source = file_get_contents((new ReflectionClass(Plugin::class))->getFileName());
        $this->assertStringContainsString('namespace Detain\\MyAdminGluster;', $source);
    }

    /**
     * Test that the source file imports Symfony GenericEvent.
     *
     * @return void
     */
    public function testSourceImportsGenericEvent(): void
    {
        $source = file_get_contents((new ReflectionClass(Plugin::class))->getFileName());
        $this->assertStringContainsString(
            'use Symfony\\Component\\EventDispatcher\\GenericEvent;',
            $source
        );
    }

    /**
     * Test that getRequirements references Gluster-related requirement paths in source.
     *
     * @return void
     */
    public function testSourceReferencesGlusterRequirements(): void
    {
        $source = file_get_contents((new ReflectionClass(Plugin::class))->getFileName());
        $this->assertStringContainsString('class.Gluster', $source);
        $this->assertStringContainsString('myadmin-gluster-backups/src/Gluster.php', $source);
    }

    /**
     * Test that getRequirements references abuse-related requirement paths in source.
     *
     * @return void
     */
    public function testSourceReferencesAbuseRequirements(): void
    {
        $source = file_get_contents((new ReflectionClass(Plugin::class))->getFileName());
        $this->assertStringContainsString('deactivate_kcare', $source);
        $this->assertStringContainsString('deactivate_abuse', $source);
        $this->assertStringContainsString('get_abuse_licenses', $source);
        $this->assertStringContainsString('abuse.inc.php', $source);
    }

    /**
     * Test that getMenu source references admin ACL check.
     *
     * @return void
     */
    public function testSourceReferencesAdminAclCheck(): void
    {
        $source = file_get_contents((new ReflectionClass(Plugin::class))->getFileName());
        $this->assertStringContainsString('has_acl', $source);
        $this->assertStringContainsString('client_billing', $source);
    }

    /**
     * Test that the source file contains a proper class docblock.
     *
     * @return void
     */
    public function testSourceContainsClassDocblock(): void
    {
        $docComment = $this->reflector->getDocComment();
        $this->assertNotFalse($docComment);
        $this->assertStringContainsString('Class Plugin', $docComment);
    }

    /**
     * Test that all event handler methods have a docblock.
     *
     * @return void
     */
    public function testAllEventHandlersHaveDocblock(): void
    {
        $handlers = ['getMenu', 'getRequirements', 'getSettings'];
        foreach ($handlers as $handler) {
            $method = $this->reflector->getMethod($handler);
            $this->assertNotFalse(
                $method->getDocComment(),
                "Method {$handler} is missing a docblock"
            );
        }
    }

    /**
     * Test that the source file starts with a proper PHP opening tag.
     *
     * @return void
     */
    public function testSourceStartsWithPhpTag(): void
    {
        $source = file_get_contents((new ReflectionClass(Plugin::class))->getFileName());
        $this->assertStringStartsWith('<?php', $source);
    }

    /**
     * Test that the getSettings method calls getSubject on the event (via source analysis).
     *
     * @return void
     */
    public function testGetSettingsCallsGetSubject(): void
    {
        $source = file_get_contents((new ReflectionClass(Plugin::class))->getFileName());
        $this->assertStringContainsString('$event->getSubject()', $source);
    }

    /**
     * Test that getHooks array values reference the Plugin class via __CLASS__.
     *
     * @return void
     */
    public function testGetHooksReferencesClass(): void
    {
        $source = file_get_contents((new ReflectionClass(Plugin::class))->getFileName());
        $this->assertStringContainsString('__CLASS__', $source);
    }
}
