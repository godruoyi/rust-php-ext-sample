mod parallel;
mod hello;

use phper::modules::Module;
use phper::php_get_module;
use crate::parallel::make_php_parallel_class;

#[php_get_module]
pub fn get_module() -> Module {
    let mut module = Module::new(
        env!("CARGO_CRATE_NAME"),
        env!("CARGO_PKG_VERSION"),
        env!("CARGO_PKG_AUTHORS"),
    );

    module.add_function("say_hello", hello::say_hello);

    module.add_class(make_php_parallel_class());

    module
}