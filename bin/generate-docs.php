<?hh
/*
 *  Copyright (c) 2004-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the BSD-style license found in the
 *  LICENSE file in the root directory of this source tree. An additional grant
 *  of patent rights can be found in the PATENTS file in the same directory.
 *
 */

namespace HH\Lib\_Private;

use Facebook\DefinitionFinder\{
  ScannedFunction,
  ScannedGeneric,
  ScannedParameter,
  ScannedTypehint,
  TreeParser
};
use namespace HH\Lib\{C, Dict, Str, Vec};

require_once(__DIR__.'/../vendor/autoload.php');
require_once(__DIR__.'/../vendor/hh_autoload.php');

final class DocsGen {
  const int TARGET_LINE_LENGTH = 80;

  public function createInDirectory($path): void {
    if (!is_dir($path)) {
      fprintf(STDERR, "%s is not a directory\n", $path);
      exit(1);
    }

    $namespaces_funcs = TreeParser::FromPath(__DIR__.'/../src/')
      ->getFunctions()
      |>Dict\group_by($$, $f ==> $f->getNamespaceName())
      |>Dict\sort_by_key($$)
      |>Dict\filter_keys(
        $$,
        $ns ==> $ns !== '' && $ns !== "HH\\Lib\\_Private",
      );

    foreach ($namespaces_funcs as $ns => $funcs) {
      $file = $path.'/'.C\lastx(Str\split("\\", $ns)).'.md';
      file_put_contents(
        $file,
        $this->getMarkdownForNamespace($ns, $funcs),
      );
      printf("%s\n", $file);
    }

    $namespaces_funcs
      |>Vec\keys($$)
      |>Vec\map(
        $$,
        $ns ==> sprintf(
          " - [%s](%s)",
          $ns,
          C\lastx(Str\split("\\", $ns)).'.md',
        ),
      )
      |>Str\join("\n", $$)
      |>file_put_contents(
        $path.'/index.md',
        "# Hack Standard Library API Reference\n\n".$$."\n",
      );
    printf("%s\n", $path.'/index.md');
  }

  private function getMarkdownForNamespace(
    string $ns,
    vec<ScannedFunction> $funcs,
  ): string {
    $funcs = Vec\sort_by($funcs, $f ==> $f->getShortName());
    $short_ns = Str\strip_prefix($ns, "HH\\Lib\\");
    $out = sprintf("# %s\n\n", $ns);

    // Build table of contents
    foreach ($funcs as $func) {
      $name = $short_ns."\\".$func->getShortName();
      // GitHub automatically puts anchors for headers
      $url = $name
        |>Str\replace($$, "\\", '')
        |>Str\lowercase($$)
        |>'#'.$$;
      $out .= sprintf(" - [%s](%s)\n", $name, $url);
    }

    foreach ($funcs as $func) {
      $out .= sprintf(
        "\n## %s()\n\n",
        $short_ns."\\".$func->getShortName(),
      );
      $out .= "```Hack\n";
      $out .= $this->renderSignature($func);
      $out .= "```\n";

      $out .= "\n".$this->renderDocComment($func);
    }
    return $out;
  }

  private function renderSignature(ScannedFunction $f): string {
    $multiline_opts = vec[
      tuple(false, false),
      tuple(false, true),
    ];

    // Only try multiline generics if we get a substantial saving
    if (Str\length((string) $this->renderGenerics($f, false)) >= 16) {
      $multiline_opts[] = tuple(true, true);
    }

    $out = '';
    foreach ($multiline_opts as $opt) {
      $out = $this->renderSignatureImpl(
        $f,
        $opt[0],
        $opt[1],
      );

      if (
        C\every(
          Str\split("\n", $out),
          $s ==> Str\length($s) < self::TARGET_LINE_LENGTH,
        )
      ) {
        return $out;
      }
    }

    return $out;
  }

