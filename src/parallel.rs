use phper::arrays::{InsertKey, ZArray};
use phper::classes::{ClassEntity, Visibility};
use phper::functions::Argument;
use phper::values::ZVal;
use phper::{echo, ok};
use rayon::prelude::*;

const PARALLEL_CLASS_NAME: &str = "Parallel\\Parallel";

pub fn make_php_parallel_class() -> ClassEntity<()> {
    let mut class = ClassEntity::new(PARALLEL_CLASS_NAME);

    class
        .add_method("run", Visibility::Public, |_this, arguments| {
            let mut functions = arguments[0].expect_z_arr()?.to_owned();

            let result: Vec<_> = functions
                .iter_mut()
                .map(|(_, argument)| argument.call([]).unwrap().to_owned())
                .collect();

            let mut arr = ZArray::new();

            for r in result {
                arr.insert(
                    InsertKey::NextIndex,
                    r.expect_z_str().unwrap().to_str().unwrap(),
                );
            }

            ok(arr)
        })
        .argument(Argument::by_val("fn"));

    class
}
