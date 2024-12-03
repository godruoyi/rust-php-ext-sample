use phper::classes::{ClassEntity, Visibility};

const PARALLEL_CLASS_NAME: &str = "Parallel\\Parallel";

pub fn make_php_parallel_class() -> ClassEntity<()> {
    let mut class = ClassEntity::new(PARALLEL_CLASS_NAME);

    class.add_method("run", Visibility::Public, |_, _| {
        Ok::<_, phper::Error>(())
    });

    class
}