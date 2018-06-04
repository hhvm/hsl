<?hh // strict
/*
 *  Copyright (c) 2004-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the MIT license found in the
 *  LICENSE file in the root directory of this source tree.
 *
 */

use namespace HH\Lib\C;
use function Facebook\FBExpect\expect;
// @oss-disable: use InvariantViolationException as InvariantException;

/**
 * @emails oncall+hack
 */
final class CSelectTest extends PHPUnit_Framework_TestCase {

  public static function provideTestFind(): varray<mixed> {
    return varray[
      tuple(
        varray[],
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
    ];
  }

  /** @dataProvider provideTestFind */
  public function testFind<T>(
    Traversable<T> $traversable,
    (function(T): bool) $value_predicate,
    ?T $expected,
  ): void {
    expect(C\find($traversable, $value_predicate))->toBeSame($expected);
  }

  public static function provideTestFindKey(): varray<mixed> {
    return varray[
      tuple(
        varray[],
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
    ];
  }

  /** @dataProvider provideTestFindKey */
  public function testFindKey<Tk, Tv>(
    KeyedTraversable<Tk, Tv> $traversable,
    (function(Tv): bool) $value_predicate,
    ?Tv $expected,
  ): void {
    expect(C\find_key($traversable, $value_predicate))->toBeSame($expected);
  }

  public static function provideTestFirst(): varray<mixed> {
    return varray[
      tuple(
        varray[],
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
    ];
  }

  /** @dataProvider provideTestFirst */
  public function testFirst<T>(
    Traversable<T> $traversable,
    ?T $expected,
  ): void {
    expect(C\first($traversable))->toBeSame($expected);
  }

  public static function provideTestFirstx(): varray<mixed> {
    return varray[
      tuple(
        varray[],
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
    ];
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

  public static function provideTestFirstKey(): varray<mixed> {
    return varray[
      tuple(
        varray[],
        null,
      ),
      tuple(
        darray[1 => null],
        1,
      ),
      tuple(
        varray[1],
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
        HackLibTestTraversables::getKeyedIterator(darray[
          'foo' => 'bar',
          'baz' => 'qux',
        ]),
        'foo',
      ),
      tuple(
        HackLibTestTraversables::getKeyedIterator(varray[]),
        null,
      ),
    ];
  }

  /** @dataProvider provideTestFirstKey */
  public function testFirstKey<Tk, Tv>(
    KeyedTraversable<Tk, Tv> $traversable,
    ?Tk $expected,
  ): void {
    expect(C\first_key($traversable))->toBeSame($expected);
  }

  public static function provideTestFirstKeyx(): varray<mixed> {
    return varray[
      tuple(
        varray[],
        InvariantException::class,
      ),
      tuple(
        darray[1 => null],
        1,
      ),
      tuple(
        varray[1],
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
        HackLibTestTraversables::getKeyedIterator(darray[
          'foo' => 'bar',
          'baz' => 'qux',
        ]),
        'foo',
      ),
      tuple(
        HackLibTestTraversables::getKeyedIterator(varray[]),
        InvariantException::class,
      ),
    ];
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

  public static function provideTestLast(): varray<mixed> {
    return varray[
      tuple(
        varray[],
        null,
      ),
      tuple(
        varray[null],
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
        HackLibTestTraversables::getIterator(varray[]),
        null,
      ),
      tuple(
        HackLibTestTraversables::getIterator(varray[null]),
        null,
      ),
      tuple(
        HackLibTestTraversables::getIterator(range(13, 14)),
        14,
      ),
    ];
  }

  /** @dataProvider provideTestLast */
  public function testLast<Tv>(
    Traversable<Tv> $traversable,
    ?Tv $expected,
  ): void {
    expect(C\last($traversable))->toBeSame($expected);
  }

  public static function provideTestLastx(): varray<mixed> {
    return varray[
      tuple(
        varray[],
        InvariantException::class,
      ),
      tuple(
        varray[null],
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
        HackLibTestTraversables::getIterator(varray[]),
        InvariantException::class,
      ),
      tuple(
        HackLibTestTraversables::getIterator(varray[null]),
        null,
      ),
      tuple(
        HackLibTestTraversables::getIterator(range(13, 14)),
        14,
      ),
    ];
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

  public static function provideTestLastKey(): varray<mixed> {
    return varray[
      tuple(
        varray[],
        null
      ),
      tuple(
        darray['' => null],
        '',
      ),
      tuple(
        darray[1 => null],
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
        HackLibTestTraversables::getKeyedIterator(darray[3 => 13, 4 => 14]),
        4,
      ),
      tuple(
        HackLibTestTraversables::getKeyedIterator(varray[]),
        null,
      ),
      tuple(
        HackLibTestTraversables::getKeyedIterator(darray['' => null]),
        '',
      ),
    ];
  }

  /** @dataProvider provideTestLastKey */
  public function testLastKey<Tk, Tv>(
    KeyedTraversable<Tk, Tv> $traversable,
    ?Tk $expected,
  ): void {
    expect(C\last_key($traversable))->toBeSame($expected);
  }

  public static function provideTestLastKeyx(): varray<mixed> {
    return varray[
      tuple(
        varray[],
        InvariantException::class,
      ),
      tuple(
        darray['' => null],
        '',
      ),
      tuple(
        darray[1 => null],
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
        HackLibTestTraversables::getKeyedIterator(darray[3 => 13, 4 => 14]),
        4,
      ),
      tuple(
        HackLibTestTraversables::getKeyedIterator(varray[]),
        InvariantException::class,
      ),
      tuple(
        HackLibTestTraversables::getKeyedIterator(darray['' => null]),
        '',
      ),
    ];
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

  public static function provideTestNfirst(): varray<mixed> {
    return varray[
      tuple(
        null,
        null,
      ),
      tuple(
        varray[],
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
    ];
  }

  /** @dataProvider provideTestNfirst */
  public function testNfirst<T>(
    ?Traversable<T> $traversable,
    ?T $expected,
  ): void {
    expect(C\nfirst($traversable))->toBeSame($expected);
  }

  public static function provideTestOnlyx(): varray<mixed> {
    return varray[
      tuple(
        varray[],
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
    ];
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

  public function testOnlyxWithCustomMessage<T>(): void {
    $triple = vec[1, 2, 3];
    expect(
      () ==> C\onlyx(
        $triple,
        'Did not find exactly one thing. Found %d instead.',
        C\count($triple)
      ),
    )
      ->toThrow(
        InvariantException::class,
        'Did not find exactly one thing. Found 3 instead.',
      );
    $single = vec[42];
    expect(
      C\onlyx(
        $single,
        'Did not find exactly one thing. Found %d instead.',
        C\count($single)
      ),
    )
      ->toBeSame(42);
  }
}
