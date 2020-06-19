<?hh // strict
/*
 *  Copyright (c) 2004-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the MIT license found in the
 *  LICENSE file in the root directory of this source tree.
 *
 */

use namespace HH\Lib\Vec;
use function Facebook\FBExpect\expect; // @oss-enable
use type Facebook\HackTest\{DataProvider, HackTest}; // @oss-enable

// @oss-disable: <<Oncalls('hack')>>
final class VecCombineTest extends HackTest {

  public static function provideTestConcat(): vec<(Traversable<mixed>, Container<Container<mixed>>, Traversable<mixed>)> {
    return vec[
      tuple(
        varray[],
        varray[],
        vec[],
      ),
      tuple(
        vec[],
        varray[
          darray[],
          Vector {},
          Map {},
          Set {},
        ],
        vec[],
      ),
      tuple(
        varray['the', 'quick'],
        varray[
          Vector {'brown', 'fox'},
          Map {'jumped' => 'over'},
          varray['the', 'lazy', 'dog'],
        ],
        vec['the', 'quick', 'brown', 'fox', 'over', 'the', 'lazy', 'dog'],
      ),
    ];
  }

  <<DataProvider('provideTestConcat')>>
  public function testConcat<Tv>(
    Traversable<Tv> $first,
    Container<Container<Tv>> $rest,
    vec<Tv> $expected,
  ): void {
    expect(Vec\concat($first, ...$rest))->toEqual($expected);
  }

  public static function provideTestZip(
  ): dict<
    string,
    (Traversable<mixed>, Traversable<mixed>, Traversable<(mixed, mixed)>),
  > {
    return dict[
      "Should return empty by zipping 2 empty vecs" =>
        tuple(vec[], vec[], vec[]),
      "Should return empty if any of the vecs is empty" =>
        tuple(vec[], vec[1, 2, 3], vec[]),
      "Should zip all elements if vecs are of the same size" => tuple(
        vec[1, 2, 3],
        vec[4, 5, 6],
        vec[tuple(1, 4), tuple(2, 5), tuple(3, 6)],
      ),
      "We can zip vecs of different types" => tuple(
        vec[1, 2, 3],
        vec['a', 'b', 'c'],
        vec[tuple(1, 'a'), tuple(2, 'b'), tuple(3, 'c')],
      ),
      "The inputs can be any traversables, but keys are discarded." => tuple(
        darray['a' => 1, 'b' => 2, 'c' => 3],
        Map {10 => 'd', 20 => 'e', 30 => 'f'},
        vec[tuple(1, 'd'), tuple(2, 'e'), tuple(3, 'f')],
      ),
      'empty dict and empty vec' => tuple(dict[], vec[], vec[]),
      'one element' => tuple(vec['a'], dict[0 => 'b'], vec[tuple('a', 'b')]),
      'first is longer than second' =>
        tuple(vec[1, -1], vec[2], vec[tuple(1, 2)]),
      'second is longer than first' =>
        tuple(vec['yes'], vec['maybe', 'no'], vec[tuple('yes', 'maybe')]),
      'very long input' => tuple(
        Vec\range(0, 101),
        Vec\range(0, 101),
        Vec\map(Vec\range(0, 101), (int $i) ==> tuple($i, $i)),
      ),
      'Hack Collections' => tuple(
        Map {'banana' => 5, 'pear' => 4},
        Set {3, 2},
        vec[tuple(5, 3), tuple(4, 2)],
      ),
      'Generators' => tuple(
        HackLibTestTraversables::getKeyedIterator(dict[
          'keys' => 'do',
          'not' => 'matter',
          'for' => 'vec',
          'zip' => 'anyhow',
        ]),
        HackLibTestTraversables::getIterator(vec[
          'generators',
          'are',
          'very',
          'powerful',
          'indeed',
        ]),
        vec[
          tuple('do', 'generators'),
          tuple('matter', 'are'),
          tuple('vec', 'very'),
          tuple('anyhow', 'powerful'),
        ],
      ),
    ];
  }

  <<DataProvider('provideTestZip')>>
  public function testZip(
    Traversable<mixed> $first,
    Traversable<mixed> $second,
    vec<(mixed, mixed)> $expected,
  ): void {
    $actual = Vec\zip($first, $second);
    expect($actual)->toEqual($expected);
  }

}
