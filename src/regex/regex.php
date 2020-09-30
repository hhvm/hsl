<?hh // strict
/*
 *  Copyright (c) 2004-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the MIT license found in the
 *  LICENSE file in the root directory of this source tree.
 *
 */

namespace HH\Lib\Regex;

use namespace HH\Lib\{_Private, Str};

/**
 * Returns the first match found in `$haystack` given the regex pattern `$pattern`
 * and an optional offset at which to start the search.
 * The regex pattern follows the PCRE library: https://www.pcre.org/original/doc/html/pcresyntax.html.
 *
 * Throws Invariant[Violation]Exception if `$offset` is not within plus/minus the length of `$haystack`.
 * Returns null if there is no match, or a Match containing
 *    - the entire matching string, at key 0,
 *    - the results of unnamed capture groups, at integer keys corresponding to
 *        the groups' occurrence within the pattern, and
 *    - the results of named capture groups, at string keys matching their respective names.
 */
<<__Rx>>
function first_match<T as Match>(
  string $haystack,
  Pattern<T> $pattern,
  int $offset = 0,
): ?T {
  return _Private\regex_match($haystack, $pattern, $offset)[0] ?? null;
}

/**
 * Returns all matches found in `$haystack` given the regex pattern `$pattern`
 * and an optional offset at which to start the search.
 * The regex pattern follows the PCRE library: https://www.pcre.org/original/doc/html/pcresyntax.html.
 *
 * Throws Invariant[Violation]Exception if `$offset` is not within plus/minus the length of `$haystack`.
 */
<<__Rx>>
function every_match<T as Match>(
  string $haystack,
  Pattern<T> $pattern,
  int $offset = 0,
): vec<T> {
  $haystack_length = Str\length($haystack);
  $result = vec[];
  while (true) {
    $match = _Private\regex_match($haystack, $pattern, $offset);
    if ($match === null) {
      break;
    }
    $captures = $match[0];
    $result[] = $captures;
    $match_begin = $match[1];
    $match_length = Str\length(Shapes::at($captures, 0) as string);
    if ($match_length === 0) {
      $offset = $match_begin + 1;
      if ($offset > $haystack_length) {
        break;
      }
    } else {
      $offset = $match_begin + $match_length;
    }
  }
  return $result;
}

/**
 * Returns whether a match exists in `$haystack` given the regex pattern `$pattern`
 * and an optional offset at which to start the search.
 * The regex pattern follows the PCRE library: https://www.pcre.org/original/doc/html/pcresyntax.html.
 *
 * Throws Invariant[Violation]Exception if `$offset` is not within plus/minus the length of `$haystack`.
 */
<<__Rx>>
function matches(
  string $haystack,
  Pattern<Match> $pattern,
  int $offset = 0,
): bool {
  return _Private\regex_match($haystack, $pattern, $offset) !== null;
}

/**
 * Returns `$haystack` with any substring matching `$pattern`
 * replaced by `$replacement`. If `$offset` is given, replacements are made
 * only starting from `$offset`.
 * The regex pattern follows the PCRE library: https://www.pcre.org/original/doc/html/pcresyntax.html.
 *
 * Throws Invariant[Violation]Exception if `$offset` is not within plus/minus the length of `$haystack`.
 */
function replace(
  string $haystack,
  Pattern<Match> $pattern,
  string $replacement,
  int $offset = 0,
): string {
  // replace is the only one of these functions that calls into a preg
  // function other than preg_match. It needs to call into preg_replace
  // to be able to handle backreferencing in the `$replacement` string.
  // preg_replace does not support offsets, so we handle them ourselves,
  // consistently with _Private\regex_match.
  $offset = _Private\validate_offset($offset, Str\length($haystack));
  $haystack1 = Str\slice($haystack, 0, $offset);
  $haystack2 = Str\slice($haystack, $offset);

  using new _Private\PHPWarningSuppressor();
  /* HH_FIXME[2049] __PHPStdLib */
  /* HH_FIXME[4107] __PHPStdLib */
  $haystack3 = \preg_replace($pattern, $replacement, $haystack2);
  if ($haystack3 === null) {
    /* HH_FIXME[2049] __PHPStdLib */
    /* HH_FIXME[4107] __PHPStdLib */
    throw new Exception($pattern, \preg_last_error());
  }
  return $haystack1.$haystack3;
}

