<?hh // strict
/*
 *  Copyright (c) 2004-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the MIT license found in the
 *  LICENSE file in the root directory of this source tree.
 *
 */

use namespace HH\Lib\{C, Str, Vec};
use function Facebook\FBExpect\expect; // @oss-enable
use type Facebook\HackTest\{DataProvider, HackTest}; // @oss-enable
// @oss-disable: use InvariantViolationException as InvariantException;

// @oss-disable: <<Oncalls('hack')>>
final class CSelectTest extends HackTest {

  public static function provideTestFind(
  ): vec<(Traversable<mixed>, (function(nothing): bool), mixed)> {
    return vec[
      tuple(
        varray[],
        $x ==> true,
        null,
      ),
      tuple(
        Vector {'the', 'quick', 'brown', 'fox'},
        ($w) ==> Str\length($w) === 5,
        'quick',
      ),
      tuple(
        Vector {'the', 'quick', 'brown', 'fox'},
        ($w) ==> Str\length($w) > 6,
        null,
      ),
    ];
  }

  <<DataProvider('provideTestFind')>>
  public function testFind<T>(
    Traversable<T> $traversable,
    (function(T): bool) $value_predicate,
    ?T $expected,
  ): void {
    expect(C\find($traversable, $value_predicate))->toEqual($expected);
  }

  public static function provideTestFindx(
  ): vec<(
    Traversable<mixed>,
    (function(nothing): bool),
    mixed,
  )> {
    return Vec\filter(self::provideTestFind(), $it ==> $it[2] !== null);
  }

  <<DataProvider('provideTestFindx')>>
  public function testFindx<T>(
    Traversable<T> $traversable,
    (function(T): bool) $value_predicate,
    T $expected,
  ): void {
    expect(C\findx($traversable, $value_predicate))->toEqual($expected);
  }

  public static function provideTestFindxException(
  ): vec<(
    Traversable<mixed>,
    (function(nothing): bool),
    classname<Exception>,
  )> {
    return Vec\filter(self::provideTestFind(), $it ==> $it[2] === null)
      |> Vec\map($$, $it ==> tuple($it[0], $it[1], InvariantException::class));
  }

  <<DataProvider('provideTestFindxException')>>
  public function testFindxException<T>(
    Traversable<T> $traversable,
    (function(T): bool) $value_predicate,
    classname<Exception> $expected,
  ): void {
    expect(() ==> C\findx($traversable, $value_predicate))
      ->toThrow($expected);
  }

