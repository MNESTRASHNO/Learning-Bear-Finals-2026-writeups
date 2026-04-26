const std = @import("std");
const pg = @import("pg");

const db = @import("./db.zig");
const models = @import("./models.zig");
const httpz = @import("httpz");

var gpa = std.heap.GeneralPurposeAllocator(.{}){};
const allocator = gpa.allocator();

pub fn main() !void {
    const uri = try std.Uri.parse("postgresql://postgres:postgres@postgres:5432/");
    db.pool = try pg.Pool.initUri(allocator, uri, 5, 10_000);
    defer db.pool.deinit();
    var server = try httpz.Server().init(allocator, .{ .port = 5882, .address = "0.0.0.0" });

    server.errorHandler(errorHandler);

    var router = server.router();

    router.post("/api/user/", createUser);
    router.post("/api/user/login", loginUser);
    router.post("/api/note/create", createNote);
    router.post("/api/note/search", searchNotes);
    router.get("/api/note/all", getAllNotes);
    try server.listen();
}

const CreateUserRequest = struct {
    username: []const u8,
    password: []const u8,
};

fn createUser(req: *httpz.Request, res: *httpz.Response) !void {
    const data = req.json(CreateUserRequest) catch |err| {
        std.log.err("Failed to parse JSON: {}", .{err});
        return err;
    };

    if (data == null) {
        try res.json(.{ .status = "error", .message = "Invalid request" }, .{});
        return;
    }

    const username = data.?.username;
    const password = data.?.password;
    var hashedPassword: [64]u8 = undefined;
    var hashedBytes: [32]u8 = undefined;
    std.crypto.hash.sha2.Sha256.hash(password, &hashedBytes, .{});
    _ = std.fmt.bufPrint(&hashedPassword, "{s}", .{std.fmt.fmtSliceHexLower(&hashedBytes)}) catch unreachable;
    const user = models.createUser(username, &hashedPassword) catch |err| {
        std.log.err("Failed to create user: {}", .{err});
        try res.json(.{ .status = "error", .message = "Failed to create user" }, .{});
        return;
    };

    try res.json(.{ .status = "success", .data = user }, .{});
}

fn loginUser(req: *httpz.Request, res: *httpz.Response) !void {
    const data = req.json(CreateUserRequest) catch |err| {
        std.log.err("Failed to parse JSON: {}", .{err});
        return err;
    };

    if (data == null) {
        try res.json(.{ .status = "error", .message = "Invalid request" }, .{});
        return;
    }

    const username = data.?.username;
    const password = data.?.password;
    const user = models.getUser(username) catch |err| {
        std.log.err("Failed to get user: {}", .{err});
        return err;
    };

    const correctPassword = try user.verifyPassword(password);
    if (!correctPassword) {
        try res.json(.{ .status = "error", .message = "Invalid password" }, .{});
        return;
    }

    const token = try models.generateSession(user.id);
    const cookie = try std.fmt.allocPrint(allocator, "token={s}; Path=/; Secure; SameSite=none;", .{token});

    res.header("Set-Cookie", cookie);
    try res.json(.{ .status = "success", .data = user }, .{});
}

const CreateNoteRequest = struct {
    title: []const u8,
    content: []const u8,
};

fn getTokenFromCookie(cookie: []const u8) ![]const u8 {
    var split = std.mem.splitSequence(u8, cookie, "token=");
    _ = split.next();
    const cookieVal = split.next().?;
    if (cookieVal.len == 0) {
        return error.InvalidCookie;
    }
    split = std.mem.splitSequence(u8, cookieVal, ";");
    const token = split.next().?;
    if (token.len == 0) {
        return error.InvalidCookie;
    }
    return token;
}

fn createNote(req: *httpz.Request, res: *httpz.Response) !void {
    const data = req.json(CreateNoteRequest) catch |err| {
        std.log.err("Failed to parse JSON: {}", .{err});
        return err;
    };

    const cookie = req.header("cookie");
    if (cookie == null) {
        try res.json(.{ .status = "error", .message = "Unauthorized" }, .{});
        return;
    }

    const token = getTokenFromCookie(cookie.?) catch |err| {
        std.log.err("Failed to get token from cookie: {}", .{err});
        return err;
    };

    const user_id = models.sessionExists(token) catch |err| {
        std.log.err("Failed to get user id: {}", .{err});
        return err;
    };

    const note = models.createNote(user_id, data.?.title, data.?.content) catch |err| {
        std.log.err("Failed to create note: {}", .{err});
        return err;
    };

    try res.json(.{ .status = "success", .data = note }, .{});
}

const SearchNotesRequest = struct {
    query: []const u8,
};

fn searchNotes(req: *httpz.Request, res: *httpz.Response) !void {
    const cookie = req.header("cookie");
    if (cookie == null) {
        try res.json(.{ .status = "error", .message = "Unauthorized" }, .{});
        return;
    }

    const token = getTokenFromCookie(cookie.?) catch |err| {
        std.log.err("Failed to get token from cookie: {}", .{err});
        return err;
    };

    const user_id = models.sessionExists(token) catch |err| {
        std.log.err("Failed to get user id: {}", .{err});
        return err;
    };

    const data = req.json(SearchNotesRequest) catch |err| {
        std.log.err("Failed to parse JSON: {}", .{err});
        return err;
    };
    if (data == null) {
        try res.json(.{ .status = "error", .message = "Invalid request" }, .{});
        return;
    }

    const query = data.?.query;
    const notes = models.searchNotes(user_id, query) catch |err| {
        std.log.err("Failed to search notes: {}", .{err});
        return err;
    };

    try res.json(.{ .status = "success", .data = notes }, .{});
}

fn getAllNotes(req: *httpz.Request, res: *httpz.Response) !void {
    const cookie = req.header("cookie");
    if (cookie == null) {
        try res.json(.{ .status = "error", .message = "Unauthorized" }, .{});
        return;
    }

    const token = getTokenFromCookie(cookie.?) catch |err| {
        std.log.err("Failed to get token from cookie: {}", .{err});
        return err;
    };

    const user_id = models.sessionExists(token) catch |err| {
        std.log.err("Failed to get user id: {}", .{err});
        return err;
    };

    const notes = models.getAllNotes(user_id) catch |err| {
        std.log.err("Failed to search notes: {}", .{err});
        return err;
    };

    try res.json(.{ .status = "success", .data = notes }, .{});
}

fn notFound(_: *httpz.Request, res: *httpz.Response) !void {
    res.status = 404;
    res.body = "Not Found";
}

fn errorHandler(req: *httpz.Request, res: *httpz.Response, err: anyerror) void {
    res.status = 500;
    res.body = "Internal Server Error";
    std.log.warn("httpz: unhandled exception for request: {s}\nErr: {}", .{ req.url.raw, err });
}
