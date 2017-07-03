<?hh // strict
/*
 *  Copyright (c) 2004-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the BSD-style license found in the
 *  LICENSE file in the root directory of this source tree. An additional grant
 *  of patent rights can be found in the PATENTS file in the same directory.
 *
 */

use \HH\Lib\Dict as DictHSL;
use function \Facebook\FBExpect\expect;

/**
 * @emails oncall+hack_prod_infra
 */
final class DictCombineTest extends PHPUnit_Framework_TestCase {

  public static function provideTestAssociate(): array<mixed> {
    return array(
      tuple(
        vec[3, 2, 1],
        Vector {'a', 'b', 'c'},
        dict[
          3 => 'a',
          2 => 'b',
          1 => 'c',
        ],
      ),
      tuple(
        range(0, 3),
        HackLibTestTraversables::getIterator(range(0, 3)),
        dict[
          0 => 0,
          1 => 1,
          2 => 2,
          3 => 3,
        ],
      ),
      tuple(
        Map {
          1 => 2,
          2 => 4,
        },
        dict[
          3 => 6,
          4 => 8,
        ],
        dict[
          2 => 6,
          4 => 8,
        ],
      ),
    );
  }

  /** @dataProvider provideTestAssociate */
  public function testAssociate<Tk as arraykey, Tv>(
    Traversable<Tk> $keys,
    Traversable<Tv> $values,
    dict<Tk, Tv> $expected,
  ): void {
    expect(DictHSL\associate($keys, $values))->toBeSame($expected);
  }

  public static function provideTestMerge(): array<mixed> {
    return array(
      tuple(
        array(),
        dict[],
      ),
      tuple(
        array(
          array(
            'one' => 'one',
            'two' => 'two',
          ),
          array(
            'three' => 'three',
            'one' => 3,
          ),
          Map {
            'four' => null,
          },
        ),
        dict[
          'one' => 3,
          'two' => 'two',
          'three' => 'three',
          'four' => null,
        ],
      ),
      tuple(
        array(
          HackLibTestTraversables::getKeyedIterator(array(
            'foo' => 'foo',
            'bar' => 'bar',
            'baz' => array(1, 2, 3),
          )),
          dict[
            'bar' => 'barbar',
          ],
          Vector {'I should feel bad for doing this', 'But yolo'},
          array(
            '1' => 'gross array behavior',
          ),
          Set {'bloop'},
        ),
        dict[
          'foo' => 'foo',
          'bar' => 'barbar',
          'baz' => array(1, 2, 3),
          0 => 'I should feel bad for doing this',
          1 => 'gross array behavior',
          'bloop' => 'bloop',
        ],
      ),
    );
  }

  /** @dataProvider provideTestMerge */
  public function testMerge<Tk, Tv>(
    Container<KeyedTraversable<Tk, Tv>> $traversables,
    dict<Tk, Tv> $expected,
  ): void {
    expect(DictHSL\merge(...$traversables))->toBeSame($expected);
  }
}
