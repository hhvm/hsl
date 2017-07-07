# Hack Standard Library

The goal of the Hack Standard Library is to provide a consistent, centralized,
well-typed set of APIs for Hack programmers. We aim to achieve this by
implementing the library according to codified design principles.

This library is especially useful for working with the Hack arrays (`vec`,
`keyset`, and `dict`).

## Project Status

This is an experimental preview release. While we are not currently expecting to
be making major changes, the API should not be considered stable until a future
1.0 release, and backwards compatiblity will not be a concern of the developers
until that time.

New v0.x releases will be automatically made weekly, if there are changes and
the tests pass.

## Examples

WARNING: `use namespace` does not yet exist; it is a potential future feature.
For now, replace `use namespace HH\Lib\Dict;` with `use HH\Lib\Dict as DictHSL`
to avoid creating a collision with the `dict` type. The same approach is
also neccessary to work with the Vec and Keyset namespaces.

```Hack
<?hh // strict

use namespace HH\Lib\{Vec,Dict,Keyset,Str,Regex,Math};

function main(vec<?int> $foo): vec<string> {
  return $foo
    |> Vec\filter_nulls($$)
    |> Vec\map($$, $it ==> (string) $it);
}
```

For a real-world example, see [bin/generate-docs.php](bin/generate-docs.php).

## Finding Functions

Functions in the HSL are organized into namespaces according to the following
rule:

If a function returns a particular type or only operates on that type, it
belongs in that namespace.

Here are some examples:

### "I want a vec containing the keys of a particular container."

Based on the `vec` return type, you'd look in the Vec namespace and come across
`Vec\keys`. Instead, if you wanted a keyset, you'd look in the Keyset namespace
and find `Keyset\keys`.

### "I want the largest value in a particular container."

Because the function isn't returning any particular container, you'd look in
the C namespace and find `C\max` and `C\max_by`.

## Full Documentation

Automatically-generated documentation is available at
[https://hhvm.github.io/hsl/api/](https://hvm.github.io/hsl/api/').

## Installation

This project uses function autoloading, so requires that your projects use
[hhvm-autoload](https://github.com/hhvm/hhvm-autoload) instead of Composer's
built-in autoloading; if you are not already using hhvm-autoload, you will need
to add an
[hh_autoload.json](https://github.com/hhvm/hhvm-autoload#configuration-hh_autoloadjson)
to your project first.

```
$ composer require hhvm/hsl
```

## Principles

 - All functions should be typed as strictly as possible in Hack
 - The library should be internally consistent
 - References may not be used
 - Arguments should be as general as possible. For example, for Hack array
   functions, prefer `Traversable`/`KeyedTraversable` inputs where practical,
   falling back to `Container`/`KeyedContainer` when needed
 - Return types should be as specific as possible
 - All files should be `<?hh // strict`

### Consistency Rules

This is not an exhaustive list.

 - Functions argument order should be consistent within the library
   - All container-related functions take the container as the first argument
     (e.g. `Vec\map()` and `Vec\filter()`)
   - `$haystack`, `$needle`, and `$pattern` are in the same order for all
     functions that take them
 - Functions should be consistently named
 - If an operation can conceivably operate on either keys or values, the default
   is to operate on the values - the version that operates on keys should have
   a `_key` suffix (e.g. `C\find()`, `C\find_key()`,
   `C\contains()`, `C\contains_key()`)
 - Find-like operations that can fail should return `?T`; a second function
   should be added with an `x` suffix that uses an invariant to return `T`
   (e.g. `C\first()`, `C\firstx()`)
 - Container functions that do an operation based on a user-supplied keying
   function for each element should be suffixed with `_by` (e.g.
   `Vec\sort_by()`, `Dict\group_by()`, `C\max_by()`)

## Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md); in particular, we are not currently
accepting feature requests or pull requests that add new features. Long-term,
we will continue to be extremely conservative about new features.

## License

The Hack Standard Library is BSD-licensed. We also provide an additional patent
grant.
