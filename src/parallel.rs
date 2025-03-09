use phper::arrays::{InsertKey, ZArray};
use phper::classes::{ClassEntity, Visibility};
use phper::functions::Argument;
use phper::values::ZVal;
use std::sync::{Arc, Mutex};
use std::thread;

const PARALLEL_CLASS_NAME: &str = "Parallel\\Parallel";

pub fn make_php_parallel_class() -> ClassEntity<()> {
    let mut class = ClassEntity::new(PARALLEL_CLASS_NAME);

    class
        .add_method("run", Visibility::Public, |_this, arguments| {
            let functions: Vec<_> = arguments[0].as_z_arr().unwrap().iter().collect();
            let results = Arc::new(Mutex::new(vec![ZVal::default(); functions.len()]));
            let mut handles = Vec::with_capacity(functions.len());

            // Spawn threads to execute each function
            for (i, (_, f)) in functions.into_iter().enumerate() {
                let mut f_clone = f.clone();
                let results_clone = Arc::clone(&results);

                let handle = thread::spawn(move || {
                    let result = f_clone.call([]).unwrap();
                    let mut results = results_clone.lock().unwrap();
                    results[i] = result;
                });

                handles.push(handle);
            }

            // Wait for all threads to complete
            for handle in handles {
                if let Err(e) = handle.join() {
                    eprintln!("Thread panicked: {:?}", e);
                }
            }

            // Create return array with the results
            let mut return_array = ZArray::new();
            let results = results.lock().unwrap();
            for (i, result) in results.iter().enumerate() {
                return_array.insert(InsertKey::Index(i as u64), result.clone());
            }

            Ok(return_array.into())
        })
        .argument(Argument::by_val("functions"));

    class
}
