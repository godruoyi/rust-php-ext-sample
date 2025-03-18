dnl config.m4 for extension php_parallel
PHP_ARG_ENABLE([php_parallel],
  [whether to enable php_parallel support],
  [AS_HELP_STRING([--enable-php_parallel],
    [Enable php_parallel support])],
  [no])

if test "$PHP_PHP_PARALLEL" != "no"; then
  dnl Check for Rust compiler
  AC_PATH_PROG(CARGO, cargo, no)
  if test "$CARGO" = "no"; then
    AC_MSG_ERROR([cargo not found. Please install Rust and cargo first.])
  fi

  dnl Set up build environment
  AC_MSG_CHECKING([for Rust extension build])

  dnl Build the Rust library
  $CARGO build --release
  if test $? -ne 0; then
    AC_MSG_ERROR([Failed to build Rust library])
  fi

  dnl Get extension directory and file
  PHP_ADD_LIBRARY_WITH_PATH(php_parallel_rust, $PWD/target/release, PHP_PARALLEL_SHARED_LIBADD)

  PHP_NEW_EXTENSION(php_parallel, [php_parallel.c], $ext_shared)
  PHP_SUBST(PHP_PARALLEL_SHARED_LIBADD)

  AC_MSG_RESULT([yes])
fi