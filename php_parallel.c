#ifdef HAVE_CONFIG_H
# include "config.h"
#endif

#include "php.h"
#include "ext/standard/info.h"

// Empty PHP module - actual implementation is in Rust with phper
zend_module_entry php_parallel_module_entry = {
    STANDARD_MODULE_HEADER,
    "php_parallel",
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    "0.1.0",
    STANDARD_MODULE_PROPERTIES
};

ZEND_GET_MODULE(php_parallel)