  private function renderSignatureImpl(
    ScannedFunction $f,
    bool $multiline_generics,
    bool $multiline_parameters,
  ): string {
    return 
      'function '.
      $f->getShortName().
      $this->renderGenerics($f, $multiline_generics).
      $this->renderParameters($f, $multiline_parameters).
      ': '.
      $this->renderType($f->getReturnType()).
      "\n";
  }

  private function renderGenerics(
    ScannedFunction $f,
    bool $multiline,
  ): ?string {
    $g = $f->getGenericTypes();
    if (C\is_empty($g)) {
      return null;
    }

    $parts = Vec\map($g, $g ==> $this->renderGeneric($g));

    if ($multiline) {
      return "<\n  ".Str\join(",\n  ", $parts)."\n>";
    }
    return '<'.Str\join(', ', $parts).'>';
  }

  private function renderGeneric(ScannedGeneric $g): string {
    $out = $g->getName();
    foreach ($g->getConstraints() as $constraint) {
      $out .= sprintf(
        ' %s %s',
        $constraint['relationship'],
        $constraint['type'],
      );
    }

    invariant(
      !$g->isCovariant(),
      "I'm lazy, and have a covariant generic",
    );
    invariant(
      !$g->isContravariant(),
      "I'm lazy, and have a contravariant generic",
    );
    return $out;
  }

  private function renderParameters(
    ScannedFunction $f,
    bool $multiline_parameters,
  ): string {
    if (C\is_empty($f->getParameters())) {
      return '()';
    }
    $parts = Vec\map($f->getParameters(), $p ==> $this->renderParameter($p));
    if ($multiline_parameters) {
      $trailing_comma = Str\contains(C\lastx($parts), '...') ? '' : ',';
      return "(\n  ".Str\join(",\n  ", $parts).$trailing_comma."\n)";
    }
    return '('.Str\join(', ', $parts).')';
  }

  private function renderParameter(ScannedParameter $p): string {
    $type = $this->renderType($p->getTypehint());
    if ($p->isVariadic()) {
      return $type.' ...$'.$p->getName();
    }
    if ($p->isOptional()) {
      return $type.' $'.$p->getName().' = '.$p->getDefaultString();
    }
    return $type.' $'.$p->getName();
  }

  private function renderType(?ScannedTypehint $t): string {
    invariant(
      $t !== null,
      "All HSL functions should have typed parameters and returns",
    );
    $text = $t->getTypeText();

    $fixups = vec[
      "\\(", // https://github.com/hhvm/definition-finder/issues/3
      "\\tuple(", // https://github.com/hhvm/definition-finder/issues/4
    ];
    foreach ($fixups as $fixup) {
      $pos = Str\search($text, $fixup);
      if ($pos !== null) {
        $text = Str\slice($text, $pos + 1);
      }
    }

    if ($t->isNullable()) {
      invariant(
        Str\starts_with($text, '?'),
        "'%s' is nullable, but doesn't start with '?'",
        $text,
      );
    }
    return $text;
  }

  private function renderDocComment(ScannedFunction $f): ?string {
    $c = $f->getDocComment();
    if ($c === null || $c === '') {
      fprintf(STDERR, "%s needs a doc comment\n", $f->getName());
      return null;
    }
    return $c
      |>Str\strip_prefix($$, '/**')
      |>Str\strip_suffix($$, '*/')
      |>Str\trim($$)
      |>Str\split("\n", $$)
      |>Vec\map($$, $s ==> Str\trim(Str\strip_prefix(Str\trim($s), '*')))
      |>Str\join("\n", $$)
      |>$$."\n";
  }
}

// Allow inclusion from UpdateReleaseScript
if (realpath($argv[0]) !== realpath(__FILE__)) {
  return;
}

$path = $argv[1] ?? null;
if ($path === null) {
  fprintf(STDERR, "Usage: %s OUTPUT_DIRECTORY\n", $argv[0]);
  exit(1);
}
(new DocsGen())->createInDirectory($path);
