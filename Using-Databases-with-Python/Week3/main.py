import sqlite3
import xml.etree.ElementTree as ET

conn = sqlite3.connect("trackdb.sqlite")
cur = conn.cursor()

# Make some fresh tables using executescript()
cur.executescript(
    """
DROP TABLE IF EXISTS Artist;
DROP TABLE IF EXISTS Genre;
DROP TABLE IF EXISTS Album;
DROP TABLE IF EXISTS Track;

CREATE TABLE Artist (
    id  INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
    name    TEXT UNIQUE
);

CREATE TABLE Genre (
    id  INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
    name    TEXT UNIQUE
);

CREATE TABLE Album (
    id  INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
    artist_id  INTEGER,
    title   TEXT UNIQUE
);

CREATE TABLE Track (
    id  INTEGER NOT NULL PRIMARY KEY 
        AUTOINCREMENT UNIQUE,
    title TEXT  UNIQUE,
    album_id  INTEGER,
    genre_id  INTEGER,
    len INTEGER, rating INTEGER, count INTEGER
);
    """
)


def lookup(entry, key):
    found = False
    for child in entry:
        if found:
            return child.text
        if child.tag == "key" and child.text == key:
            found = True
    return None


fname = "Library.xml"
stuff = ET.parse(fname)
records = stuff.findall("dict/dict/dict")
print("Dictionary Count: ", len(records))

for record in records:
    if lookup(record, "Track ID") is None:
        continue

    artist = lookup(record, "Artist")
    genre = lookup(record, "Genre")
    album = lookup(record, "Album")
    title = lookup(record, "Name")
    length = lookup(record, "Total Time")
    rating = lookup(record, "Rating")
    count = lookup(record, "Play Count")

    if artist is None or genre is None or album is None or title is None:
        continue

    print(artist, genre, album, title, length, rating, count)

    cur.execute("INSERT OR IGNORE INTO Artist ( name ) VALUES( ? )", (artist,))
    cur.execute("SELECT id FROM Artist WHERE name = ? ", (artist,))
    artist_id = cur.fetchone()[0]

    cur.execute("INSERT OR IGNORE INTO Genre ( name ) VALUES( ? )", (genre,))
    cur.execute("SELECT id FROM Genre WHERE name = ? ", (genre,))
    genre_id = cur.fetchone()[0]

    cur.execute(
        "INSERT OR IGNORE INTO Album ( artist_id, title ) VALUES( ?, ? )",
        (artist_id, album),
    )
    cur.execute("SELECT id FROM Album WHERE title = ? ", (album,))
    album_id = cur.fetchone()[0]

    cur.execute(
        """
                    INSERT OR REPLACE INTO Track ( title, album_id, genre_id, len, rating, count )
                    VALUES( ?, ?, ?, ?, ?, ?)
                """,
        (title, album_id, genre_id, length, rating, count),
    )
    conn.commit()

