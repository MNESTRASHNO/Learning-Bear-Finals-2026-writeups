const std = @import("std");
const db = @import("./db.zig");

var gpa = std.heap.GeneralPurposeAllocator(.{}){};
const allocator = gpa.allocator();

const User = struct {
    id: i32,
    username: []const u8,
    password: []const u8,
    pub fn verifyPassword(self: User, password: []const u8) !bool {
        var hashedPassword: [64]u8 = undefined;
        var hashedBytes: [32]u8 = undefined;
        std.crypto.hash.sha2.Sha256.hash(password, &hashedBytes, .{});
        _ = std.fmt.bufPrint(&hashedPassword, "{s}", .{std.fmt.fmtSliceHexLower(&hashedBytes)}) catch unreachable;

        return std.mem.eql(u8, &hashedPassword, self.password);
    }
};

const Note = struct {
    id: i32,
    owner_id: i32,
    title: []const u8,
    content: []const u8,
};

const Session = struct {
    user_id: i32,
    token: []const u8,
};

pub fn sessionExists(token: []const u8) !i32 {
    var query = db.pool.query("SELECT user_id FROM sessions WHERE token = $1", .{token}) catch |err| {
        std.log.err("Failed to query database: {}", .{err});
        return err;
    };
    defer query.deinit();

    const row = try query.next();
    if (row == null) {
        return error.SessionNotFound;
    }

    const user_id = row.?.get(i32, 0);
    return user_id;
}

pub fn generateSession(user_id: i32) ![]const u8 {
    const random_bytes = try allocator.alloc(u8, 32);
    _ = std.crypto.random.bytes(random_bytes);

    var token: [64]u8 = undefined;
    _ = std.fmt.bufPrint(&token, "{s}", .{std.fmt.fmtSliceHexLower(random_bytes)}) catch unreachable;

    var query = db.pool.query("INSERT INTO sessions (user_id, token) VALUES ($1, $2) RETURNING token", .{ user_id, &token }) catch |err| {
        std.log.err("Failed to query database: {}", .{err});
        return err;
    };
    defer query.deinit();

    var row = query.next() catch |err| {
        std.log.err("Failed to get next row: {}", .{err});
        return err;
    };

    if (row == null) {
        return error.SessionCreationFailed;
    }

    const token_temp = row.?.get([]const u8, 0);

    const tok = try allocator.dupe(u8, token_temp);

    return @as([]const u8, tok);
}

pub fn userExists(username: []const u8) !bool {
    var query = db.pool.query("SELECT username FROM users WHERE username = $1", .{username}) catch |err| {
        std.log.err("Failed to query database: {}", .{err});
        return err;
    };

    defer query.deinit();
    const row = try query.next();
    if (row == null) {
        return false;
    }
    return true;
}

pub fn createUser(username: []const u8, password: []const u8) !User {
    const exists = try userExists(username);
    if (exists) {
        return error.UserAlreadyExists;
    }

    var query = db.pool.query("INSERT INTO users (username, password) VALUES ($1, $2) RETURNING id, username", .{ username, password }) catch |err| {
        std.log.err("Failed to query database: {}", .{err});
        return err;
    };
    defer query.deinit();

    var row = query.next() catch |err| {
        std.log.err("Failed to get next row: {}", .{err});
        return err;
    };
    const resId = row.?.get(i32, 0);
    const resUsername = row.?.get([]const u8, 1);

    if (std.mem.eql(u8, username, resUsername)) {
        return User{ .id = resId, .username = username, .password = password };
    }
    return undefined;
}

pub fn getUser(username: []const u8) !User {
    var query = db.pool.query("SELECT id, username, password FROM users WHERE username = $1", .{username}) catch |err| {
        std.log.err("Failed to query database: {}", .{err});
        return err;
    };
    defer query.deinit();

    var row = try query.next();
    if (row == null) {
        return error.UserNotFound;
    }
    const id_temp = row.?.get(i32, 0);
    const resUsername_temp = row.?.get([]const u8, 1);
    const password_temp = row.?.get([]const u8, 2);

    const id = try allocator.dupe(i32, &[_]i32{id_temp});
    const resUsername = try allocator.dupe(u8, resUsername_temp);
    const password = try allocator.dupe(u8, password_temp);

    return User{
        .id = id[0],
        .username = resUsername,
        .password = password,
    };
}

