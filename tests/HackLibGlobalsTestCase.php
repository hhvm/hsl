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

use function Facebook\FBExpect\expect;

/**
 * @emails oncall+php_prod_infra
 */
final class HackLibGlobalsTestCase extends PHPUnit_Framework_TestCase {

  public static function providesIsHackArray(): array<mixed> {
    return array(
      array(null, false),
      array(true, false),
      array(false, false),
      array(42, false),
      array('foo', false),
      array(array(), false),
      array(array('foo'), false),
      array(Map {}, false),
      array(Set {'foo'}, false),

      array(dict[], true),
      array(vec[], true),
      array(keyset[], true),

      array(dict['foo' => 'bar'], true),
      array(vec[42], true),
      array(keyset['foobar'], true),
    );
  }

  /** @dataProvider providesIsHackArray */
  public function testIsHackArray(
    mixed $candidate,
    bool $expected,
  ): void {
    expect(is_hack_array($candidate))->toEqual($expected);
  }

  public static function providesIsAnyArray(): array<mixed> {
    return array(
      tuple(null, false),
      tuple(true, false),
      tuple(false, false),
      tuple(42, false),
      tuple('foo', false),
      tuple(array(), true),
      tuple(array('foo'), true),
      tuple(Map {}, false),
      tuple(Set {'foo'}, false),

      tuple(dict[], true),
      tuple(vec[], true),
      tuple(keyset[], true),

      tuple(dict['foo' => 'bar'], true),
      tuple(vec[42], true),
      tuple(keyset['foobar'], true),
    );
  }

  /** @dataProvider providesIsAnyArray */
  public function testIsAnyArray(
    mixed $val,
    bool $expected,
  ): void {
    expect(is_any_array($val))->toEqual($expected);
  }
}
