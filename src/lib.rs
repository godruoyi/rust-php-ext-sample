use phper::functions::Argument;
use phper::modules::Module;
use phper::values::ZVal;
use phper::{echo, php_get_module};

fn say_hello(arguments: &mut [ZVal]) -> phper::Result<String> {
    let name = arguments[0].expect_z_str()?.to_str()?;

    Ok(format!("Hello, {}!", name))
}

#[php_get_module]
pub fn get_module() -> Module {
    let mut module = Module::new(
        env!("CARGO_CRATE_NAME"),
        env!("CARGO_PKG_VERSION"),
        env!("CARGO_PKG_AUTHORS"),
    );

    module
        .add_function("rust_php_ext_sample", say_hello)
        .argument(Argument::by_val("name"));

    module
}
