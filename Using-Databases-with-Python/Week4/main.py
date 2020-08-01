import sqlite3
import json

conn = sqlite3.connect("rosterdb.sqlite")
cur = conn.cursor()

# Make fresh tables using executescript
cur.executescript(
    """
DROP TABLE IF EXISTS User;
DROP TABLE IF EXISTS Member;
DROP TABLE IF EXISTS Course;

CREATE TABLE User (
    id     INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
    name   TEXT UNIQUE
);

CREATE TABLE Course (
    id     INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
    title  TEXT UNIQUE
);

CREATE TABLE Member (
    user_id     INTEGER,
    course_id   INTEGER,
    role        INTEGER,
    PRIMARY KEY (user_id, course_id)
);
    """
)

fname = "roster_data.json"
file_data = open(fname).read()
json_data = json.loads(file_data)

for entry in json_data:
    name = entry[0]
    title = entry[1]
    role = entry[2]

    print((name, title))

    cur.execute("INSERT OR IGNORE INTO User ( name ) VALUES( ? )", (name,))
    cur.execute("SELECT id FROM User WHERE name = ?", (name,))
    user_id = cur.fetchone()[0]

    cur.execute("INSERT OR IGNORE INTO Course ( title ) VALUES( ? )", (title,))
    cur.execute("SELECT id FROM Course WHERE title = ?", (title,))
    course_id = cur.fetchone()[0]

    cur.execute(
        "INSERT OR REPLACE INTO Member ( user_id, course_id, role ) VALUES ( ?, ?, ? )",
        (user_id, course_id, role),
    )
    conn.commit()

