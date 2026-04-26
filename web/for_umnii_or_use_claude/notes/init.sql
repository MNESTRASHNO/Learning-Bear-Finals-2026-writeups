CREATE TABLE users (
  id SERIAL PRIMARY KEY,
  username VARCHAR(64) UNIQUE,
  password VARCHAR(64)
);

CREATE TABLE notes (
  id SERIAL PRIMARY KEY,
  owner_id INTEGER,
  title VARCHAR(128),
  content TEXT,

  FOREIGN KEY (owner_id) REFERENCES users(id)
);

CREATE TABLE sessions (
  user_id INTEGER,
  token TEXT,
  FOREIGN KEY (user_id) REFERENCES users(id)
);
