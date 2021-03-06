<?php

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Tests\Fixer\Casing;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author SpacePossum
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Casing\ConstantCaseFixer
 */
final class ConstantCaseFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideLowerGeneratedCases
     */
    public function testFixLowerGeneratedCases($expected, $input = null)
    {
        $this->fixer->configure(['case' => 'lower']);
        $this->doTest($expected, $input);
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideUpperGeneratedCases
     */
    public function testFixUpperGeneratedCases($expected, $input = null)
    {
        $this->fixer->configure(['case' => 'upper']);
        $this->doTest($expected, $input);
    }

    public function provideLowerGeneratedCases()
    {
        $cases = [];
        foreach (['true', 'false', 'null'] as $case) {
            $cases[] = [
                sprintf('<?php $x = %s;', $case),
                sprintf('<?php $x = %s;', strtoupper($case)),
            ];

            $cases[] = [
                sprintf('<?php $x = %s;', $case),
                sprintf('<?php $x = %s;', ucfirst($case)),
            ];

            $cases[] = [sprintf('<?php $x = new %s;', ucfirst($case))];
            $cases[] = [sprintf('<?php $x = new %s;', strtoupper($case))];
            $cases[] = [sprintf('<?php $x = "%s story";', $case)];
            $cases[] = [sprintf('<?php $x = "%s";', $case)];
        }

        return $cases;
    }

    public function provideUpperGeneratedCases()
    {
        $cases = [];
        foreach (['true', 'false', 'null'] as $case) {
            $cases[] = [
                sprintf('<?php $x = %s;', strtoupper($case)),
                sprintf('<?php $x = %s;', $case),
            ];

            $cases[] = [
                sprintf('<?php $x = %s;', strtoupper($case)),
                sprintf('<?php $x = %s;', ucfirst($case)),
            ];

            $cases[] = [sprintf('<?php $x = new %s;', ucfirst($case))];
            $cases[] = [sprintf('<?php $x = new %s;', strtoupper($case))];
            $cases[] = [sprintf('<?php $x = "%s story";', $case)];
            $cases[] = [sprintf('<?php $x = "%s";', $case)];
        }

        return $cases;
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases()
    {
        return [
            [
                '<?php if (true) if (false) if (null) {}',
                '<?php if (TRUE) if (FALSE) if (NULL) {}',
            ],
            [
                '<?php if (!true) if (!false) if (!null) {}',
                '<?php if (!TRUE) if (!FALSE) if (!NULL) {}',
            ],
            [
                '<?php if ($a == true) if ($a == false) if ($a == null) {}',
                '<?php if ($a == TRUE) if ($a == FALSE) if ($a == NULL) {}',
            ],
            [
                '<?php if ($a === true) if ($a === false) if ($a === null) {}',
                '<?php if ($a === TRUE) if ($a === FALSE) if ($a === NULL) {}',
            ],
            [
                '<?php if ($a != true) if ($a != false) if ($a != null) {}',
                '<?php if ($a != TRUE) if ($a != FALSE) if ($a != NULL) {}',
            ],
            [
                '<?php if ($a !== true) if ($a !== false) if ($a !== null) {}',
                '<?php if ($a !== TRUE) if ($a !== FALSE) if ($a !== NULL) {}',
            ],
            [
                '<?php if (true && true and true AND true || false or false OR false xor null XOR null) {}',
                '<?php if (TRUE && TRUE and TRUE AND TRUE || FALSE or FALSE OR FALSE xor NULL XOR NULL) {}',
            ],
            [
                '<?php /* foo */ true; /** bar */ false;',
                '<?php /* foo */ TRUE; /** bar */ FALSE;',
            ],
            ['<?php echo $null;'],
            ['<?php $x = False::foo();'],
            ['<?php namespace Foo\Null;'],
            ['<?php class Foo extends True {}'],
            ['<?php class Foo implements False {}'],
            ['<?php $foo instanceof True; $foo instanceof False; $foo instanceof Null;'],
            [
                '<?php
    class Foo
    {
        const TRUE = 1;
        const FALSE = 2;
        const NULL = null;
    }',
            ],
            ['<?php $x = new /**/False?>'],
            ['<?php Null/**/::test();'],
            ['<?php True//
                                ::test();'],
            ['<?php class Foo { public function Bar() { $this->False = 1; $this->True = 2; $this->Null = 3; } }'],
        ];
    }

    /**
     * @dataProvider provideFix56Cases
     *
     * @param string      $expected
     * @param null|string $input
     *
     * @requires PHP <7.0
     */
    public function testFix56($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFix56Cases()
    {
        return [
            ['<?php use Foo\Null;'],
            ['<?php use Foo\Null as Null;'],
            ['<?php class True {} class False {} class Null {}'],
            ['<?php Class Null { use True; }'],
            ['<?php interface True {}'],
            ['<?php trait False {}'],
            [
                '<?php
                class Null {
                    use True, False {
                        False::bar insteadof True;
                        True::baz insteadof False;
                        False::baz as Null;
                    }
                }',
            ],
        ];
    }
}