/**
 * Returns `$haystack` with any substring matching `$pattern`
 * replaced by the result of `$replace_func` applied to that match.
 * If `$offset` is given, replacements are made only starting from `$offset`.
 * The regex pattern follows the PCRE library: https://www.pcre.org/original/doc/html/pcresyntax.html.
 *
 * Throws Invariant[Violation]Exception if `$offset` is not within plus/minus the length of `$haystack`.
 */
<<__Rx, __AtMostRxAsArgs>>
function replace_with<T as Match>(
  string $haystack,
  Pattern<T> $pattern,
  <<__AtMostRxAsFunc>> (function(T): string) $replace_func,
  int $offset = 0,
): string {
  $haystack_length = Str\length($haystack);
  $result = Str\slice($haystack, 0, 0);
  $match_end = 0;
  while (true) {
    $match = _Private\regex_match($haystack, $pattern, $offset);
    if ($match === null) {
      break;
    }
    $captures = $match[0];
    $match_begin = $match[1];
    // Copy anything between the previous match and this one
    $result .= Str\slice($haystack, $match_end, $match_begin - $match_end);
    $result .= $replace_func($captures);
    $match_length = Str\length(Shapes::at($captures, 0) as string);
    $match_end = $match_begin + $match_length;
    if ($match_length === 0) {
      // To get the next match (and avoid looping forever), need to skip forward
      // before searching again
      // Note that `$offset` is for searching and `$match_end` is for copying
      $offset = $match_begin + 1;
      if ($offset > $haystack_length) {
        break;
      }
    } else {
      $offset = $match_end;
    }
  }
  $result .= Str\slice($haystack, $match_end);
  return $result;
}

/**
 * Splits `$haystack` into chunks by its substrings that match with `$pattern`.
 * If `$limit` is given, the returned vec will have at most that many elements.
 * The last element of the vec will be whatever is left of the haystack string
 * after the appropriate number of splits.
 * If no substrings of `$haystack` match `$delimiter`, a vec containing only `$haystack` will be returned.
 * The regex pattern follows the PCRE library: https://www.pcre.org/original/doc/html/pcresyntax.html.
 *
 * Throws Invariant[Violation]Exception if `$limit` < 2.
 */
<<__Rx>>
function split(
  string $haystack,
  Pattern<Match> $delimiter,
  ?int $limit = null,
): vec<string> {
  if ($limit === null) {
    $limit = \INF;
  }
  invariant(
    $limit > 1,
    'Expected limit greater than 1, got %d.',
    $limit,
  );
  $haystack_length = Str\length($haystack);
  $result = vec[];
  $offset = 0;
  $match_end = 0;
  $count = 1;
  $match = _Private\regex_match($haystack, $delimiter, $offset);
  while ($match && $count < $limit) {
    $captures = $match[0];
    $match_begin = $match[1];
    // Copy anything between the previous match and this one
    $result[] = Str\slice($haystack, $match_end, $match_begin - $match_end);
    $match_length = Str\length(Shapes::at($captures, 0) as string);
    $match_end = $match_begin + $match_length;
    if ($match_length === 0) {
      // To get the next match (and avoid looping forever), need to skip forward
      // before searching again
      // Note that `$offset` is for searching and `$match_end` is for copying
      $offset = $match_begin + 1;
      if ($offset > $haystack_length) {
        break;
      }
    } else {
      $offset = $match_end;
    }
    $count++;
    $match = _Private\regex_match($haystack, $delimiter, $offset);
  }
  $result[] = Str\slice($haystack, $match_end);
  return $result;
}

/**
 * Renders a Regex Pattern to a string.
 * The regex pattern follows the PCRE library: https://www.pcre.org/original/doc/html/pcresyntax.html.
 */
<<__Pure>>
function to_string(Pattern<Match> $pattern): string {
  return $pattern as string;
}
