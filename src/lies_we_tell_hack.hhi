<?hh // strict
/*
 *  Copyright (c) 2004-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the MIT license found in the
 *  LICENSE file in the root directory of this source tree.
 *
 */

namespace HH {
  class InvariantException extends \InvariantException {} // @oss-enable
  type KeyedContainer<Tk, Tv> = \KeyedContainer<Tk, Tv>;
}

namespace {
  const POSIX_S_IFMT = 0;
  const POSIX_S_IFREG = 0;
  const POSIX_S_IFDIR = 0;
  const POSIX_S_IFLNK = 0;
}
