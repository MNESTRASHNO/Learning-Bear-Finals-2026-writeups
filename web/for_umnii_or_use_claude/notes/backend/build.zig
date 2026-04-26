const std = @import("std");

pub fn build(b: *std.Build) void {
    const target = b.standardTargetOptions(.{});
    const exe = b.addExecutable(.{ .name = "app", .root_source_file = b.path("./main.zig"), .target = target });
    b.installArtifact(exe);

    exe.linkSystemLibrary("c");
    // ...

    // const json_mod = b.dependency("json", .{ .target = target }).module("json");
    // exe.root_module.addImport("json", json_mod);

    const pg = b.dependency("pg", .{ .target = target }).module("pg");
    exe.root_module.addImport("pg", pg);

    const httpz = b.dependency("httpz", .{ .target = target }).module("httpz");
    exe.root_module.addImport("httpz", httpz);
}
