<?hh // strict
// Copyright 2004-present Facebook. All Rights Reserved.

namespace HH\Lib\_Private;
use HH\Lib\Math;
use HH\Lib\Str;

function random_bool(
  (function (int, int): int) $random_int,
  int $rate,
): bool {
  invariant($rate >= 0, 'Expected non-negative rate');
  if ($rate === 0) {
    return false;
  }
  return $random_int(1, $rate) === 1;
}

function random_float(
  (function (int, int): int) $random_int,
): float {
  return (float)($random_int(0, \StdInt::INT53_MAX) / \StdInt::INT53_MAX);
}

function random_int(
  (function (int, int): int) $random_int,
  int $min,
  int $max,
): int {
  invariant(
    $min <= $max,
    'Expected $min (%d) to be less than or equal to $max (%d)',
    $min,
    $max,
  );
  return $random_int($min, $max);
}

function random_string(
  (function (int): string) $random_bytes,
  int $length,
  ?string $alphabet = null,
): string {
  invariant($length >= 0, 'Expected positive length, got %d', $length);
  if ($length === 0) {
    return '';
  }
  if ($alphabet === null) {
    return $random_bytes($length);
  }
  $alphabet_size = Str\length($alphabet);
  $bits = (int)Math\ceil(Math\log($alphabet_size, 2));
  // I do not expect us to have an alphabet with 2^56 characters. It is still
  // nice to have an upper bound, though, to avoid overflowing $unpacked_data
  invariant(
    $bits >= 1 && $bits <= 56,
    'Expected $alphabet\'s length to be in [2^1, 2^56]',
  );

  $ret = '';
  while ($length > 0) {
    // Generate twice as much data as we technically need. This is like
    // guessing "how many times do I need to flip a coin to get N heads?" I'm
    // guessing probably no more than 2N.
    $urandom_length = (int)Math\ceil(2 * $length * $bits / 8.0);
    $data = $random_bytes($urandom_length);

    $unpacked_data = 0; // The unused, unpacked data so far
    $unpacked_bits = 0; // A count of how many unused, unpacked bits we have
    for ($i = 0; $i < $urandom_length && $length > 0; ++$i) {
      // Unpack 8 bits
      $unpacked_data = ($unpacked_data << 8) | \unpack('C', $data[$i])[1];
      $unpacked_bits += 8;

      // While we have enough bits to select a character from the alphabet, keep
      // consuming the random data
      for (; $unpacked_bits >= $bits && $length > 0; $unpacked_bits -= $bits) {
        $index = ($unpacked_data & ((1 << $bits) - 1));
        $unpacked_data >>= $bits;
        // Unfortunately, the alphabet size is not necessarily a power of two.
        // Worst case, it is 2^k + 1, which means we need (k+1) bits and we
        // have around a 50% chance of missing as k gets larger
        if ($index < $alphabet_size) {
          $ret .= $alphabet[$index];
          --$length;
        }
      }
    }
  }

  return $ret;
}
