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

/**
 * Handy functions to create iterators.
 *
 * Not using generators to be compatible with code that is explicitly setting
 * Hack.Lang.AutoprimeGenerators to false.
 */
abstract final class HackLibTestTraversables {

  // For testing functions that accept Traversables
  public static function getIterator<T>(Traversable<T> $ary): Iterator<T> {
    $dict = dict[];
    $i = 0;
    foreach ($ary as $v) {
      $dict[$i] = $v;
      $i++;
    }
    return new HackLibTestForwardOnlyIterator($dict);;
  }

  // For testing functions that accept KeyedTraversables
  public static function getKeyedIterator<Tk, Tv>(
    KeyedTraversable<Tk, Tv> $ary,
  ): KeyedIterator<Tk, Tv> {
    return new HackLibTestForwardOnlyIterator(dict($ary));
  }
}
