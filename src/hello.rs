use phper::echo;
use phper::values::ZVal;

pub fn say_hello(arguments: &mut [ZVal]) -> phper::Result<()> {
    // Get the first argument, expect the type `ZStr`, and convert to Rust utf-8
    // str.
    let name = arguments[0].expect_z_str()?.to_str()?;

    // Macro which do php internal `echo`.
    echo!("Hello, {}!\n", name);

    Ok(())
}