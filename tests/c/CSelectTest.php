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

use namespace \HH\Lib\C;
use function \Facebook\FBExpect\expect;
// @oss-disable: use InvariantViolationException as InvariantException;

/**
 * @emails oncall+hack_prod_infra
 */
final class CSelectTest extends PHPUnit_Framework_TestCase {

  public static function provideTestFind(): array<mixed> {
    return array(
      tuple(
        array(),
        $x ==> $x,
        null,
      ),
      tuple(
        Vector {'the', 'quick', 'brown', 'fox'},
        ($w) ==> strlen($w) === 5,
        'quick',
      ),
      tuple(
        Vector {'the', 'quick', 'brown', 'fox'},
        ($w) ==> strlen($w) > 6,
        null,
      ),
    );
  }

  /** @dataProvider provideTestFind */
  public function testFind<T>(
    Traversable<T> $traversable,
    (function(T): bool) $value_predicate,
    ?T $expected,
  ): void {
    expect(C\find($traversable, $value_predicate))->toBeSame($expected);
  }

  public static function provideTestFindKey(): array<mixed> {
    return array(
      tuple(
        array(),
        $x ==> $x,
        null,
      ),
      tuple(
        Vector {'the', 'quick', 'brown', 'fox'},
        ($w) ==> strlen($w) === 5,
        1,
      ),
      tuple(
        dict[
          'zero' => 0,
          'one' => 1,
          'two' => 2,
        ],
        ($n) ==> $n === 2,
        'two',
      ),
    );
  }

  /** @dataProvider provideTestFindKey */
  public function testFindKey<Tk, Tv>(
    KeyedTraversable<Tk, Tv> $traversable,
    (function(Tv): bool) $value_predicate,
    ?Tv $expected,
  ): void {
    expect(C\find_key($traversable, $value_predicate))->toBeSame($expected);
  }

  public static function provideTestFirst(): array<mixed> {
    return array(
      tuple(
        array(),
        null,
      ),
      tuple(
        HackLibTestTraversables::getIterator(range(1, 5)),
        1,
      ),
      tuple(
        dict[
          '5' => '10',
          '10' => '20',
        ],
        '10',
      ),
    );
  }

  /** @dataProvider provideTestFirst */
  public function testFirst<T>(
    Traversable<T> $traversable,
    ?T $expected,
  ): void {
    expect(C\first($traversable))->toBeSame($expected);
  }

  public static function provideTestFirstx(): array<mixed> {
    return array(
      tuple(
        array(),
        InvariantException::class,
      ),
      tuple(
        HackLibTestTraversables::getIterator(range(1, 5)),
        1,
      ),
      tuple(
        dict[
          '5' => '10',
          '10' => '20',
        ],
        '10',
      ),
    );
  }

  /** @dataProvider provideTestFirstx */
  public function testFirstx<T>(
    Traversable<T> $traversable,
    mixed $expected,
  ): void {
    if (is_subclass_of($expected, Exception::class)) {
      expect(() ==> C\firstx($traversable))
        ->toThrow(/* UNSAFE_EXPR */ $expected);
    } else {
      expect(C\firstx($traversable))->toBeSame($expected);
    }
  }

  public static function provideTestFirstKey(): array<mixed> {
    return array(
      tuple(
        array(),
        null,
      ),
      tuple(
        array(1 => null),
        1,
      ),
      tuple(
        array(1),
        0,
      ),
      tuple(
        range(3, 10),
        0,
      ),
      tuple(
        Map {},
        null,
      ),
      tuple(
        Map {1 => null},
        1,
      ),
      tuple(
        Set {2, 3},
        2,
      ),
      tuple(
        Vector {2, 3},
        0,
      ),
      tuple(
        Map {3 => 3, 2 => 2},
        3,
      ),
      tuple(
        HackLibTestTraversables::getKeyedIterator(array(
          'foo' => 'bar',
          'baz' => 'qux',
        )),
        'foo',
      ),
      tuple(
        HackLibTestTraversables::getKeyedIterator(array()),
        null,
      ),
    );
  }

