<?php

namespace Huluti\BreadcrumbsBundle\Tests\Service;

use Huluti\BreadcrumbsBundle\Service\BreadcrumbPlaceholderResolver;
use Huluti\BreadcrumbsBundle\Test\Models\TestCategory;
use Huluti\BreadcrumbsBundle\Test\Models\TestProduct;
use Huluti\BreadcrumbsBundle\Test\Models\TestSupplier;
use Huluti\BreadcrumbsBundle\Test\Models\TestUser;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversNothing]
class BreadcrumbPlaceholderResolverTest extends TestCase
{
    private BreadcrumbPlaceholderResolver $resolver;

    protected function setUp(): void
    {
        $this->resolver = new BreadcrumbPlaceholderResolver();
    }

    #[DataProvider('provideResolveTextData')]
    public function testResolveText(string $text, array $arguments, string $expected): void
    {
        $result = $this->resolver->resolveText($text, $arguments);
        $this->assertSame($expected, $result);
    }

    public static function provideResolveTextData(): array
    {
        $user = new TestUser('John', 'Doe', 25);
        $category = new TestCategory('Electronics');
        $product = new TestProduct('Laptop', 999.99, $category);

        return [
            'no placeholders' => [
                'Simple text without placeholders',
                [],
                'Simple text without placeholders',
            ],
            'single placeholder' => [
                'Hello {user.firstName}',
                ['user' => $user],
                'Hello John',
            ],
            'multiple placeholders' => [
                '{user.firstName} {user.lastName} is {user.age} years old',
                ['user' => $user],
                'John Doe is 25 years old',
            ],
            'mixed with text' => [
                'User: {user.firstName}, Product: {product.name}',
                ['user' => $user, 'product' => $product],
                'User: John, Product: Laptop',
            ],
            'nested properties' => [
                '{product.category.name}',
                ['product' => $product],
                'Electronics',
            ],
            'invalid placeholder keeps original' => [
                'Hello {invalid.property}',
                ['user' => $user],
                'Hello {invalid.property}',
            ],
            'empty curly braces' => [
                'Hello {}',
                ['user' => $user],
                'Hello {}',
            ],
            'multiple with some invalid' => [
                '{user.firstName} - {invalid} - {user.lastName}',
                ['user' => $user],
                'John - {invalid} - Doe',
            ],
        ];
    }

    #[DataProvider('provideResolvePlaceholderData')]
    public function testResolvePlaceholder(string $placeholder, array $arguments, ?string $expected): void
    {
        $result = $this->resolver->resolvePlaceholder($placeholder, $arguments);
        $this->assertSame($expected, $result);
    }

    public static function provideResolvePlaceholderData(): array
    {
        $user = new TestUser('John', 'Doe', 25);
        $product = new TestProduct('Laptop', 999.99);

        return [
            'simple property' => [
                '{user.firstName}',
                ['user' => $user],
                'John',
            ],
            'object without property path' => [
                '{user}',
                ['user' => $user],
                'TestUser: John Doe',
            ],
            'numeric value' => [
                '{user.age}',
                ['user' => $user],
                '25',
            ],
            'float value' => [
                '{product.price}',
                ['product' => $product],
                '999.99',
            ],
            'non-existent object' => [
                '{nonexistent.property}',
                ['user' => $user],
                null,
            ],
            'non-existent property' => [
                '{user.nonExistent}',
                ['user' => $user],
                null,
            ],
            'empty placeholder' => [
                '{}',
                ['user' => $user],
                null,
            ],
            'null value' => [
                '{product.nullValue}',
                ['product' => $product],
                null,
            ],
            'trim curly braces' => [
                '{user.firstName}',
                ['user' => $user],
                'John',
            ],
        ];
    }

    #[DataProvider('provideResolveParametersData')]
    public function testResolveParameters(array $parameters, array $arguments, array $expected): void
    {
        $result = $this->resolver->resolveParameters($parameters, $arguments);
        $this->assertSame($expected, $result);
    }

    public static function provideResolveParametersData(): array
    {
        $user = new TestUser('John', 'Doe', 25);
        $product = new TestProduct('Laptop', 999.99);

        return [
            'no parameters' => [
                [],
                ['user' => $user],
                [],
            ],
            'simple parameters' => [
                ['name' => 'John', 'age' => 25],
                ['user' => $user],
                ['name' => 'John', 'age' => 25],
            ],
            'single placeholder parameter' => [
                ['username' => '{user.firstName}'],
                ['user' => $user],
                ['username' => 'John'],
            ],
            'multiple placeholder parameters' => [
                [
                    'firstName' => '{user.firstName}',
                    'lastName' => '{user.lastName}',
                    'product' => '{product.name}',
                ],
                ['user' => $user, 'product' => $product],
                [
                    'firstName' => 'John',
                    'lastName' => 'Doe',
                    'product' => 'Laptop',
                ],
            ],
            'mixed placeholders and regular values' => [
                [
                    'static' => 'static value',
                    'dynamic' => '{user.firstName}',
                    'number' => 42,
                ],
                ['user' => $user],
                [
                    'static' => 'static value',
                    'dynamic' => 'John',
                    'number' => 42,
                ],
            ],
            'invalid placeholder' => [
                ['invalid' => '{invalid.property}'],
                ['user' => $user],
                ['invalid' => '{invalid.property}'],
            ],
            'nested arrays not affected' => [
                [
                    'user' => ['name' => '{user.firstName}'],
                    'simple' => '{user.firstName}',
                ],
                ['user' => $user],
                [
                    'user' => ['name' => '{user.firstName}'],
                    'simple' => 'John',
                ],
            ],
            'string not matching placeholder pattern' => [
                ['text' => 'This is {not a placeholder}'],
                ['user' => $user],
                ['text' => 'This is {not a placeholder}'],
            ],
        ];
    }

    public function testResolveWithComplexObjectGraph(): void
    {
        $category = new TestCategory('Electronics');
        $supplier = new TestSupplier('TechCorp', 'contact@techcorp.com');
        $product = new TestProduct('Laptop', 999.99, $category, $supplier);
        $user = new TestUser('John', 'Doe', 25, $product);

        $text = '{user.firstName} bought {user.favoriteProduct.name} from {user.favoriteProduct.supplier.name}';
        $expected = 'John bought Laptop from TechCorp';

        $result = $this->resolver->resolveText($text, ['user' => $user]);
        $this->assertSame($expected, $result);
    }

    public function testResolveWithNullValues(): void
    {
        $user = new TestUser('John', 'Doe', 25, null);

        $result = $this->resolver->resolveText('Favorite: {user.favoriteProduct.name}', ['user' => $user]);
        $this->assertSame('Favorite: {user.favoriteProduct.name}', $result);
    }

    public function testPropertyAccessorExceptionHandling(): void
    {
        $object = new class {
            public function getThrowsException()
            {
                throw new \RuntimeException('Test exception');
            }
        };

        $result = $this->resolver->resolvePlaceholder('{object.throwsException}', ['object' => $object]);
        $this->assertNull($result);
    }
}
