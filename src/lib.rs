use phper::arrays::ZArray;
use phper::classes::{ClassEntity, Visibility};
use phper::functions::Argument;
use phper::modules::Module;
use phper::php_get_module;
use std::convert::Infallible;

#[php_get_module]
pub fn get_module() -> Module {
    let mut module = Module::new(
        env!("CARGO_CRATE_NAME"),
        env!("CARGO_PKG_VERSION"),
        env!("CARGO_PKG_AUTHORS"),
    );

    module.add_class(make_php_parallel_class());

    module
}

const PARALLEL_CLASS_NAME: &str = "Parallel\\Parallel";

pub fn make_php_parallel_class() -> ClassEntity<()> {
    let mut class = ClassEntity::new(PARALLEL_CLASS_NAME);

    class.add_property("test", Visibility::Public, "test");

    class
        .add_method("run", Visibility::Public, |_this, _arguments| {
            let arr = ZArray::new();

            Ok::<_, Infallible>(arr)
        })
        .argument(Argument::by_val("var"));

    class
}