  /** @dataProvider provideTestFirstKey */
  public function testFirstKey<Tk, Tv>(
    KeyedTraversable<Tk, Tv> $traversable,
    ?Tk $expected,
  ): void {
    expect(C\first_key($traversable))->toBeSame($expected);
  }

  public static function provideTestFirstKeyx(): array<mixed> {
    return array(
      tuple(
        array(),
        InvariantException::class,
      ),
      tuple(
        array(1 => null),
        1,
      ),
      tuple(
        array(1),
        0,
      ),
      tuple(
        range(3, 10),
        0,
      ),
      tuple(
        Map {},
        InvariantException::class,
      ),
      tuple(
        Map {1 => null},
        1,
      ),
      tuple(
        Set {2, 3},
        2,
      ),
      tuple(
        Vector {2, 3},
        0,
      ),
      tuple(
        Map {3 => 3, 2 => 2},
        3,
      ),
      tuple(
        HackLibTestTraversables::getKeyedIterator(array(
          'foo' => 'bar',
          'baz' => 'qux',
        )),
        'foo',
      ),
      tuple(
        HackLibTestTraversables::getKeyedIterator(array()),
        InvariantException::class,
      ),
    );
  }

  /** @dataProvider provideTestFirstKeyx */
  public function testFirstKeyx<Tk, Tv>(
    KeyedTraversable<Tk, Tv> $traversable,
    mixed $expected,
  ): void {
    if (is_subclass_of($expected, Exception::class)) {
      expect(() ==> C\first_keyx($traversable))
        ->toThrow(/* UNSAFE_EXPR */ $expected);
    } else {
      expect(C\first_keyx($traversable))->toBeSame($expected);
    }
  }

  public static function provideTestMax(): array<mixed> {
    return array(
      tuple(
        array(),
        null,
      ),
      tuple(
        Set {8, 6, 7, 5, 3, 0, 9},
        9,
      ),
      tuple(
        HackLibTestTraversables::getIterator(
          array(8, 6, 7, 5, 3, 0, 9),
        ),
        9,
      ),
    );
  }

  /** @dataProvider provideTestMax */
  public function testMax<T as num>(
    Traversable<T> $traversable,
    ?T $expected,
  ): void {
    expect(C\max($traversable))->toBeSame($expected);
  }

  public static function provideTestMaxBy(): array<mixed> {
    return array(
      tuple(
        array(),
        $x ==> $x,
        null,
      ),
      tuple(
        vec['the', 'quick', 'brown', 'fox'],
        fun('strlen'),
        'brown',
      ),
      tuple(
        HackLibTestTraversables::getIterator(
          array('the', 'quick', 'brown', 'fox'),
        ),
        fun('strlen'),
        'brown',
      ),
    );
  }

  /** @dataProvider provideTestMaxBy */
  public function testMaxBy<T>(
    Traversable<T> $traversable,
    (function(T): num) $num_func,
    ?T $expected,
  ): void {
    expect(C\max_by($traversable, $num_func))->toBeSame($expected);
  }

  public static function provideTestMin(): array<mixed> {
    return array(
      tuple(
        array(),
        null,
      ),
      tuple(
        Set {8, 6, 7, 5, 3, 0, 9},
        0,
      ),
      tuple(
        HackLibTestTraversables::getIterator(
          array(8, 6, 7, 5, 3, 0, 9),
        ),
        0,
      ),
      tuple(
        Vector {8, 6, 7, -5, -3, 0, 9},
        -5,
      ),
    );
  }

  /** @dataProvider provideTestMin */
  public function testMin<T as num>(
    Traversable<T> $traversable,
    ?T $expected,
  ): void {
    expect(C\min($traversable))->toBeSame($expected);
  }

