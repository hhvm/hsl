<?hh // strict
/*
 *  Copyright (c) 2004-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the MIT license found in the
 *  LICENSE file in the root directory of this source tree.
 *
 */

/**
 * Iterator that implements the same behavior as generators when
 * Hack.Lang.AutoprimeGenerators is false
 */
final class HackLibTestForwardOnlyIterator<Tk as arraykey, Tv>
implements Iterator<Tv>, KeyedIterator<Tk, Tv> {
  private bool $used = false;
  private int $keyIdx = 0;
  private varray<Tk> $keys;

  public function __construct(private dict<Tk, Tv> $data) {
    $this->keys = array_keys($data);
  }

  public function current(): Tv  {
    $this->used = true;
    return $this->data[$this->keys[$this->keyIdx]];
  }

  public function key(): Tk {
    return $this->keys[$this->keyIdx];
  }

  public function rewind(): void {
    if ($this->used) {
      $this->next();
      $this->used = false;
    }
  }

  public function valid(): bool {
    return array_key_exists($this->keyIdx, $this->keys);
  }

  public function next(): void {
    $this->keyIdx++;
  }
}