  public static function provideTestFindKey(): vec<(KeyedTraversable<mixed, mixed>, (function(nothing): bool), ?arraykey)> {
    return vec[
      tuple(
        varray[],
        $x ==> $x,
        null,
      ),
      tuple(
        Vector {'the', 'quick', 'brown', 'fox'},
        ($w) ==> Str\length($w) === 5,
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

  <<DataProvider('provideTestFindKey')>>
  public function testFindKey<Tk, Tv>(
    KeyedTraversable<Tk, Tv> $traversable,
    (function(Tv): bool) $value_predicate,
    ?Tk $expected,
  ): void {
    expect(C\find_key($traversable, $value_predicate))->toEqual($expected);
  }

  public static function provideTestFirst<T>(): vec<(Traversable<mixed>, mixed)> {
    return vec[
      tuple(
        varray[],
        null,
      ),
      tuple(
        HackLibTestTraversables::getIterator(Vec\range(1, 5)),
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

  <<DataProvider('provideTestFirst')>>
  public function testFirst<T>(
    Traversable<T> $traversable,
    ?T $expected,
  ): void {
    expect(C\first($traversable))->toEqual($expected);
  }

  public static function provideTestFirstx(): vec<(Traversable<mixed>, mixed)> {
    return vec[
      tuple(
        HackLibTestTraversables::getIterator(Vec\range(1, 5)),
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

  <<DataProvider('provideTestFirstx')>>
  public function testFirstx<T>(
    Traversable<T> $traversable,
    T $expected,
  ): void {
    expect(C\firstx($traversable))->toEqual($expected);
  }

  public static function provideTestFirstxException(
  ): vec<(Traversable<nothing>, classname<Exception>)> {
    return vec[
      tuple(
        varray[],
        InvariantException::class,
      ),
    ];
  }

  <<DataProvider('provideTestFirstxException')>>
  public function testFirstxException<T>(
    Traversable<T> $traversable,
    classname<Exception> $expected,
  ): void {
    expect(() ==> C\firstx($traversable))
      ->toThrow($expected);
  }

  public static function provideTestFirstKey(
  ): vec<(KeyedTraversable<mixed, mixed>, mixed)> {
    return vec[
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
        Vec\range(3, 10),
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
      tuple(
        () ==> {
          yield 42 => 'spam';
          yield null => 'quux';
        }(),
        42,
      ),
    ];
  }

  <<DataProvider('provideTestFirstKey')>>
  public function testFirstKey<Tk, Tv>(
    KeyedTraversable<Tk, Tv> $traversable,
    ?Tk $expected,
  ): void {
    expect(C\first_key($traversable))->toEqual($expected);
  }

  public static function provideTestFirstKeyx(
  ): vec<(KeyedTraversable<mixed, mixed>, mixed)> {
    return vec[
      tuple(
        darray[1 => null],
        1,
      ),
      tuple(
        varray[1],
        0,
      ),
      tuple(
        Vec\range(3, 10),
        0,
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
        () ==> {
          yield null => 'quux';
          yield 42 => 'spam';
        }(),
        null,
      ),
    ];
  }

  <<DataProvider('provideTestFirstKeyx')>>
  public function testFirstKeyx<Tk, Tv>(
    KeyedTraversable<Tk, Tv> $traversable,
    Tk $expected,
  ): void {
    expect(C\first_keyx($traversable))->toEqual($expected);
  }

  public static function provideTestFirstKeyxException(
  ): vec<(KeyedTraversable<mixed, mixed>, classname<Exception>)> {
    return vec[
      tuple(
        varray[],
        InvariantException::class,
      ),
      tuple(
        Map {},
        InvariantException::class,
      ),
      tuple(
        HackLibTestTraversables::getKeyedIterator(varray[]),
        InvariantException::class,
      ),
      tuple(
        () ==> {
          yield break;
        }(),
        InvariantException::class,
      ),
    ];
  }

  <<DataProvider('provideTestFirstKeyxException')>>
  public function testFirstKeyxException<Tk, Tv>(
    KeyedTraversable<Tk, Tv> $traversable,
    classname<Exception> $expected,
  ): void {
    expect(() ==> C\first_keyx($traversable))
      ->toThrow($expected);
  }

  public static function provideTestLast(): vec<(Traversable<mixed>, mixed)> {
    return vec[
      tuple(
        varray[],
        null,
      ),
      tuple(
        varray[null],
        null,
      ),
      tuple(
        Vec\range(1, 11),
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
        HackLibTestTraversables::getIterator(Vec\range(13, 14)),
        14,
      ),
    ];
  }

  <<DataProvider('provideTestLast')>>
  public function testLast<Tv>(
    Traversable<Tv> $traversable,
    ?Tv $expected,
  ): void {
    expect(C\last($traversable))->toEqual($expected);
  }

  public static function provideTestLastx(): vec<(Traversable<mixed>, mixed)> {
    return vec[
      tuple(
        varray[null],
        null,
      ),
      tuple(
        Vec\range(1, 11),
        11,
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
        HackLibTestTraversables::getIterator(varray[null]),
        null,
      ),
      tuple(
        HackLibTestTraversables::getIterator(Vec\range(13, 14)),
        14,
      ),
    ];
  }

  <<DataProvider('provideTestLastx')>>
  public function testLastx<Tv>(
    Traversable<Tv> $traversable,
    Tv $expected,
  ): void {
    expect(C\lastx($traversable))->toEqual($expected);
  }

  public static function provideTestLastxException(
  ): vec<(Traversable<mixed>, classname<Exception>)> {
    return vec[
      tuple(
        varray[],
        InvariantException::class,
      ),
      tuple(
        Map {},
        InvariantException::class,
      ),
      tuple(
        HackLibTestTraversables::getIterator(varray[]),
        InvariantException::class,
      ),
    ];
  }

  <<DataProvider('provideTestLastxException')>>
  public function testLastxException<Tv>(
    Traversable<Tv> $traversable,
    classname<Exception> $expected,
  ): void {
    expect(() ==> C\lastx($traversable))
      ->toThrow($expected);
  }

  public static function provideTestLastKey(
  ): vec<(KeyedTraversable<mixed, mixed>, mixed)> {
    return vec[
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
        Vec\range(1, 5),
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
      tuple(
        () ==> {
          yield null => 'quux';
          yield 42 => 'spam';
        }(),
        42,
      ),
    ];
  }

  <<DataProvider('provideTestLastKey')>>
  public function testLastKey<Tk, Tv>(
    KeyedTraversable<Tk, Tv> $traversable,
    ?Tk $expected,
  ): void {
    expect(C\last_key($traversable))->toEqual($expected);
  }

  public static function provideTestLastKeyx(
  ): vec<(KeyedTraversable<mixed, mixed>, mixed)> {
    return vec[
      tuple(
        darray['' => null],
        '',
      ),
      tuple(
        darray[1 => null],
        1,
      ),
      tuple(
        Vec\range(1, 5),
        4,
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
        HackLibTestTraversables::getKeyedIterator(darray['' => null]),
        '',
      ),
      tuple(
        () ==> {
          yield 42 => 'spam';
          yield null => 'quux';
        }(),
        null,
      ),
    ];
  }

  <<DataProvider('provideTestLastKeyx')>>
  public function testLastKeyx<Tk, Tv>(
    KeyedTraversable<Tk, Tv> $traversable,
    Tk $expected,
  ): void {
    expect(C\last_keyx($traversable))->toEqual($expected);
  }

  public static function provideTestLastKeyxException(
  ): vec<(KeyedTraversable<mixed, mixed>, classname<Exception>)> {
    return vec[
      tuple(
        varray[],
        InvariantException::class,
      ),
      tuple(
        Map {},
        InvariantException::class,
      ),
      tuple(
        HackLibTestTraversables::getKeyedIterator(varray[]),
        InvariantException::class,
      ),
      tuple(
        () ==> {
          yield break;
        }(),
        InvariantException::class,
      ),
    ];
  }

  <<DataProvider('provideTestLastKeyxException')>>
  public function testLastKeyxException<Tk, Tv>(
    KeyedTraversable<Tk, Tv> $traversable,
    classname<Exception> $expected,
  ): void {
    expect(() ==> C\last_keyx($traversable))
      ->toThrow($expected);
  }

  public static function provideTestNfirst(
  ): vec<(?Traversable<mixed>, mixed)> {
    return vec[
      tuple(
        null,
        null,
      ),
      tuple(
        varray[],
        null,
      ),
      tuple(
        HackLibTestTraversables::getIterator(Vec\range(1, 5)),
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

  <<DataProvider('provideTestNfirst')>>
  public function testNfirst<T>(
    ?Traversable<T> $traversable,
    ?T $expected,
  ): void {
    expect(C\nfirst($traversable))->toEqual($expected);
  }

  public static function provideTestOnlyx(): vec<(Traversable<mixed>, mixed)> {
    return vec[
      tuple(
        vec[1],
        1,
      ),
      tuple(
        dict[
          '5' => '10',
        ],
        '10',
      ),
    ];
  }

  <<DataProvider('provideTestOnlyx')>>
  public function testOnlyx<T>(
    Traversable<T> $traversable,
    T $expected,
  ): void {
    expect(C\onlyx($traversable))->toEqual($expected);
  }

  public static function provideTestOnlyxException(
  ): vec<(Traversable<mixed>, classname<Exception>)> {
    return vec[
      tuple(
        varray[],
        InvariantException::class,
      ),
      tuple(
        HackLibTestTraversables::getIterator(Vec\range(1, 5)),
        InvariantException::class,
      ),
      tuple(
        dict[
          '5' => '10',
          '10' => '20',
        ],
        InvariantException::class,
      ),
    ];
  }

  <<DataProvider('provideTestOnlyxException')>>
  public function testOnlyxException<T>(
    Traversable<T> $traversable,
    classname<Exception> $expected,
  ): void {
    expect(() ==> C\onlyx($traversable))
      ->toThrow($expected);
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
      ->toEqual(42);
  }
}