  public static function provideTestMinBy(): array<mixed> {
    return array(
      tuple(
        array(),
        $x ==> $x,
        null,
      ),
      tuple(
        vec['the', 'quick', 'brown', 'fox'],
        fun('strlen'),
        'fox',
      ),
      tuple(
        HackLibTestTraversables::getIterator(
          array('the', 'quick', 'brown', 'fox'),
        ),
        fun('strlen'),
        'fox',
      ),
    );
  }

  /** @dataProvider provideTestMinBy */
  public function testMinBy<T>(
    Traversable<T> $traversable,
    (function(T): num) $num_func,
    ?T $expected,
  ): void {
    expect(C\min_by($traversable, $num_func))->toBeSame($expected);
  }

  public static function provideTestLast(): array<mixed> {
    return array(
      tuple(
        array(),
        null,
      ),
      tuple(
        array(null),
        null,
      ),
      tuple(
        range(1, 11),
        11,
      ),
      tuple(
        Map {},
        null,
      ),
      tuple(
        Map {0 => null},
        null,
      ),
      tuple(
        Vector {1, 2},
        2,
      ),
      tuple(
        Set {2, 3},
        3,
      ),
      tuple(
        Map {3 => 30, 4 => 40},
        40,
      ),
      tuple(
        Map {3 => 30, 4 => null},
        null,
      ),
      tuple(
        vec[1, 2],
        2,
      ),
      tuple(
        keyset[2, 3],
        3,
      ),
      tuple(
        dict[3 => 4, 4 => 5],
        5,
      ),
      tuple(
        HackLibTestTraversables::getIterator(array()),
        null,
      ),
      tuple(
        HackLibTestTraversables::getIterator(array(null)),
        null,
      ),
      tuple(
        HackLibTestTraversables::getIterator(range(13, 14)),
        14,
      ),
    );
  }

  /** @dataProvider provideTestLast */
  public function testLast<Tv>(
    Traversable<Tv> $traversable,
    ?Tv $expected,
  ): void {
    expect(C\last($traversable))->toBeSame($expected);
  }

  public static function provideTestLastx(): array<mixed> {
    return array(
      tuple(
        array(),
        InvariantException::class,
      ),
      tuple(
        array(null),
        null,
      ),
      tuple(
        range(1, 11),
        11,
      ),
      tuple(
        Map {},
        InvariantException::class,
      ),
      tuple(
        Map {0 => null},
        null,
      ),
      tuple(
        Vector {1, 2},
        2,
      ),
      tuple(
        Set {2, 3},
        3,
      ),
      tuple(
        Map {3 => 30, 4 => 40},
        40,
      ),
      tuple(
        Map {3 => 30, 4 => null},
        null,
      ),
      tuple(
        vec[1, 2],
        2,
      ),
      tuple(
        keyset[2, 3],
        3,
      ),
      tuple(
        dict[3 => 4, 4 => 5],
        5,
      ),
      tuple(
        HackLibTestTraversables::getIterator(array()),
        InvariantException::class,
      ),
      tuple(
        HackLibTestTraversables::getIterator(array(null)),
        null,
      ),
      tuple(
        HackLibTestTraversables::getIterator(range(13, 14)),
        14,
      ),
    );
  }

  /** @dataProvider provideTestLastx */
  public function testLastx<Tv>(
    Traversable<Tv> $traversable,
    mixed $expected,
  ): void {
    if (is_subclass_of($expected, Exception::class)) {
      expect(() ==> C\lastx($traversable))
        ->toThrow(/* UNSAFE_EXPR */ $expected);
    } else {
      expect(C\lastx($traversable))->toBeSame($expected);
    }
  }

  public static function provideTestLastKey(): array<mixed> {
    return array(
      tuple(
        array(),
        null
      ),
      tuple(
        array('' => null),
        '',
      ),
      tuple(
        array(1 => null),
        1,
      ),
      tuple(
        range(1, 5),
        4,
      ),
      tuple(
        Map {},
        null,
      ),
      tuple(
        Vector {1, 2},
        1,
      ),
      tuple(
        Set {2, 3},
        3,
      ),
      tuple(
        Map {3 => 3, 4 => 4},
        4,
      ),
      tuple(
        vec[1, 2],
        1,
      ),
      tuple(
        keyset[2, 3],
        3,
      ),
      tuple(
        dict[3 => 3, 4 => 4],
        4,
      ),
      tuple(
        HackLibTestTraversables::getKeyedIterator(array(3 => 13, 4 => 14)),
        4,
      ),
      tuple(
        HackLibTestTraversables::getKeyedIterator(array()),
        null,
      ),
      tuple(
        HackLibTestTraversables::getKeyedIterator(array('' => null)),
        '',
      ),
    );
  }