pub fn createNote(owner_id: i32, title: []const u8, content: []const u8) !Note {
    const encoder = std.base64.standard_no_pad.Encoder;
    const bufSize = encoder.calcSize(content.len);
    const buf = try allocator.alloc(u8, bufSize);
    const encoded_content = encoder.encode(buf, content);
    defer allocator.free(buf);

    var query = db.pool.query("INSERT INTO notes (owner_id, title, content) VALUES ($1, $2, $3) RETURNING id, owner_id, title, content", .{ owner_id, title, encoded_content }) catch |err| {
        std.log.err("Failed to query database: {}", .{err});
        return err;
    };
    defer query.deinit();

    var row = try query.next();
    if (row == null) {
        return error.NoteNotFound;
    }

    const id = row.?.get(i32, 0);
    const oi = row.?.get(i32, 1);
    const t = row.?.get([]const u8, 2);
    const c = row.?.get([]const u8, 3);
    const bufLen = try std.base64.standard_no_pad.Decoder.calcSizeUpperBound(c.len);
    const buf2 = try allocator.alloc(u8, bufLen);
    try std.base64.standard_no_pad.Decoder.decode(buf2, c);
    defer allocator.free(buf2);

    const oi_temp = try allocator.dupe(i32, &[_]i32{oi});
    const id_temp = try allocator.dupe(i32, &[_]i32{id});
    const t_temp = try allocator.dupe(u8, t);
    const c_temp = try allocator.dupe(u8, buf2);

    return Note{ .id = id_temp[0], .owner_id = oi_temp[0], .title = t_temp, .content = c_temp };
}

pub fn searchNotes(owner_id: i32, query_text: []const u8) ![]Note {
    const pattern = try std.fmt.allocPrint(allocator, "%{s}%", .{query_text});
    defer allocator.free(pattern);

    var query = db.pool.query("SELECT id, owner_id, title, content FROM notes WHERE owner_id = $1 AND (title ILIKE $2 OR content ILIKE $2) LIMIT 5", .{ owner_id, pattern }) catch |err| {
        std.log.err("Failed to query database: {}", .{err});
        return err;
    };
    defer query.deinit();

    var notes = std.ArrayList(Note).init(allocator);
    defer notes.deinit();

    while (try query.next()) |row| {
        const id = row.get(i32, 0);
        const oi = row.get(i32, 1);
        const title = row.get([]const u8, 2);
        const content = row.get([]const u8, 3);

        const bufLen = try std.base64.standard_no_pad.Decoder.calcSizeUpperBound(content.len);
        const buf = try allocator.alloc(u8, bufLen);
        _ = try std.base64.standard_no_pad.Decoder.decode(buf, content);

        const id_temp = try allocator.dupe(i32, &[_]i32{id});
        const oi_temp = try allocator.dupe(i32, &[_]i32{oi});
        const title_temp = try allocator.dupe(u8, title);
        const content_temp = try allocator.dupe(u8, buf);

        try notes.append(Note{
            .id = id_temp[0],
            .owner_id = oi_temp[0],
            .title = title_temp,
            .content = content_temp,
        });
    }

    return notes.toOwnedSlice();
}

pub fn getAllNotes(owner_id: i32) ![]Note {
    var query = db.pool.query("SELECT id, owner_id, title, content FROM notes WHERE owner_id = $1", .{owner_id}) catch |err| {
        std.log.err("Failed to query database: {}", .{err});
        return err;
    };
    defer query.deinit();

    var notes = std.ArrayList(Note).init(allocator);
    defer notes.deinit();

    while (try query.next()) |row| {
        const id = row.get(i32, 0);
        const oi = row.get(i32, 1);
        const title = row.get([]const u8, 2);
        const content = row.get([]const u8, 3);

        const bufLen = try std.base64.standard_no_pad.Decoder.calcSizeUpperBound(content.len);
        const buf = try allocator.alloc(u8, bufLen);
        _ = try std.base64.standard_no_pad.Decoder.decode(buf, content);

        const id_temp = try allocator.dupe(i32, &[_]i32{id});
        const oi_temp = try allocator.dupe(i32, &[_]i32{oi});
        const title_temp = try allocator.dupe(u8, title);
        const content_temp = try allocator.dupe(u8, buf);

        try notes.append(Note{
            .id = id_temp[0],
            .owner_id = oi_temp[0],
            .title = title_temp,
            .content = content_temp,
        });
    }

    return notes.toOwnedSlice();
}
