test( "hello test", function() {
ok( 1 == "1", "Passed!" );
});

test("Render Calendar", function() {
   var cal = oktolab.calendar();
   console.log(typeof cal);
   ok("object" === typeof cal, "Can create Oktolab.calendar");
});