  /** @dataProvider provideTestLastKey */
  public function testLastKey<Tk, Tv>(
    KeyedTraversable<Tk, Tv> $traversable,
    ?Tk $expected,
  ): void {
    expect(C\last_key($traversable))->toBeSame($expected);
  }

  public static function provideTestLastKeyx(): array<mixed> {
    return array(
      tuple(
        array(),
        InvariantException::class,
      ),
      tuple(
        array('' => null),
        '',
      ),
      tuple(
        array(1 => null),
        1,
      ),
      tuple(
        range(1, 5),
        4,
      ),
      tuple(
        Map {},
        InvariantException::class,
      ),
      tuple(
        Vector {1, 2},
        1,
      ),
      tuple(
        Set {2, 3},
        3,
      ),
      tuple(
        Map {3 => 3, 4 => 4},
        4,
      ),
      tuple(
        vec[1, 2],
        1,
      ),
      tuple(
        keyset[2, 3],
        3,
      ),
      tuple(
        dict[3 => 3, 4 => 4],
        4,
      ),
      tuple(
        HackLibTestTraversables::getKeyedIterator(array(3 => 13, 4 => 14)),
        4,
      ),
      tuple(
        HackLibTestTraversables::getKeyedIterator(array()),
        InvariantException::class,
      ),
      tuple(
        HackLibTestTraversables::getKeyedIterator(array('' => null)),
        '',
      ),
    );
  }

  /** @dataProvider provideTestLastKeyx */
  public function testLastKeyx<Tk, Tv>(
    KeyedTraversable<Tk, Tv> $traversable,
    mixed $expected,
  ): void {
    if (is_subclass_of($expected, Exception::class)) {
      expect(() ==> C\last_keyx($traversable))
        ->toThrow(/* UNSAFE_EXPR */ $expected);
    } else {
      expect(C\last_keyx($traversable))->toBeSame($expected);
    }
  }

  public static function provideTestNfirst(): array<mixed> {
    return array(
      tuple(
        null,
        null,
      ),
      tuple(
        array(),
        null,
      ),
      tuple(
        HackLibTestTraversables::getIterator(range(1, 5)),
        1,
      ),
      tuple(
        dict[
          '5' => '10',
          '10' => '20',
        ],
        '10',
      ),
    );
  }

  /** @dataProvider provideTestNfirst */
  public function testNfirst<T>(
    ?Traversable<T> $traversable,
    ?T $expected,
  ): void {
    expect(C\nfirst($traversable))->toBeSame($expected);
  }

  public static function provideTestOnlyx(): array<mixed> {
    return array(
      tuple(
        array(),
        InvariantException::class,
      ),
      tuple(
        vec[1],
        1,
      ),
      tuple(
        HackLibTestTraversables::getIterator(range(1, 5)),
        InvariantException::class,
      ),
      tuple(
        dict[
          '5' => '10',
          '10' => '20',
        ],
        InvariantException::class,
      ),
      tuple(
        dict[
          '5' => '10',
        ],
        '10',
      ),
    );
  }

  /** @dataProvider provideTestOnlyx */
  public function testOnlyx<T>(
    Traversable<T> $traversable,
    mixed $expected,
  ): void {
    if (is_subclass_of($expected, Exception::class)) {
      expect(() ==> C\onlyx($traversable))
        ->toThrow(/* UNSAFE_EXPR */ $expected);
    } else {
      expect(C\onlyx($traversable))->toBeSame($expected);
    }
  }
}
