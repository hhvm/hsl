<?hh // strict
// Copyright 2004-present Facebook. All Rights Reserved.

use HH\Lib\C;
use HH\Lib\Dict;
use HH\Lib\Math;
use HH\Lib\Str;
use HH\Lib\Vec;

trait RandomTestTrait {
  require extends PHPUnit_Framework_TestCase;

  public abstract function getRandomFloat(): float;
  public abstract function getRandomInt(int $min, int $max): int;
  public abstract function getRandomString(
    int $length,
    ?string $alphabet = null,
  ): string;

  /**
   * Test that `float` returns values in the correct range.  This test calls
   * `float` multiple times and checks that:
   *
   * 1. Each value is in the correct range.
   *
   * 2. The maximum value produced by all the calls is "large enough," and
   *    similarly for the minimum value.  (In other words, this checks that
   *    `float` is filling the requested range.)
   */
  public function testFloatRange(): void {
    // This number of iterations is chosen such that the probability of failure
    // of a single run of this function is extremely low:
    // 0.8 ** 1000 = 1e-97
    $iterations = 1000;

    $min_rand = 1.0;
    $max_rand = 0.0;
    for ($i = 0; $i < $iterations; $i++) {
      $rand = $this->getRandomFloat();
      expect($rand)->toBeLessThanOrEqualTo(1.0);
      expect($rand)->toBeGreaterThanOrEqualTo(0.0);
      $min_rand = Math\min($min_rand, $rand);
      $max_rand = Math\max($max_rand, $rand);
    }

    expect($min_rand)->toBeLessThanOrEqualTo(0.1);
    expect($max_rand)->toBeGreaterThanOrEqualTo(0.9);
  }

  public static function provideTestIntRange(
  ): array<(int, int)> {
    return array(
      tuple(0, 0),
      tuple(0, StdInt::INT32_MAX),
      tuple(0, PHP_INT_MAX),
      tuple(StdInt::INT32_MIN, StdInt::INT32_MAX),
      tuple(-PHP_INT_MAX - 1, PHP_INT_MAX),
      tuple(-10, 0),
      tuple(-10, 10),
    );
  }

  /**
   * Tests that `int` is filling up the whole range. With 1000 repetitions, we
   * expect a uniform distribution to produce values outside the middle 80% of
   * the range with probability 1 - 0.8^1000 ~= 1 - 1e-97. So the probability of
   * this test failing should be negligible if `int` is uniform.
   *
   * This also ensures `int` is producing values in the correct range.
   */
  /** @dataProvider provideTestIntRange */
  public function testIntRange(int $min, int $max): void {
    $iterations = 1000;
    $midpoint = Math\mean($min, $max);
    $expected_min = $midpoint - 0.9 * ($midpoint - $min);
    $expected_max = $midpoint + 0.9 * ($max - $midpoint);

    $min_rand = PHP_INT_MAX;
    $max_rand = PHP_INT_MIN;
    for ($i = 0; $i < $iterations; $i++) {
      $rand = $this->getRandomInt($min, $max);
      expect($rand)->toBeLessThanOrEqualTo($max);
      expect($rand)->toBeGreaterThanOrEqualTo($min);
      $min_rand = Math\min($min_rand, $rand);
      $max_rand = Math\max($max_rand, $rand);
    }

    expect($min_rand)->toBeLessThanOrEqualTo($expected_min);
    expect($max_rand)->toBeGreaterThanOrEqualTo($expected_max);
  }

  public static function provideTestIntException(): array<(int, int)> {
    return array(
      tuple(0, -1),
      tuple(-1, -5),
      tuple(StdInt::INT32_MAX, 0),
      tuple(PHP_INT_MAX, PHP_INT_MIN),
    );
  }

  /** @dataProvider provideTestIntException */
  public function testIntException(int $min, int $max): void {
    expect(() ==> $this->getRandomInt($min, $max))
      ->toThrow(\InvariantViolationException::class);
  }

  public static function provideAlphabets(): array<mixed> {
    return array(
      tuple(Alphabet::BASE64),
      tuple(Alphabet::BASE64URL),
      tuple(Alphabet::ALPHANUMERIC),
    );
  }

  /**
   * Ensures that `string` returns strings of the correct length.
   */
  /** @dataProvider provideAlphabets */
  public function testStringLength(string $alphabet): void {
    $reps = 15;
    for ($length = 1; $length <= $reps; $length++) {
      expect(Str\length($this->getRandomString($length)))->toBeSame($length);
      expect(Str\length($this->getRandomString($length, $alphabet)))
        ->toBeSame($length);
    }
  }

  /**
   * Ensures that `string` returns strings containing the correct
   * characters.
   */
  /** @dataProvider provideAlphabets */
  public function testStringCharacters(string $alphabet): void {
    $length = 100;
    $valid_chars = keyset(Str\chunk($alphabet));
    foreach (Str\chunk($this->getRandomString($length, $alphabet)) as $char) {
      expect($valid_chars)->toContainKey($char);
    }
  }

  /**
   * Ensures that `string` returns all possible characters in all
   * possible positions.
   */
  /** @dataProvider provideAlphabets */
  public function testStringPositions(string $alphabet): void {
    $reps = 1000;
    $length = Str\length($alphabet);
    $found = Vec\map(
      Vec\range(0, $length - 1),
      $_idx ==> Dict\from_keys(Str\chunk($alphabet), $_char ==> false)
    );
    for ($i = 0; $i < $reps; $i++) {
      $data = $this->getRandomString($length, $alphabet);
      for ($j = 0; $j < $length; $j++) {
        $found[$j][$data[$j]] = true;
      }
    }

    expect(C\every(Vec\map($found, $char_found ==> C\every($char_found))))
      ->toBeTrue('Expected to find every character in every position');
  }
}
