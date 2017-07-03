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

abstract final class HackLibTestTraversables {

  // For testing functions that accept Traversables
  public static function getIterator<T>(Traversable<T> $ary): Iterator<T> {
    foreach ($ary as $v) {
      yield $v;
    }
  }

  // For testing functions that accept KeyedTraversables
  public static function getKeyedIterator<Tk, Tv>(
    KeyedTraversable<Tk, Tv> $ary,
  ): KeyedIterator<Tk, Tv> {
    foreach ($ary as $k => $v) {
      yield $k => $v;
    }
  }
}
