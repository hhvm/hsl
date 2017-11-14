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

use namespace HH\Lib\Tuple as Tuple;
use function Facebook\FBExpect\expect;

/**
 * @emails oncall+hack_prod_infra
 */
final class TupleAsyncTest extends PHPUnit_Framework_TestCase {
  public function testWithNonNullableTypes(): void {
    /* HH_IGNORE_ERROR[5542] open source */
    \HH\Asio\join(async {
      $t = await Tuple\from_async(async { return 1; }, async { return 'foo'; });
      expect($t)->toBeSame(tuple(1, 'foo'));
      list($a, $b) = $t;
      expect(
        ((int $x, string $y) ==> tuple($x, $y))($a, $b)
      )->toBeSame($t);
    });
  }

  public function testWithNullLiterals(): void {
    /* HH_IGNORE_ERROR[5542] open source */
    \HH\Asio\join(async {
      $t = await Tuple\from_async(async { return 1; }, null, async { return null; });
      expect($t)->toBeSame(tuple(1, null, null));
      list($a, $b, $c) = $t;
      expect(
        ((int $x, ?int $y, ?int $z) ==> tuple($x, $y, $z))($a, $b, $c)
      )->toBeSame($t);
    });
  }

  public function testWithNullableTypes(): void {
    /* HH_IGNORE_ERROR[5542] open source */
    \HH\Asio\join(async {
      $t = await Tuple\from_async(async { return 1; }, async { return 'foo'; });
      expect($t)->toBeSame(tuple(1, 'foo'));
      list($a, $b) = $t;
      expect(
        ((?int $x, ?string $y) ==> tuple($x, $y))($a, $b)
      )->toBeSame($t);
    });
  }
}
